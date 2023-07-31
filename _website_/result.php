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
        // no nick
        if (!isset($_GET['nick'])){
            header("Location: index.php");
        }

        $nick=$_GET['nick'];
        $year="22";
        if (isset($_GET['year']))
            $year=$_GET['year'];

        echo "<iframe id='ifr' src='raw_result.php?nick=$nick&year=$year' hidden></iframe>";

        $context = stream_context_create([
            'http' => [
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36',
            ],
        ]);

        // reddit avatar
        $pfp="";
        error_reporting(0);
        $json= file_get_contents("https://www.reddit.com/user/$nick/about.json",false,$context);
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
        <img id='tmpl' src='template$year.png' alt='template'>
        <img $h id='pfp' class='pfp' src='data:image/png;base64,$pfp'/>
        <div id='nickname' class='nickname'>u/$nick</div>
        <div id='data' class='data'></div>
        <div class='circle' style='border-width: 5px; margin-left: var(--offset_x); left: 2048px; top: 808px;'></div>
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

        <script src="raw_data.js?m=1"></script>

    </div>

    <div class='center_div' id="loading_msg"></div>

    <?php include("footer.html") ?>
    <canvas hidden id="output" width="2800" height="2000"></canvas>

    <script>//data loading stuff
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

    </script>
    <script src="downloading.js?m=0"></script>
</body>
</html>	