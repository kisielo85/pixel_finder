<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pixel_finder</title>
    <link rel="stylesheet" href="style.css?m=13">
</head>
<body>   
    <h1 id="js_check">please enable javascript in order for site to work</h1>
    <script>
        document.getElementById("js_check").hidden=true
    </script>
    
    <?php
    session_start();
    
    if(isset($_SESSION['loop'])){
        $l=$_SESSION['loop'];
    }
    else{
        $_SESSION['loop']=5;
        $l=5;
    }
    echo "<input type='hidden' id='l' value='$l'>";
    

    if (isset($_GET['nick'])){
        $nick=$_GET['nick'];
        if ($_SESSION['nick']!=$nick){
            unset($_SESSION['pfp']);
            unset($_SESSION['data']);
            header("Location: index.php?nick=$nick");
        }

        $dt_good=false;
        $noresult=false;
        $found=false;
        if (isset($_SESSION['data'])){
            $data=$_SESSION['data'];
            $dt_good=true;
        }
        else{
            $data="";
            if($l<5){
                $ip="";
                $cipher= "aes-128-gcm";
                if (in_array($cipher, openssl_get_cipher_methods()))
                {
                    $tag=file_get_contents("server_ip_tag.txt");
                    $txt=file_get_contents("server_ip.txt");
                    $ip = openssl_decrypt($txt, $cipher, "BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB", $options=0, "CCCCCCCCCCCCCCCCC", $tag);
                }
                error_reporting(0);
                $data= file_get_contents("http://$ip/static/results/data_$nick.txt");
                error_reporting(1);
                if ($data != ""){
                    if ($data=="_end_"){
                        $noresult=true;
                    }
                    else{
                        $found=true;
                        $data = explode(".",$data);
                        if($data[count($data)-1]=="_end_"){
                            $dt_good=true;
                            $_SESSION['data']=$data;
                        }
                    }
                    
                }
            }
        }
        $pfp="";
        if ($dt_good) {
            echo "<div id='loading'><h2>loading..</h2><p>the more pixels there are - the longer it will take<p></div>";
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
            <img id='tmpl' src='template.png' alt='template'>
            <img $h id='pfp' class='pfp' src='data:image/png;base64,$pfp'/>
            <div id='nickname' class='nickname'>u/$nick</div>
            <div id='data' class='data'></div>
            <div class='circle' style='border-width: 5px; left: 2048px; top: 808px;'></div>
            </div>";
            echo "<script>const hash='$data[0]'; const data=[";//saving data to js
                for ($i=1; $i<count($data)-1; $i+=1){
                    $line=explode(";",$data[$i]);
                    echo "['$line[0]',$line[1],$line[2],'$line[3]',$line[4]],";
                }
                echo "]";
            echo "</script>";?>

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
            
            <script>
                nick=document.getElementById("nickname")
                font=80
                while(nick.scrollHeight>120 && font > 0){
                    font-=1;
                    nick.style.setProperty('font-size',font+"px");
                }
                trophy=[0,0,0]
                const image=document.getElementById("image")
                for (row of data){
                    clss="circle"
                    for (n of row[4]){
                        clss+=" c"+n
                        trophy[n]+=1
                    }
                        
                    image.innerHTML+="<div class='"+clss+"' style='left: "+(row[1]-12)+"px; top: "+(row[2]-12)+"px;'></div>"
                }

                
                dt=["first placer: "+trophy[0],"final canvas: "+trophy[1],"endgame: "+trophy[2]]

                document.getElementById("data").innerHTML="placed pixels: "+data.length+"<br>"
                t=0
                for (i=0; i<3; i++){
                    if (trophy[i]!=0){
                        image.innerHTML+="<div class='circle c"+i+"'style='border-width: 5px; left: 2048px; top: "+(908+100*t)+"px;'></div>"
                        document.getElementById("data").innerHTML+=dt[i]+"<br>"
                        t+=1
                    }
                }
                
                document.getElementById("loading").hidden=true
                
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
                sset(0)
                sset(1)
                sset(2)
                sset(3)
                

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
                    <code><?php echo $data[0];?></code>
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

            <script>

                rw_dt=[false,true,true]
                onnly=3
            
                function data_gen(html=false){
                    i=0
                    out=""
                    endl="\n"
                    if (html) endl="<br>"

                    for (row of data){
                        if (onnly==3 || row[4].includes(onnly)){
                            if (html){
                                i++
                                if (i>5){
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

        <?php
            include("footer.html");
            $l=5;
        } elseif ($noresult) {
            echo "<div class='center_div'>";
            echo "<strong>u/$nick</strong><br>";
            echo "user not found :c<br><br>";
            echo "<form action='index.php'><input type='submit' value='return' /></form>";
            echo "</div>";
        }
        else{
            echo "<div class='center_div'>";
            if ($found){
                echo "<strong>u/$nick</strong> found<br>processing data..<br>";
                $l=4;
            }
            else{
                echo "looking for: <strong>u/$nick</strong>..<br>";
                $l-=1;
            }
            if ($l<=0){
                header("Location: index.php?nick=$nick");
            }
            echo "<p id='msg'><p>";

            echo "<form action='index.php'><input type='submit' value='return' /></form>";
            echo "</div>";
        }
    }

    $_SESSION['loop']=$l;
    ?>
    <canvas hidden id="output" width="2800" height="2000"></canvas>


    <script>
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
            ctx.fillText("u/<?php echo $nick?>", 2400, 660);

            ctx.font = "bold 60px Arial";//first/last px
            ctx.fillStyle = "#cfcfcf";
            ctx.textAlign = "left";
            ctx.fillText("placed pixels: "+data.length, 2100, 800);

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
            aDownloadLink.download = 'canvas_<?php echo $nick ?>.png';
            aDownloadLink.href = image;
            aDownloadLink.click();
        }
        const names=["_first_placer_","_final_canvas_","_endgame_","_"]
        function saveRawData() {
            var blob = new Blob([data_gen()], { type: "text/plain;charset=utf-8" });
            var aDownloadLink = document.createElement('a');
            aDownloadLink.download = "data"+names[onnly]+"<?php echo $nick ?>.txt";
            aDownloadLink.href = window.URL.createObjectURL(blob);
            aDownloadLink.click();
        }
    </script>

    <script>
        var res=document.getElementById("image")
        if (<?php if ($dt_good or $noresult) echo "true"; else echo "false";?>){
            document.title="result - pixel_finder"
            //counter copied from w3schools lol
            /*var countDownDate = new Date("Jun 25, 2022 00:00:00").getTime();

            var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            document.getElementById("timer").innerHTML = days + "d " + hours + "h " + minutes + "m ";

            if (distance < 0) {
                clearInterval(x);
                document.getElementById("timer").innerHTML = "it should be already gone lol.";
            }
            }, 1000);*/
        }
        else{
            async function reload(x) {
            await new Promise(resolve => setTimeout(resolve, x));
            window.location.reload()
            }
            var l =document.getElementById("l").value
            if (l>3){
                document.getElementById("msg").innerHTML="please stay on this site"
                reload(4000)
            }
            else{
                document.getElementById("msg").innerHTML="looks like the database is not responding<br>wait here or come back later<br><br>re-trying in "+l*8+" seconds.."
                reload(8000)
            }
        }
    </script>



</body>
</html>	