from genericpath import isdir
import mysql.connector
from datetime import datetime, timedelta
import time
import os
from flask import Flask, request
from threading import Thread
import json


dev=False

config=json.load(open("config.json", 'r'))
db_user=config['db_user']
db_pass=config["db_pass"]
db_host=config["db_host"]
db_name=config["db_name"]

def b(text):
  for ch in ['\\','/',':','*','?','"','<','>','|']:
    if ch in text:
      text = text.replace(ch,'')
  return text


def next_hour(dt):
  dt += timedelta(hours=1)
  return dt.replace(minute=0, second=0, microsecond=0)

traffic=[0,0,0]
def cls():
  nextdate=next_hour(datetime.now())
  global traffic
  while(1):
    now=datetime.now()
    wait_time=(nextdate-now).total_seconds()
    print(nextdate)

    print("wait for:",nextdate," - ",wait_time,"s")
    time.sleep(wait_time)

    file=open('trafficc.txt','a')
    nextdate=next_hour(nextdate)
    save=nextdate.strftime("%m.%d %H:%M")+","+str(traffic[0])+","+str(traffic[1])+","+str(traffic[2])+";"
    print("save:",save)
    file.write(save)
    file.close()
    

    traffic=[0,0,0]

app = Flask(__name__)

def get_hash(nick,year):
  db = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
  cursor = db.cursor()

  if year=='17':
    query=f"SELECT hash FROM users17 WHERE username='{nick}'"
    cursor.execute(query)
    res=cursor.fetchone()
    if not res: return (False,False)
    return (False,res[0])
  
  elif year=='22':
    query=f"SELECT id, hash FROM users22 WHERE username='{nick}'"
    cursor.execute(query)
    res=cursor.fetchone()
    if not res: return (False,False)
    return (res[0],res[1])
  
  elif year=='23':
    query=f"select date, x, y from data23_scraped where username='{nick}' LIMIT 10"
    cursor.execute(query)
    res=cursor.fetchall()
    if res==[]: return (False,False)

    query="SELECT hash, count(hash) as repeated FROM ("
    for r in res:
      date=str(r[0])
      query+=f"SELECT hash FROM data23 WHERE date >='{date}' AND date < DATE_ADD('{date}', INTERVAL 10 SECOND) and x='\"{r[1]}' and y='{r[2]}\"' UNION ALL "
    query=query[:-10]+") as subrerer group by hash order by repeated desc LIMIT 1"

    cursor.execute(query)
    res2=cursor.fetchone()
    db.close()
    if not res2: return (False,False)
    return (False,res2[0])
      

  else: return (False,False)


endgame23=datetime(2023,7,25,19,44)
def get_pixels(nick,year):
  
  id,hash=get_hash(nick,year)
  if not hash: return False

  out={
    'hash':hash,
    'pixels':[]
  }

  db = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
  cursor = db.cursor()

  if year=='17':
    color_table=['#FFFFFF','#E4E4E4','#888888','#222222','#FFA7D1','#E50000','#E59500','#A06A42','#E5D900','#94E044','#02BE01','#00E5F0','#0083C7','#0000EA','#E04AFF','#820080']
    query=f"SELECT date, color, x, y, first_placer, final_canvas FROM data17 WHERE hash='{hash}'"
    cursor.execute(query)
    res=cursor.fetchall()

    for r in res:
      tr=[]
      if r[4]: tr.append(0)
      if r[5]: tr.append(1)
      out['pixels'].append({
        'date':str(r[0]),
        'color':color_table[r[1]],
        'x':r[2],
        'y':r[3],
        'trophy':tr}
        )
    return json.dumps(out)


  elif year=='22':
    query=f"SELECT date, color, x, y, first_placer, final_canvas, endgame FROM data22 WHERE user_id='{id}'"
    cursor.execute(query)
    res=cursor.fetchall()
    for r in res:
      tr=[]
      if r[4]: tr.append(0)
      if r[5]: tr.append(1)
      if r[6]: tr.append(2)
      out['pixels'].append({
        'date':str(r[0]),
        'color':'#'+r[1],
        'x':r[2],
        'y':r[3],
        'trophy':tr}
        )
    return json.dumps(out)
  
  elif year=='23':
    cursor.execute(f"""SELECT date, color, data23.x, data23.y,
    CASE WHEN tr.first_placer=hash AND ROW_NUMBER() OVER(PARTITION BY data23.x, data23.y ORDER BY date desc)=1 THEN TRUE ELSE FALSE END AS first_placer,
    CASE WHEN tr.final_canvas=hash AND ROW_NUMBER() OVER(PARTITION BY data23.x, data23.y ORDER BY date)=1 THEN TRUE ELSE FALSE END AS final_canvas,
    CASE WHEN date > '2023-07-25 19:44:00' THEN TRUE ELSE FALSE END AS endgame
    FROM data23
    LEFT JOIN (
      SELECT * FROM trophies_23 where
        first_placer='{hash}' or
        final_canvas='{hash}'
    ) as tr
    ON CONCAT('"',tr.x)=data23.x and CONCAT(tr.y,'"')=data23.y
    WHERE hash = '{hash}';""")

    res=cursor.fetchall()
    for r in res:
      tr=[]
      if r[4]: tr.append(0)
      if r[5]: tr.append(1)
      if r[6]: tr.append(2)
      out['pixels'].append({
        'date':str(r[0]),
        'color':'#'+r[1],
        'x':int(r[2][1:]),
        'y':int(r[3][:-1]),
        'trophy':tr}
      )
    
    return json.dumps(out)
  
  return False


@app.route('/')
@app.route('/find/<string:nick>/<string:year>', methods=['GET'])
def result(nick,year):
  p=get_pixels(nick,year)
  if not p: return "not_found"
  if year=="17": traffic[0]+=1
  elif year=="22": traffic[1]+=1
  elif year=="23": traffic[2]+=1
  print("traffic:",traffic)
  return p

@app.route('/traffic')
def stats():
  file=open("trafficc.txt","r")
  return file.read()

if __name__ == "__main__":
  c = Thread(target=cls)
  c.daemon=True
  c.start()
  app.run(debug=False,host="0.0.0.0",port=int(2139))
