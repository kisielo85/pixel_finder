import os


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


folder_path="C:\\Users\\Pszemek\\Desktop\\data23"
files = os.listdir(folder_path)

file_out=open("2023_official.csv",'w')

filecount=len(files)

count=0; count2=0
for file_name in files:
    print("file:",file_name)

    file_path = os.path.join(folder_path, file_name)
    file=open(file_path, 'r')

    line=file.readline().strip()
    while line:
        count+=1
        try:
            date,hash,x,y,color=line.split(",")
        except:
            #skipping the admin stuff
            print("skip:",line)
            line=file.readline().strip()
            continue

        r_date=date.rfind(".")
        if r_date==-1:
            r_date=date.rfind("UTC")-1
        
        date=date[:r_date]

        file_out.write(f"{date},{x[1:]},{y[:-1]},{color[1:]},{hash}\n")

        if count > 100000:
            count=0
            count2+=100000
            eeta(count2/160353085)
        line=file.readline().strip()

file_out.close()
        
