function hslaToHex(h, s, l, o) {
    o=o*255/100
    o=o.toString(16).substring(0, 2)
    l /= 100;
    const a = s * Math.min(l, 1 - l) / 100;
    const f = n => {
        const k = (n + h / 30) % 12;
        const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
        return Math.round(255 * color).toString(16).padStart(2, '0');
    };
    return `#${f(0)}${f(8)}${f(4)}`+o;
}

// getting link to avatar from reddit
fetch(`https://api.reddit.com/user/${nick}/about`)
.then((response) => response.json())
.then((res_data) => {
    pfp_link=res_data.data.icon_img
    pfp_link=pfp_link.slice(0,pfp_link.search('\\?'))

    // getting base64
    const data = new URLSearchParams();
    data.append('link', pfp_link);

    fetch('http://kisielo85.cba.pl/utils/link_to_base64.php', {method: 'POST',body: data})
    .then((response) => response.text())
    .then((res_data) => {
        document.getElementById('pfp').src="data:image/png;base64,"+res_data
    })
    .catch((error) => {
        document.getElementById('pfp').src="img/default_avatar.png"
    })

})
.catch((error) => {
    document.getElementById('pfp').src='img/default_avatar.png'
});



function savepng(){
    var canvas = document.getElementById('output')
    ctx = canvas.getContext('2d');
    template = document.getElementById("tmpl")
    ctx.drawImage(template, 0, 0);

    //avatar
    profile = document.getElementById("pfp")
    if (profile.complete && profile.naturalHeight !== 0) {
        ctx.drawImage(profile, 2175+offset_gui, 100, 450, 450);
    }

    //nickname
    ctx.font = "bold "+font+"px Arial";
    ctx.fillStyle = "#cfcfcf";
    ctx.textAlign = "center";
    ctx.fillText("u/"+nick, 2400+offset_gui, 660);

    //placed pixels txt
    ctx.font = "bold 60px Arial";
    ctx.fillStyle = "#cfcfcf";
    ctx.textAlign = "left";
    ctx.fillText("placed pixels: "+(data.pixels.length), 2100+offset_gui, 800);

    //placed pixels circle
    ctx.beginPath();
    ctx.arc(2070+offset_gui,783,12,0,2*Math.PI);
    ctx.lineWidth = 5;
    c=hslaToHex(clr[3][0],100,clr[3][1],clr[3][2])
    ctx.strokeStyle=c;
    ctx.stroke();
    
    //trophies
    t=0
    for (i=0; i<3; i++){
        if (trophy[i]!=0){
            //text
            ctx.fillText(dt[i], 2100+offset_gui, 900+t*100);

            //circles
            ctx.beginPath();
            ctx.arc(2070+offset_gui,883+t*100,12,0,2*Math.PI);
            ctx.lineWidth = 5;
            c=hslaToHex(clr[i][0],100,clr[i][1],clr[i][2])
            ctx.strokeStyle=c;
            ctx.stroke();

            t+=1
        }
    }
    
    //pixel circles
    const layers=[3,0,1,2]
    for (z of layers){
        for (d of data.pixels){

            //checking layer
            e=3
            for (i of d.trophy) e=i
            if (e!=z) continue

            ctx.beginPath();

            x=parseInt(d.x)+offset_x
            y=parseInt(d.y)+offset_y
            if (year==17){ x*=2; y*=2 }

            ctx.arc(x+0.5,y+0.5,12,0,2*Math.PI);
            ctx.lineWidth = 2;
            c=hslaToHex(clr[e][0],100,clr[e][1],clr[e][2])
            ctx.strokeStyle=c;
            ctx.stroke();
            
        }
    }

    //pixel highlights
    for (d of data.pixels){
        x=parseInt(d.x)+offset_x
        y=parseInt(d.y)+offset_y
        if (year==17){ x*=2; y*=2 }

        ctx.fillStyle = d.color+"40";
        ctx.fillRect( x-1, y-1, 3, 3 );
        ctx.fillStyle = d.color;
        ctx.fillRect( x, y, 1, 1 );
    }
    
    //saving png
    var image = canvas.toDataURL();
    var aDownloadLink = document.createElement('a');
    aDownloadLink.download = `canvas${year}_${nick}.png`;
    aDownloadLink.href = image;
    aDownloadLink.click();
}
const names=["_first_placer_","_final_canvas_","_endgame_","_"]
function saveRawData() {
    var blob = new Blob([data_gen()], { type: "text/plain;charset=utf-8" });
    var aDownloadLink = document.createElement('a');
    aDownloadLink.download = `data${year}${names[onnly]}${nick}.txt`;
    aDownloadLink.href = window.URL.createObjectURL(blob);
    aDownloadLink.click();
}