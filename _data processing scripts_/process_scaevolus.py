# this script was used to process data I got from u/scaevolus

from datetime import datetime
import pytz

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

file=open("230728_pixelhistory-1")
line=file.readline()
i=0;count=0

file_out=open("scaevolus_data.csv",'w')


while line:
    data=line.split(" ")
    if len(data)==6 or len(data)==7:
        try:
            canvas, x, y=int(data[0]),int(data[1]),int(data[2])
            x=x+(canvas%3)*1000
            if canvas>2:
                y+=1000
            x-=1500
            y-=1000
            if len(data)==7:
                date=data[4]
                username=data[6]
            else:
                date=data[3]
                username=data[5]
            
            date=datetime.fromtimestamp(int(date)//1000,tz=pytz.UTC).strftime('%Y-%m-%d %H:%M:%S')
            
            file_out.write(f"{date},{x},{y},{username}")
        except:
           print(data)

    if count > 100000:
       print(f"{date},{x},{y},{username}")
       count=0
       eeta(i/64000000)
    line=file.readline()
    i+=1;count+=1

file_out.close()