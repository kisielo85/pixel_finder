import sys
import os
import subprocess
import string
import random

def random_string(size=30):
    return ''.join(random.choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(size))

def pass_save(p):
    file = open("passwords.txt", "wt")
    for i in p:
        file.write(i+".")
    file.close()

def gen_pass():
    passwords = [random_string(),random_string(),random_string()]
    pass_save(passwords)
    return passwords

def replace_passwords(old_pass,new_pass):
    def replace_string(old,new,filename):
        #print("open:",filename,"  replace",old,"with",new)
        file = open(filename, "rt")
        data = file.read()
        data = data.replace(old, new)
        file.close()
        file = open(filename, "wt")
        file.write(data)
        file.close()
    
    replace_string(old_pass[0],new_pass[0],"finder.py")
    replace_string(old_pass[0],new_pass[0],"_website_/ip_set.php")

    for i in ["ip_set.php","result.php","raw_result.php","index.php"]:
        replace_string(old_pass[1],new_pass[1],"_website_/"+i)
        replace_string(old_pass[2],new_pass[2],"_website_/"+i)

def replace_line(search,new,filename):
    file = open(filename, "r")
    data=file.readlines()
    file.close()
    for i in range(len(data)):
        if search in data[i]:
            data[i]=new+"\n"
            break
    file = open(filename, "wt")
    for i in data:
        file.write(i)
    file.close()

print("\n\n -- Secuirity -----")
if os.path.isfile("passwords.txt"):
    file = open('passwords.txt', 'r')
    old_passwords = file.readlines()[0][:-1].split('.')
    file.close()

    x=input("passwords already generated, make new ones? (Y/N): ")
    if x.lower()=="y":
        passwords=gen_pass()
        replace_passwords(old_passwords,passwords)
        print("passwords replaced, you may now upload website files")
    elif x.lower()=="default":
        replace_passwords(old_passwords,["pass_A","pass_B","pass_C"])
        print("default pass brr")
        os.remove("passwords.txt")
        exit()#[shrek's voice] what are you doing in MY COOOOOOOOODE
else:
    passwords=gen_pass()
    replace_passwords(["pass_A","pass_B","pass_C"],passwords)
    print("passwords created, you may now upload website files")
    
print("\n\n -- Software -----")
r=input("install requirements?  (Y/N): ")
if r.lower()=="y":
    subprocess.check_call([sys.executable, "-m", "pip", "install", "-r", "requirements.txt"])

print("\n\n -- Connections -----")
for i in [["database user:\n","db_user=\""],["database password:\n","db_pass=\""],["open port (for finder.py):\n","port=\""],["website example - \"http://kisielo85.cba.pl/place2022\":\n","website=\""]]:
    x=input(i[0])
    replace_line(i[1],i[1]+x+'"',"finder.py")


