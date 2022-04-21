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






def HashToNick(hash):
  mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  password="poopyhead",
  database="nick_finder"
  )
  mycursor = mydb.cursor()
  mycursor.execute("SELECT date,x,y FROM place_data where hash = '"+hash+"' LIMIT 10")
  myresult = mycursor.fetchall()
  nicks={"0":0}
  for r in myresult:
    print(r)
    d1=r[0]-timedelta(seconds=1)
    d2=r[0]+timedelta(seconds=3)
    x=str(r[1])[2:]
    y=str(r[2])[1:-1]
    q="SELECT nick FROM nick_data WHERE '"+str(d1)+"' <= date AND date <= '"+str(d2)+"'"
    print(q)
    mycursor.execute(q)
    res_h = mycursor.fetchall()
    print(res_h)
    for h in res_h:
      if h[0] in nicks:
        nicks[h[0]]+=1
      else:
        nicks[h[0]]=1
  maxx="0"
  for i in nicks:
    if nicks[i]>nicks[maxx]:
      maxx=i
    print(nicks)
  return maxx

print(HashToNick("kgZoJz//JpfXgowLxOhcQlFYOCm8m6upa6Rpltcc63K6Cz0vEWJF/RYmlsaXsIQEbXrwz+Il3BkD8XZVx7YMLQ=="))