<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-HVG835GHS8"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-HVG835GHS8');
    </script>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pixel finder</title>
    <link rel="stylesheet" href="style.css?m=14">
</head>
<body>   
    <h1 id="js_check">please enable javascript in order for site to work</h1>
    <script> document.getElementById("js_check").hidden=true </script>

    <?php
        //checking nick, year and passing it to javascript
        if (!$nick=@$_GET['nick']) header("Location: index.php");
        if (!$year=@$_GET['year']) $year="22";
        echo "<script> const nick='$nick'; const year='$year'; </script>";
        // loading data from raw_result
        echo "<iframe id='ifr' src='raw_result.php?nick=$nick&year=$year' hidden></iframe>";

        // getting avatar from reddit, and converting to base64
        $context = stream_context_create(['http' =>
        ['header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36']]);
        $pfp="";
        error_reporting(0);
        $json= file_get_contents("https://www.reddit.com/user/$nick/about.json",false,$context);
        error_reporting(1);
        $img=json_decode($json,true)["data"]["icon_img"];
        $img=substr($img,0,strrpos($img,".")+4);
        if ($img !="") $pfp = "data:image/png;base64,".base64_encode(file_get_contents($img));
    ?>

    <!-- this div holds everything that
    needs to be shown if a result is found-->
    <div id="result" hidden style="width: fit-content;margin: auto;">
        
        <div class='image' id='image'>
            <div id='circles'></div>
            <img id='tmpl' src='img/template<?php echo$year?>.png' alt='template'>
            
            <img id='pfp' class='pfp' src='<?php echo$pfp?>'/>
            <div id='nickname' class='nickname'>u/<?php echo$nick?></div>
            <div id='data' class='data'></div>
            <div class='circle' style='border-width: 5px; margin-left: var(--offset_x); left: 2048px; top: 808px;'></div>
        </div>

        <!-- color changing sliders -->
        <div class="box"> 
            <div class="button">
                <input type="radio" id="r3" name="set" checked onchange="type_set(3)"/>
                <label for="r3">default</label>
            </div>
            <div class="button">
                <input type="radio" id="r0" name="set" onchange="type_set(0)"/>
                <label for="r0">first placer</label>
            </div>
            <div class="button">
                <input type="radio" id="r1" name="set" onchange="type_set(1)"/>
                <label for="r1">final canvas</label>
            </div>
            <div class="button">
                <input type="radio" id="r2" name="set" onchange="type_set(2)"/>
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
        <script src="scripts/sliders.js?e=1"></script>


        <!-- raw data  -->
        <div class="box">
            <h2>raw data</h2>

            <h3>hash:</h3>
            <div class="code_box"><code id="raw_data_hash"></code></div>

            
            <br><h3>download:</h3><br>

            <!-- what to include -->
            <div class="button bt_raw_data">
                <input type="checkbox" id="e0" onchange="raw_data(0)"/><label for="e0">date</label>
            </div>
            <div class="button bt_raw_data">
                <input type="checkbox" id="e1" checked onchange="raw_data(1)"/><label for="e1">color</label>
            </div>
            <div class="button bt_raw_data">
                <input type="checkbox" id="e2" checked onchange="raw_data(2)"/><label for="e2">cords</label>
            </div>

            <!-- filters -->
            <div class="button">
                <input type="radio" id="ee3" name="set2" checked onchange="only(3)"/><label for="ee3">all</label>
            </div>
            <div class="button">
                <input type="radio" id="ee0" name="set2" onchange="only(0)"/><label for="ee0">first placer</label>
            </div>
            <div class="button">
                <input type="radio" id="ee1" name="set2" onchange="only(1)"/><label for="ee1">final canvas</label>
            </div>
            <div class="button">
                <input type="radio" id="ee2" name="set2" onchange="only(2)"/><label for="ee2">endgame</label>
            </div>
            
            <!-- result preview -->
            <div class="code_box"><code id="raw_result"></code></div>

            <input type='button' class="btn" value='save .txt' onclick="saveRawData()"/>

        </div>
        <script src="scripts/raw_data.js?e=1"></script>

    </div>

    <!-- used for messages like "looking for user" / "user not found" etc -->
    <div class='center_div' id="loading_msg">
        <div id="msg_content"></div>

        <form action='index.php'>
            <input type='hidden' name='year' value='<?php echo $year?>'>
            <input type='hidden' name='nick' value='<?php echo $nick?>'>
            <input type='submit' value='return'/>
        </form>
    </div>

    <?php include("footer.html") ?>

    <canvas hidden id="output" width="2800" height="2000"></canvas>

    <script src="scripts/data_loading.js?e=1"></script>
    <script src="scripts/downloading.js?e=2"></script>

    </body>
</html>	