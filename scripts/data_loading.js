const api_link="https://pixelfinderapi.153070065.xyz/api"


// 2023 canvas has different dimentions
offset_gui=0; offset_x=0; offset_y=0;
if (year==23){
    offset_x=1500; offset_y=1000; offset_gui=1000;
    root.style.setProperty('--offset_x', '1000px');
    document.getElementById('output').width=3800
}


function loadmsg(x){
    document.getElementById("msg_content").innerHTML=x
}
            
loadmsg("looking for: <strong>u/"+nick+"</strong>..<br><p>please stay on this site</p>")

repeated=0
data=""
const circles=document.getElementById("circles")

// once a valid result is found, loading stuff to html
function loadpx(){

    // placing circles, and counting trophies
    circles.innerHTML=""
    trophy=[0,0,0]
    for (d of data.pixels){
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

    // numbers on the right
    document.getElementById("data").innerHTML="placed pixels: "+(data.pixels.length)+"<br>"
    dt=["first placer: "+trophy[0],"final canvas: "+trophy[1],"endgame: "+trophy[2]]
    t=0
    for (i=0; i<3; i++){
        if (trophy[i]==0) continue
        
        image.innerHTML+="<div class='circle c"+i+"'style='border-width: 5px; margin-left: var(--offset_x); left: 2048px; top: "+(908+100*t)+"px;'></div>"
        document.getElementById("data").innerHTML+=dt[i]+"<br>"
        t+=1
    }

    document.getElementById("result").hidden=false
    document.getElementById("info").hidden=false
    document.getElementById("loading_msg").hidden=true

    // fixing nickname size
    font=80
    nick_txt=document.getElementById("nickname")
    while(nick_txt.scrollHeight>120 && font > 0){
        font-=1;
        nick_txt.style.setProperty('font-size',font+"px");
    }
}

// fetching data from pixel_finder API
fetch(`${api_link}?nick=${nick}&year=${year}`)
.then((response) => response.json())
.then((res_data) => {
    if (res_data.error){
        switch (res_data.error){
            case "not_found":
                loadmsg("<strong>u/"+nick+"</strong><br>user not found :c<br>")
                break
            default:
                loadmsg(`something went wrong :c<p>please come back later</p><p>${res_data.error}</p>`)
                break
        }
        return
    }
    data=res_data
    loadpx()
})
.catch((error) => {
    loadmsg(`pixel_finder API not responding :c<p>please come back later</p><p>${error}</p>`)
    console.log(error)
})