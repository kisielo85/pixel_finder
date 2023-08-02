<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pixel_finder</title>
    <link rel="stylesheet" href="style.css?m=16">     
</head>
<body>
    <?php
        $year = @$_GET['year'];
        if ($year=="2022") header("Location: /place?year=22");
        if (!$year) $year="23";
    ?>
    <form action="" method="GET">
        <h1>super duper pixel finder
            <select name="year">
                <option value="17" <?php if($year=="17")echo"selected"?>>2017</option>
                <option value="22" <?php if($year=="22")echo"selected"?>>2022</option>
                <option value="23" <?php if($year=="23")echo"selected"?>>2023</option>
            </select>
        </h1>

    
        <div class="center_div">
            u/<input type="text" name='nick' maxlength="20" autocomplete="off">
            <input type="submit" value="find">
        </div>

    </form>
    <?php
    if ($nick = @$_GET['nick']){

        if (strlen($nick)>20){
            $nick=substr($nick,0,20);
        }
        $ch = array('\\','/',':','*','?','"','<','>','|',' ');
        $nick=str_replace($ch,'',$nick);

        $header="Location: result.php?nick=$nick&year=$year";
        header($header);
    }
    ?>

    <div class="box" style="text-align: left;">
        <h3>updates</h3><br>
        30.07.2023: fixed the savePNG button<br>
        01.08.2023: trophies for place2023 are now working<br>
        01.08.2023: improved 2023 matching
        <p>you can check what I'm currently working on here:<br><a href="https://trello.com/b/vH66AXR5/pixelfinder">trello.com/b/vH66AXR5/pixelfinder</a></p>
    </div>

    <div class="box" style="text-align: left;">
        <h3>reddit is killing third party apps and itself.</h3><br>
        reddit changes its policy and from now on the API is paid, which means the death of many applications such as apollo, infinity and boost etc, these were often open source applications that created a new clearer look, no ads and other features.<br><br>
        <h4><a href="https://www.reddit.com/r/Save3rdPartyApps/">r/Save3rdPartyApps</a><h4>
        
    </div>
    
    <div class="box">
        <h3>(advertisement)</h3><br>
        <iframe sandbox="allow-same-origin allow-scripts allow-popups allow-forms" src="advert.html" frameborder="0" style="width:350px; height:300px; border:none"></iframe>
    </div>
    
</body>
</html>