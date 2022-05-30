from genericpath import exists, isdir
from turtle import distance
import mysql.connector
from datetime import date, datetime, timedelta
import time
import requests
import shutil
import os
from flask import Flask, render_template, url_for, request, redirect
from multiprocessing import Process
import glob


dev=False

port=2137
db_user="finder"
db_pass=""
website="http://kisielo85.cba.pl/place2022"

if dev:
  port=2138
  website="http://localhost/nick_finder"
  db_user="root"
  db_pass="poopyhead"

def nickToHash(nick):
  mydb = mysql.connector.connect(
  host="localhost",
  user=db_user,
  password=db_pass,
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
  mydb.close()
  maxx="0"
  for i in hashes:
    if hashes[i]>hashes[maxx]:
      maxx=i
  return maxx

def to_date(x):
  return datetime.strptime(x, '%Y-%m-%d %H:%M:%S.%f')
end = to_date('2022-04-04 22:47:40.000')

def trophy(x,y,hash,dt):
  x=int(x)
  y=int(y)
  mydb = mysql.connector.connect(
  host="localhost",
  user=db_user,
  password=db_pass,
  database="nick_finder"
  )
  mycursor = mydb.cursor()
  mycursor.execute("select first_placer, final_canvas from trophy_pixels where id="+str(x*2000+y+1))
  myresult = mycursor.fetchall()
  mydb.close()
  i=0
  t=""
  for h in myresult[0]:
    if h==hash:
     t+=str(i)+","
    i+=1
  
  if dt>end:
    t+="2,"
  
  
  t="["+t[:-1]+"]"
  return t

def hashToPixels(hash):
  mydb = mysql.connector.connect(
  host="localhost",
  user=db_user,
  password=db_pass,
  database="nick_finder"
  )
  mycursor = mydb.cursor()
  mycursor.execute("select x,y,date,color from place_data where hash='"+hash+"' ORDER BY date")
  myresult = mycursor.fetchall()
  mydb.close()

  for j in range(len(myresult)):
    x=str(myresult[j][0])[1:]
    y=str(myresult[j][1])[:-1]
    c=myresult[j][3]
    
    dt=myresult[j][2]
    t=trophy(x,y,hash,dt)
    myresult[j]=(x,y,dt,c,t)
    j+=1
  return myresult

def f(user):
  print("user:",user)
  dir="static/results/data_"+user+".txt"
  h=nickToHash(user)
  file=open(dir,"w")
  if h=="0":
    file.write("_end_")
    file.close()
    print(user,"not found")
  else:
    file.write(h+".")
    file.close()

    pixels=hashToPixels(h)
    file=open(dir,"w")
    file.write(h+".")
    for i in pixels:
      x,y,d,c,t=i
      file.write(str(d)+";"+x+";"+y+";"+c+";"+t+".")
    file.write("_end_")
    file.close()
    del pixels
    print("saved: data_"+user+".txt")
  del h
  return
  time.sleep(120)
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
    if os.name == 'nt':
      os.system('cls')
    else:
      os.system('clear')
    
    files=glob.glob("static/results/*.txt")
    count=0
    for file in files:
      if datetime.fromtimestamp(os.path.getmtime(file))+timedelta(minutes=10)<datetime.now():
        count+=1
        os.remove(file)
    print("removed",count,"files")
    file=open("static/stats.txt","a")
    file.write(str(datetime.now().strftime("%Y-%m-%d %H:%M"))+"  "+str(count)+"\n")
    file.close()
    time.sleep(600)


def checkip():
  while(1):
    try:
      URL = website+"/ip_set.php"
      PARAMS = {'pass':"AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA","port":str(port)}
      requests.get(url = URL, params = PARAMS)
    except:
      print("server offline")
    time.sleep(30)

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
  ip = Process(target=checkip)
  ip.start()
  app.run(debug=False,host="0.0.0.0",port=port)
