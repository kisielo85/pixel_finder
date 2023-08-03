# used for processing 2022 u/opl_ data
# from json to csv
import os
import json
from datetime import datetime
import pytz

folder_path="C:\\Users\\Pszemek\\Desktop\\place2022-opl-raw\\processing"
files = os.listdir(folder_path)

file_out=open("opl2022_data.csv",'w')
filecount=len(files)
count=0
for file_name in files:
    count+=1
    print(f"{file_name} - {count}/{filecount}")

    file_path = os.path.join(folder_path, file_name)
    file=open(file_path, 'r')

    line=file.readline().strip()
    while line:
        pos=line.find('{\\"data')
        if pos == -1:
            line=file.readline().strip()
            continue
        data=json.loads(json.loads(line[pos-1:]))

        for d in data['data']:

            try:
                dd=data['data'][d]['data'][0]['data']
                date=datetime.fromtimestamp(int(dd['lastModifiedTimestamp'])//1000,tz=pytz.UTC).strftime('%Y-%m-%d %H:%M:%S')
                username=dd['userInfo']['username']
            except:
                print("error:",data['data'][d])
                continue

            posx=d.find("x")
            x=int(d[1:posx])

            posc=d.find("c")
            if posc != -1:
                y=int(d[posx+1:posc])
                c=int(d[posc+1:])

                if c==1 or c==3: x+=1000
                if c > 1: y+=1000
            else:
                y=d[posx+1:]
            print("|",username,"|")
            print('\\r' in username)
            file_out.write(f"{date},{x},{y},{username}\n")

        line = file.readline().strip()

file_out.close()