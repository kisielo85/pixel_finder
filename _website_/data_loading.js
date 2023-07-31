const year=<?php echo $year?>;
offset_gui=0; offset_x=0; offset_y=0;
if (year==23){
    offset_x=1500; offset_y=1000; offset_gui=1000;
    root.style.setProperty('--offset_x', '1000px');
    document.getElementById('output').width=3800
}

function loadmsg(x){
    document.getElementById("loading_msg").innerHTML=x+"<p id='msg'><p><form action='index.php'><input type='hidden' name='year' value=<?php echo $year?>><input type='submit' value='return' /></form></div>"
}
loadmsg("looking for: <strong>u/"+nick+"</strong>..<br><p>please stay on this site</p>")

repeated=0
data=""
const circles=document.getElementById("circles")

function loadpx(){
    circles.innerHTML=""
    trophy=[0,0,0]

    //placing circles
    for (d of data.pixels){
        //console.log(x)
        clss="circle"
        for (t of d.trophy){
            clss+=" c"+t
            trophy[t]++
        }
        if (year==17)
            circles.innerHTML+=`<div class='${clss}' style='left: ${d.x*2-12}px; top: ${d.y*2-12}px;'></div>`
        else
            circles.innerHTML+=`<div class='${clss}' style='left: ${d.x-12+offset_x}px; top: ${d.y-12+offset_y}px;'></div>`
    }

    document.getElementById("raw_data_hash").innerHTML=data.hash
    raw_data_write()

    //numbers on the right
    document.getElementById("data").innerHTML="placed pixels: "+(data.pixels.length)+"<br>"

    dt=["first placer: "+trophy[0],"final canvas: "+trophy[1],"endgame: "+trophy[2]]
    t=0
    for (i=0; i<3; i++){
        if (trophy[i]!=0){
            image.innerHTML+="<div class='circle c"+i+"'style='border-width: 5px; margin-left: var(--offset_x); left: 2048px; top: "+(908+100*t)+"px;'></div>"
            document.getElementById("data").innerHTML+=dt[i]+"<br>"
            t+=1
        }
        
        document.getElementById("result").hidden=false
        document.getElementById("footer").hidden=false
        document.getElementById("loading_msg").hidden=true
    }
}

function checkData() { //getting stuff from raw_result.php
    out=document.getElementById('out')
    data=document.getElementById('ifr').contentWindow.document.body.innerHTML;
    refresh=true
    switch (data){
        case "":
        case "error":
            repeated+=1
            if (repeated>2){
                loadmsg("database not responding :c<p>please come back later</p>")
                console.log("request nr."+repeated)
            }
            break
        case "not_found":
            loadmsg("<div class='center_div'><strong>u/"+nick+"</strong><br>user not found :c<br>")
            refresh=false
            break
        default:
            data = JSON.parse(data)
            refresh=false
            loadpx()
            /*data=data.split(".")
            if (data[data.length-1]=="_end_"){
                refresh=false
                loadpx()
            }
            else repeated+=1*/
    }
    setTimeout(function() { if (refresh) document.getElementById('ifr').contentWindow.location.reload(); }, 1000);
}
checkData();
var myIframe = document.getElementById('ifr');
    myIframe.addEventListener("load", function() {
    checkData();
});