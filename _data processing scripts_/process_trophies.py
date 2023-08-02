import mysql.connector
from PIL import Image
from datetime import datetime, timedelta
import pickle

db_user="k85"
db_pass="password"
db_host="localhost"
db_name="place2022"

#canvas size
canvas_x=3000
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


def save_progress(c,x):
    with open(f"progress_{x}.pkl", 'wb') as file:
        pickle.dump(c, file)

def load_progress(x):
    with open(f"progress_{x}.pkl", 'rb') as file:
        return pickle.load(file)

print("loading array")
first_canvas = [[False for i in range(canvas_x)] for j in range(canvas_y)]
final_canvas = [[False for i in range(canvas_x)] for j in range(canvas_y)]
#canvas=load_progress("00_final")
print("loaded")

db = mysql.connector.connect(host=db_host, user=db_user, password=db_pass, database=db_name)
cursor = db.cursor()

cursor.execute("select date, x+1500, y+1000, hash from data23 where date < '2023-07-25 19:44:00';")
row = cursor.fetchone()
print(row)
count=0;count2=0
print("loop brr")
while row is not None:
    date, x,y,hash=row[0],row[1],row[2],row[3]

    if not first_canvas[y][x] or date < first_canvas[y][x][0]:
       first_canvas[y][x]=[date,hash]
    
    if not final_canvas[y][x] or date > final_canvas[y][x][0]:
        final_canvas[y][x]=[date,hash]

    count+=1
    if count>500000:
        count=0;count2+=500000
        eeta(count2/132224375)
    row = cursor.fetchone()

print("saving..")
save_progress(final_canvas,"23_final")
save_progress(first_canvas,"23_first")

file_first=open("first_placer23.csv",'w')
file_final=open("final_canvas23.csv",'w')
file_trophies=open("23_trophies.csv",'w')

eta=[]
for y in range(canvas_y):
    eeta(y/canvas_y)
    for x in range(canvas_x):
        file_first.write(f"{x-1500},{y-1000},")
        file_final.write(f"{x-1500},{y-1000},")
        file_trophies.write(f"{x-1500},{y-1000},")

        if first_canvas[y][x]:
            file_first.write(first_canvas[y][x][1])
            file_trophies.write(first_canvas[y][x][1]+',')
        else:
            file_first.write("_")
            file_trophies.write("_,")

        if final_canvas[y][x]:
            file_final.write(final_canvas[y][x][1])
            file_trophies.write(final_canvas[y][x][1])
        else:
            file_final.write("_")
            file_trophies.write("_")
    
        file_final.write("\n")
        file_first.write("\n")
        file_trophies.write("\n")

file_first.close()
file_final.close()
file_trophies.close()






