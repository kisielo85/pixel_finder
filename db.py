from genericpath import exists, isdir
import mysql.connector
from datetime import datetime, timedelta
import time
import urllib.request, json 
import requests
from PIL import Image, ImageFont, ImageDraw 
import shutil
import os
from flask import Flask, render_template, url_for, request, redirect
from multiprocessing import Process

if not isdir("user_data"):
  os.mkdir("user_data")

if not isdir("results"):
  os.mkdir("results")

def nickToHash(nick):
  mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  password="poopyhead",
  database="nick_finder"
  )
  mycursor = mydb.cursor()
  mycursor.execute("SELECT * FROM nick_data where nick = '"+nick+"' LIMIT 15")
  myresult = mycursor.fetchall()
  hashes={"0":0}
  for r in myresult:
    d1=r[1]-timedelta(seconds=1)
    d2=r[1]+timedelta(seconds=3)
    x=str(r[2])
    y=str(r[3])
    q="SELECT hash FROM place_data WHERE '"+str(d1)+"' <= date AND date <= '"+str(d2)+"' AND x LIKE '%"+x+"%' AND y LIKE '%"+y+"%'"
    mycursor.execute(q)
    res_h = mycursor.fetchall()
    for h in res_h:
      if h[0] in hashes:
        hashes[h[0]]+=1
      else:
        hashes[h[0]]=1
  maxx="0"
  for i in hashes:
    if hashes[i]>hashes[maxx]:
      maxx=i
  return maxx

def hashToPixels(hash):
  mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  password="poopyhead",
  database="nick_finder"
  )
  mycursor = mydb.cursor()
  mycursor.execute("select x,y,date,color from place_data where hash='"+hash+"'")
  myresult = mycursor.fetchall()
  j=0
  for i in myresult:
    x=str(myresult[j][0])[1:]
    y=str(myresult[j][1])[:-1]
    c=myresult[j][3][1:]
    myresult[j]=(x,y,myresult[j][2],c)
    j+=1
  return myresult

def makePNG(user,pixels):
  got_avatar=False
  img_url=""
  for i in range(500):
    try:
      with urllib.request.urlopen("https://www.reddit.com/user/"+user+"/about.json") as url:
        data = json.loads(url.read().decode())
        img_url=data['data']['icon_img']
        find=img_url.find("?width=256")
        if(find!=-1):
            img_url=img_url[:find]
        break
    except:
        pass

  if img_url != "":
    filename = img_url.split("/")[-1]

    r = requests.get(img_url, stream = True)

    if r.status_code == 200:
      r.raw.decode_content = True
      
      with open(filename,'wb') as f:
        shutil.copyfileobj(r.raw, f)

      if os.path.exists("user_data/avatar_"+user+".png"):
        os.remove("user_data/avatar_"+user+".png")
      os.rename(filename,"user_data/avatar_"+user+".png")
      got_avatar=True
    else:
      print('no profile pic :c')
  else:
    print('no profile pic :c')

  img = Image.open('template.png')
  circle = Image.open('circle.png')
  img = img.convert("RGB")
  circle = circle.convert("RGBA")

  def to_date(x):
    return datetime.strptime(x, '%Y-%m-%d %H:%M:%S.%f')

  first_date=to_date('2022-05-01 00:00:00.000')
  last_date=to_date('2022-03-01 00:00:00.000')
  first_px=["0","0","0"]
  last_px=["0","0","0"]

  for i in pixels:
    date=i[2]
    
    x=int(i[0])
    y=int(i[1])
    if date>last_date:
        last_date=date
        last_px=[str(x),str(y),i[3]]
    if date<first_date:
        first_date=date
        first_px=[str(x),str(y),i[3]]
    img.putpixel((x,y),tuple(bytes.fromhex( str(i[3]) ) ))
    img.paste(circle,(x-12,y-12),circle)

  if got_avatar:
    avatar = Image.open("user_data/avatar_"+user+".png")
    avatar = avatar.convert("RGBA")
    avatar=avatar.resize((450,450))
    img.paste(avatar,(2175,100),avatar)

  draw = ImageDraw.Draw(img)
  text = "u/"+user
  tt=80
  font = ImageFont.truetype('Roboto-Bold.ttf', tt) 
  w, h = draw.textsize(text,font=font)
  while w>750:
    tt-=1
    font = ImageFont.truetype('Roboto-Bold.ttf', tt)
    w, h = draw.textsize(text,font=font)
  draw.text((2400-w/2, 600), text,font=font,align="center") 

  text = "placed pixels: "+str(len(pixels))+"\n\nfirst pixel: ("+first_px[0]+","+first_px[1]+")\n#"+first_px[2]+"\n\nlast pixel: ("+last_px[0]+","+last_px[1]+")\n#"+last_px[2]
  font = ImageFont.truetype('Roboto-Bold.ttf', 50)
  w, h = draw.textsize(text,font=font)
  draw.text((2400-w/2, 800), text,font=font,align="left")

  img.save("static/results/result_"+user+".png")
  print("saved: result_"+user+".png")

def makeErrorPNG(user):
  img = Image.open('error_template.png')
  draw = ImageDraw.Draw(img)
  text = "u/"+user
  tt=200
  font = ImageFont.truetype('Roboto-Bold.ttf', tt) 
  w, h = draw.textsize(text,font=font)
  while w>1000:
    tt-=1
    font = ImageFont.truetype('Roboto-Bold.ttf', tt)
    w, h = draw.textsize(text,font=font)
  draw.text((1000-w/2, 500), text,font=font,align="center") 
  img.save("static/results/result_"+user+".png")

def f(user):
  print("user:",user)
  h=nickToHash(user)
  if h=="0":
    print("hash not found")
    makeErrorPNG(user)
  else:
    pixels=hashToPixels(h)
    makePNG(user,pixels)
    del pixels
  del h
  time.sleep(120)
  dir="user_data/avatar_"+user+".png"
  if exists(dir):
    os.remove(dir)
  dir="static/results/result_"+user+".png"
  if exists(dir):
    os.remove(dir)
  print(user,"data removed")

def b(text):
  for ch in ['\\','/',':','*','?','"','<','>','|']:
    if ch in text:
      text = text.replace(ch,'')
  return text

def webExc(x):
  user = b(x["name"])
  p = Process(target=f, args=(user,))
  p.start()
  
def cls():
  while(1):
    os.system("cls")
    time.sleep(60)

actions=["tv-spotify","tv-on","tv-off","pc-on"]
actions.sort()

app = Flask(__name__)
@app.route('/')
@app.route('/home')

def home():
  return render_template("index.html")

@app.route('/',methods=['POST', 'GET'])
def result():
  output = request.form.to_dict()
  webExc(output)
  return redirect(request.path)

if __name__ == "__main__":
  c = Process(target=cls)
  c.start()
  app.run(debug=False,host="0.0.0.0",port=2137)
