<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pixel_finder</title>
    <link rel="stylesheet" href="style.css?m=14">
</head>
<body>   
    <h1 id="js_check">please enable javascript in order for site to work</h1>
    <script>
        document.getElementById("js_check").hidden=true
    </script>
    <div id="result" hidden style="width: fit-content;margin: auto;">
        
        <?php
        if (!isset($_GET['nick'])){
            header("Location: index.php");
        }

        $nick=$_GET['nick'];
        echo "<iframe id='ifr' src='raw_result.php?nick=$nick' hidden></iframe>";

        $pfp="";
        error_reporting(0); #avatar
        $json= file_get_contents("https://www.reddit.com/user/$nick/about.json");
        error_reporting(1);
        $img=json_decode($json,true)["data"]["icon_img"];
        $img=substr($img,0,strrpos($img,".")+4);
        if ($img !=""){
            $pfp = base64_encode(file_get_contents($img));
        }

        $h="";
        if ($pfp=="")
            $h="hidden";
        echo "<div class='image' id='image'>
        <div id='circles'></div>
        <img id='tmpl' src='template.png' alt='template'>
        <img $h id='pfp' class='pfp' src='data:image/png;base64,$pfp'/>
        <div id='nickname' class='nickname'>u/$nick</div>
        <div id='data' class='data'></div>
        <div class='circle' style='border-width: 5px; left: 2048px; top: 808px;'></div>
        </div>";
        ?>

        <div class="box"> 
            <div class="button">
                <input type="radio" id="r3" name="set" checked onchange="sset(3)"/>
                <label for="r3">default</label>
            </div>
            <div class="button">
                <input type="radio" id="r0" name="set" onchange="sset(0)"/>
                <label for="r0">first placer</label>
            </div>
            <div class="button">
                <input type="radio" id="r1" name="set" onchange="sset(1)"/>
                <label for="r1">final canvas</label>
            </div>
            <div class="button">
                <input type="radio" id="r2" name="set" onchange="sset(2)"/>
                <label for="r2">endgame</label>
            </div>

            <input id="c0" class="hue slider" type="range" value="16" max="360" oninput="setClr(0)">
            <input id="c1" class="lght slider" type="range" value="50" min="20" max="100" oninput="setClr(1)">
            <input id="c2" class="opct slider" type="range" value="70" min="10" max="100" oninput="setClr(2)">
            
            <div style="width:100%; text-align:right;">
                <input type='button' class="btn" style="font-size: 24px;" value='reset' onclick="default_clr()"/>
            </div>                
            <input type='button' class="btn" value='save .png' onclick="savepng()"/>
        </div>
        
        <script>//color stuff
            const nick="<?php echo $nick ?>"
            ncknm=document.getElementById("nickname")//nick size
            font=80
            while(ncknm.scrollHeight>120 && font > 0){
                font-=1;
                ncknm.style.setProperty('font-size',font+"px");
            }
            
            set=0
            const df= [[125,55,70],[75,60,70],[190,70,70],[16,50,70]]
            var clr=[]
            df_clr()

            function sset(x){
                set=x
                for (var i = 0; i < 3; i++){
                    document.getElementById("c"+i).value=clr[set][i]
                    setClr(i)
                }
            }

            const root = document.documentElement;
            function setClr(x){

                val=document.getElementById("c"+x).value
                clr[set][x]=val
                if (x!=0) val+="%"
                root.style.setProperty('--s'+x, val);
                val=clr[set][0]+",100%,"+clr[set][1]+"%,"+clr[set][2]+"%"
                root.style.setProperty('--c'+set, val);
                
                
            }
            sset(0); sset(1); sset(2); sset(3);
            
            function default_clr(){
                clr[set]=[...df[set]]
                sset(set)
            }
            function df_clr(){
                for (var i = 0; i < df.length; i++)
                    clr[i]=[...df[i]]
            }
        </script>
    
        <div class="box">
            <h2>raw data</h2>
            <h3>hash:</h3>
            <div class="code_box">
                <code id="raw_data_hash"></code>
            </div>
            <br><h3>download:</h3><br>
            <div class="button bt_raw_data">
                <input type="checkbox" id="e0" onchange="raw_data(0)"/>
                <label for="e0">date</label>
            </div>
            <div class="button bt_raw_data">
                <input type="checkbox" id="e1" checked onchange="raw_data(1)"/>
                <label for="e1">color</label>
            </div>
            <div class="button bt_raw_data">
                <input type="checkbox" id="e2" checked onchange="raw_data(2)"/>
                <label for="e2">cords</label>
            </div>

            <div class="button">
                <input type="radio" id="ee3" name="set2" checked onchange="only(3)"/>
                <label for="ee3">all</label>
            </div>
            <div class="button">
                <input type="radio" id="ee0" name="set2" onchange="only(0)"/>
                <label for="ee0">first placer</label>
            </div>
            <div class="button">
                <input type="radio" id="ee1" name="set2" onchange="only(1)"/>
                <label for="ee1">final canvas</label>
            </div>
            <div class="button">
                <input type="radio" id="ee2" name="set2" onchange="only(2)"/>
                <label for="ee2">endgame</label>
            </div>
            
            <div class="code_box">
                <code id="raw_result"></code>
            </div>
            <input type='button' class="btn" value='save .txt' onclick="saveRawData()"/>

        </div>

        <script>//raw data stuff
            rw_dt=[false,true,true]
            onnly=3
        
            function data_gen(html=false){
                i=0
                out=""
                endl="\n"
                if (html) endl="<br>"

                for (let r = 1; r < data.length-1; r++) {
                    row=data[r]
                    if (onnly==3 || row[4].includes(onnly)){
                        if (html){
                            i++
                            if (i>9){
                                out+="..."
                                break
                            }
                        }
                        
                        $coma=false
                        if (rw_dt[0]){
                            out+=row[0]
                            $coma=true
                        }
                        if (rw_dt[1]){
                            if ($coma) out+=","
                            out+=row[3]
                            $coma=true
                        }
                        if (rw_dt[2]){
                            if ($coma) out+=","
                            out+=row[1]+","+row[2]
                            $coma=true
                        }
                        out+=endl;
                    }
                }
                return out
            }
            function raw_data_write(){
                result=document.getElementById("raw_result")
                result.innerHTML=data_gen(true)
            }
            raw_data_write()

            function raw_data(x){
                rw_dt[x]=document.getElementById("e"+x).checked
                raw_data_write()
            }

            function only(x){
                onnly=x
                raw_data_write()
            }                

        </script>

    </div>

    <div class='center_div' id="loading_msg"></div>

    <?php include("footer.html") ?>

    <script>//data loading stuff
        function loadmsg(x){
            document.getElementById("loading_msg").innerHTML=x+"<p id='msg'><p><form action='index.php'><input type='submit' value='return' /></form></div>"
        }
        loadmsg("looking for: <strong>u/"+nick+"</strong>..<br><p>please stay on this site</p>")

        repeated=0
        data=""
        const circles=document.getElementById("circles")

        function loadpx(){
            circles.innerHTML=""
            trophy=[0,0,0]

            //placing circles
            for (let i = 1; i < data.length-1; i++) {
                data[i]=data[i].split(";")
                var d=data[i]
                tr=JSON.parse(d[4])
                data[i][4]=tr
                clss="circle"
                for (t of tr){
                    clss+=" c"+t
                    trophy[t]+=1
                }
                circles.innerHTML+="<div class='"+clss+"' style='left: "+(d[1]-12)+"px; top: "+(d[2]-12)+"px;'></div>"
            }
            document.getElementById("raw_data_hash").innerHTML=data[0]
            raw_data_write()
        
            //numbers on the right
            document.getElementById("data").innerHTML="placed pixels: "+(data.length-2)+"<br>"
            dt=["first placer: "+trophy[0],"final canvas: "+trophy[1],"endgame: "+trophy[2]]
            t=0
            for (i=0; i<3; i++){
                if (trophy[i]!=0){
                    image.innerHTML+="<div class='circle c"+i+"'style='border-width: 5px; left: 2048px; top: "+(908+100*t)+"px;'></div>"
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
                case "request_sent":
                    repeated+=1
                    if (repeated>2){
                        loadmsg("database not responding :c<p>wait here or come back later</p>")
                        console.log("request nr."+repeated)
                    }
                    break
                case "processing":
                    loadmsg("<strong>u/"+nick+"</strong> found<br>processing data..<br>")
                    break
                case "not_found":
                    loadmsg("<div class='center_div'><strong>u/"+nick+"</strong><br>user not found :c<br>")
                    refresh=false
                    break
                default:
                    data=data.split(".")
                    if (data[data.length-1]=="_end_"){
                        refresh=false
                        loadpx()
                    }
                    else repeated+=1
            }
            if (refresh) setTimeout(function() { document.getElementById('ifr').contentWindow.location.reload(); }, 1000);
        }
        var myIframe = document.getElementById('ifr');
            myIframe.addEventListener("load", function() {
            checkData();
        });

    </script>
    <canvas hidden id="output" width="2800" height="2000"></canvas>
    <script>//downloading stuff
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
            ctx.drawImage(profile, 2175, 100, 450, 450);

            ctx.font = "bold "+font+"px Arial";//nickname
            ctx.fillStyle = "#cfcfcf";
            ctx.textAlign = "center";
            ctx.fillText("u/"+nick, 2400, 660);

            ctx.font = "bold 60px Arial";//first/last px
            ctx.fillStyle = "#cfcfcf";
            ctx.textAlign = "left";
            ctx.fillText("placed pixels: "+(data.length-2), 2100, 800);

            ctx.beginPath();//default circle
            ctx.arc(2070,783,12,0,2*Math.PI);
            ctx.lineWidth = 5;
            c=hslaToHex(clr[3][0],100,clr[3][1],clr[3][2])
            ctx.strokeStyle=c;
            ctx.stroke();

            t=0
            for (i=0; i<3; i++){//trophies
                if (trophy[i]!=0){
                    ctx.fillText(dt[i], 2100, 900+t*100); //text

                    ctx.beginPath();
                    ctx.arc(2070,883+t*100,12,0,2*Math.PI); //circles
                    ctx.lineWidth = 5;
                    c=hslaToHex(clr[i][0],100,clr[i][1],clr[i][2])
                    ctx.strokeStyle=c;
                    ctx.stroke();
                    t+=1
                }
            }


            const layers=[3,0,1,2]
            for (z of layers){
                for (row of data){
                    e=3
                    for (i of row[4]){
                        e=i
                    }
                    if (e==z){
                        ctx.beginPath();
                        ctx.arc(row[1],row[2],12,0,2*Math.PI);
                        ctx.lineWidth = 2;
                        c=hslaToHex(clr[e][0],100,clr[e][1],clr[e][2])
                        ctx.strokeStyle=c;
                        ctx.stroke();
                    }
                    
                }
            }
            
            var image = canvas.toDataURL();
            var aDownloadLink = document.createElement('a');
            aDownloadLink.download = 'canvas_'+nick+'.png';
            aDownloadLink.href = image;
            aDownloadLink.click();
        }
        const names=["_first_placer_","_final_canvas_","_endgame_","_"]
        function saveRawData() {
            var blob = new Blob([data_gen()], { type: "text/plain;charset=utf-8" });
            var aDownloadLink = document.createElement('a');
            aDownloadLink.download = "data"+names[onnly]+nick+".txt";
            aDownloadLink.href = window.URL.createObjectURL(blob);
            aDownloadLink.click();
        }
    </script>
</body>
</html>	