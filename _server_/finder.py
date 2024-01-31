from genericpath import isdir
import mysql.connector
from datetime import datetime, timedelta
import time
import os
from flask import Flask, request, jsonify
from threading import Thread
import json

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

# clearing console and saving traffic
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

# find hash using username
def get_hash(nick,year):
  db = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
  cursor = db.cursor()

  if year=='17':
    query=f"SELECT hash FROM 2017_users WHERE username='{nick}'"
    cursor.execute(query)
    res=cursor.fetchone()
    if not res: return (False,False)
    return (res[0],False)
  
  elif year=='22':
    cursor.execute(f"select date, x, y from 2022_scraped where username='{nick}' LIMIT 10")
    res=cursor.fetchall()
    if res==[]: return (False,False)

    query="SELECT hash, count(hash) as repeated FROM ("
    for r in res:
      date=str(r[0])
      query+=f"SELECT hash FROM 2022_official WHERE date >='{date}' AND date < DATE_ADD('{date}', INTERVAL 10 SECOND) and ( x={r[1]} or x={r[1]+1000} ) and ( y={r[2]} or y={r[2]+1000} ) UNION ALL "
    query=query[:-10]+") as subrerer group by hash order by repeated desc LIMIT 1"

    cursor.execute(query)
    res2=cursor.fetchone()
    db.close()
    if not res2: return (False,False)
    return (res2[0],f"{res2[1]}/{len(res)}")
  
  
  elif year=='23':
    cursor.execute(f"select date, x, y from 2023_scraped where username='{nick}' LIMIT 10")
    res=cursor.fetchall()
    if res==[]: return (False,False)

    query="SELECT hash, count(hash) as repeated FROM ("
    for r in res:
      date=str(r[0])
      query+=f"SELECT hash FROM 2023_official WHERE date >='{date}' AND date < DATE_ADD('{date}', INTERVAL 10 SECOND) and x={r[1]} and y={r[2]} UNION ALL "
    query=query[:-10]+") as subrerer group by hash order by repeated desc LIMIT 1"

    cursor.execute(query)
    res2=cursor.fetchone()
    db.close()
    if not res2: return (False,False)
    return (res2[0],f"{res2[1]}/{len(res)}")
      

  else: return (False,False)

# returns placed pixels data
ENDGAME={'22':'2022-04-04 22:47:40','23':'2023-07-25 19:44:00'}
def get_pixels(nick,year):
  
  hash,machedPoints=get_hash(nick,year)
  print("mached:",machedPoints)
  if not hash: return False

  out={
    'hash':hash,
    'pixels':[]
  }

  db = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
  cursor = db.cursor()

  if year=='17':
    color_table=['#FFFFFF','#E4E4E4','#888888','#222222','#FFA7D1','#E50000','#E59500','#A06A42','#E5D900','#94E044','#02BE01','#00E5F0','#0083C7','#0000EA','#E04AFF','#820080']
    query=f"SELECT date, color, x, y, first_placer, final_canvas FROM 2017_official WHERE hash='{hash}'"
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
  
  elif year in ['22','23']:
    cursor.execute(f"""SELECT date, color, dt.x, dt.y,
    CASE WHEN tr.first_placer=hash AND ROW_NUMBER() OVER(PARTITION BY dt.x, dt.y ORDER BY date desc)=1 THEN TRUE ELSE FALSE END AS first_placer,
    CASE WHEN tr.final_canvas=hash AND ROW_NUMBER() OVER(PARTITION BY dt.x, dt.y ORDER BY date)=1 THEN TRUE ELSE FALSE END AS final_canvas,
    CASE WHEN date > '{ENDGAME[year]}' THEN TRUE ELSE FALSE END AS endgame
    FROM 20{year}_official dt
    LEFT JOIN (
      SELECT * FROM 20{year}_trophy WHERE
      first_placer='{hash}' OR
      final_canvas='{hash}'
    ) as tr
    ON tr.x=dt.x AND tr.y=dt.y
    WHERE hash = '{hash}' order by date;""")

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
  
  return False

# find nick using hash
def get_nick(hash, year):
  if year!='23': return

  db = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
  src_cursor = db.cursor()

  db2 = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
  search_cursor = db2.cursor()

  src_cursor.execute(f"select date, x, y from 2023_official where hash='{hash}'")
  src_res=src_cursor.fetchmany(5)

  matched=0
  src_found=0
  best=False
  maybe={}
  while src_res and matched<10:
    src_found+=len(src_res)

    query="SELECT username, count(username) as repeated FROM("
    for r in src_res:
      date=str(r[0])
      query+=f"SELECT username FROM 2023_scraped WHERE date > DATE_SUB('{date}', INTERVAL 10 SECOND) AND date <= '{date}' and x={r[1]} and y={r[2]} UNION ALL "
    query=query[:-10]+") as subrerer group by username order by repeated desc"

    search_cursor.execute(query)
    search_res=search_cursor.fetchall()

    for r in search_res:
      if not r[0] in maybe:
        maybe[r[0]]=r[1]
      else:
        maybe[r[0]]+=r[1]
    
    if maybe:
      best = max(maybe, key=lambda x: maybe[x])
      matched=maybe[best]
    
    src_res=src_cursor.fetchmany(5)
  
  return f"{matched}/{src_found}",best

@app.route('/')
@app.route('/find', methods=['GET'])
def result():
  nick = request.args.get("nick")
  year = request.args.get("year")
  p=get_pixels(nick,year)
  if not p: return {'error':'not_found'}
  if year=="17": traffic[0]+=1
  elif year=="22": traffic[1]+=1
  elif year=="23": traffic[2]+=1
  print("traffic:",traffic)
  response = jsonify(p)
  response.headers.add('Access-Control-Allow-Origin', '*')
  return response

@app.route('/traffic')
def stats():
  file=open("trafficc.txt","r")
  return file.read()

if __name__ == "__main__":
  c = Thread(target=cls)
  c.daemon=True
  c.start()
  app.run(debug=False,host="0.0.0.0",port=int(2139))
