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

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2466749636726261" crossorigin="anonymous"></script>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pixel finder</title>
    <link rel="stylesheet" href="style.css?m=16">     
</head>
<body>
    <?php
        $year = @$_GET['year'];
        if ($year=="2022") header("Location: /place?year=22");
        if (!$year) $year="23";
    ?>
    
    <form action="result.php" method="GET">
        <h1>super duper pixel finder
            <select name="year">
                <option value="17" <?php if($year=="17")echo"selected"?>>2017</option>
                <option value="22" <?php if($year=="22")echo"selected"?>>2022</option>
                <option value="23" <?php if($year=="23")echo"selected"?>>2023</option>
            </select>
        </h1>
    
        <div class="center_div">
            u/<input type="text" name='nick' maxlength="20" autocomplete="off" value="<?php echo @$_GET['nick'] ?>">
            <input type="submit" value="find">
        </div>
    </form>

    <div class="box" style="text-align: left;">
        <p>this project is on <a href="https://github.com/kisielo85/pixel_finder">github</a></p>
        <p>you can check what I'm currently working on here:<br><a href="https://trello.com/b/vH66AXR5/pixelfinder">trello.com/b/vH66AXR5/pixelfinder</a></p>
    </div>

    <div class="box" style="text-align: left;">
        <h3>reddit is killing third party apps and itself.</h3><br>
        reddit changes its policy and from now on the API is paid, which means the death of many applications such as apollo, infinity and boost etc, these were often open source applications that created a new clearer look, no ads and other features.<br><br>
        <h4><a href="https://www.reddit.com/r/Save3rdPartyApps/">r/Save3rdPartyApps</a><h4>
    </div>
    
</body>
</html>