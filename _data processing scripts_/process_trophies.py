import mysql.connector
import datetime
import json

config=json.load(open("config.json", 'r'))
db_user=config['db_user']
db_pass=config["db_pass"]
db_host=config["db_host"]
db_name=config["db_name"]

#canvas size
canvas_x=2000
canvas_y=2000

#--------------------------
# ETA function - used for tracking progress
from time import time, sleep
eta=[]
def eeta(pr):
  nw=time()
  
  eta.append([nw,pr])
  if len(eta)>50: #it checks the time of x past fuction calls
    eta.pop(0)
  
  progress=pr-eta[0][1]
  if progress!=0:
    elapsed=nw-eta[0][0]
    time_left=int(elapsed*(1-pr)/progress)

    days = int(time_left / 86400)
    hours = int((time_left % 86400) / 3600)
    minutes = int((time_left % 3600) / 60)
    seconds = int(time_left % 60)

    print(round(pr * 100, 2), "%\tETA:  ", days, "d  ", hours, "h  ", minutes, "m  ", seconds, "s",sep='')
#--------------------------

print("loading array")
first_canvas = [[False for i in range(canvas_x)] for j in range(canvas_y)]
final_canvas = [[False for i in range(canvas_x)] for j in range(canvas_y)]
print("loaded")

db = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
cursor = db.cursor()

#cursor.execute("select date, x+1500, y+1000, hash from data23 where date < '2023-07-25 19:44:00';") #2023
cursor.execute("select date, x, y, hash from 2022_official where date < '2022-04-04 22:47:40';") #2022
row = cursor.fetchone()

count=0;count2=0
print("loop brr")
while row is not None:
    date, x,y,hash=row[0],row[1],row[2],row[3]

    if not first_canvas[y][x] or date < first_canvas[y][x][0]:
       first_canvas[y][x]=[date,hash]
    
    if not final_canvas[y][x] or date > final_canvas[y][x][0]:
        final_canvas[y][x]=[date,hash]

    count+=1
    if count>100000:
        count=0;count2+=100000
        #eeta(count2/132224375) #2023
        eeta(count2/160353085) #2022
        
    row = cursor.fetchone()

print("saving..")

file_trophies=open("2022_trophy.csv",'w')

eta=[]
count=0
for y in range(canvas_y):
    if y%50==0:
        eeta(y/canvas_y)
    
    for x in range(canvas_x):
        #file_trophies.write(f"{x-1500},{y-1000},") #2023
        file_trophies.write(f"{x},{y},") #2022

        if first_canvas[y][x]:
            file_trophies.write(first_canvas[y][x][1]+',')
        else:
            file_trophies.write("_,")

        if final_canvas[y][x]:
            file_trophies.write(final_canvas[y][x][1])
        else:
            file_trophies.write("_")
    
        file_trophies.write("\n")

file_trophies.close()






