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


function savepng(){
    var canvas = document.getElementById('output')
    ctx = canvas.getContext('2d');
    template = document.getElementById("tmpl")
    ctx.drawImage(template, 0, 0);

    profile = document.getElementById("pfp")//avatar
    if (profile.complete && profile.naturalHeight !== 0) {
        ctx.drawImage(profile, 2175+offset_gui, 100, 450, 450);
    }

    ctx.font = "bold "+font+"px Arial";//nickname
    ctx.fillStyle = "#cfcfcf";
    ctx.textAlign = "center";
    ctx.fillText("u/"+nick, 2400+offset_gui, 660);

    ctx.font = "bold 60px Arial";//first/last px
    ctx.fillStyle = "#cfcfcf";
    ctx.textAlign = "left";
    ctx.fillText("placed pixels: "+(data.pixels.length), 2100+offset_gui, 800);

    ctx.beginPath();//default circle
    ctx.arc(2070+offset_gui,783,12,0,2*Math.PI);
    ctx.lineWidth = 5;
    c=hslaToHex(clr[3][0],100,clr[3][1],clr[3][2])
    ctx.strokeStyle=c;
    ctx.stroke();

    t=0
    for (i=0; i<3; i++){//trophies
        if (trophy[i]!=0){
            ctx.fillText(dt[i], 2100+offset_gui, 900+t*100); //text

            ctx.beginPath();
            ctx.arc(2070+offset_gui,883+t*100,12,0,2*Math.PI); //circles
            ctx.lineWidth = 5;
            c=hslaToHex(clr[i][0],100,clr[i][1],clr[i][2])
            ctx.strokeStyle=c;
            ctx.stroke();
            t+=1
        }
    }


    const layers=[3,0,1,2]
    for (z of layers){
        for (d of data.pixels){
            e=3
            for (i of d.trophy){
                e=i
            }
            if (e==z){
                ctx.beginPath();
                if (year==17)
                    ctx.arc(parseInt(d.x)*2,parseInt(d.y)*2,12,0,2*Math.PI);
                else
                    ctx.arc(parseInt(d.x)+offset_x,parseInt(d.y)+offset_y,12,0,2*Math.PI);
                ctx.lineWidth = 2;
                c=hslaToHex(clr[e][0],100,clr[e][1],clr[e][2])
                ctx.strokeStyle=c;
                ctx.stroke();
            }
            
        }
    }
    
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