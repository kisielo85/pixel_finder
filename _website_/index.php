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
    <h1>super duper pixel finder 2000</h1>
    <form action="" method="GET">
        <div class="center_div">
            u/<input type="text" name='nick' maxlength="20" autocomplete="off">
            <input type="submit" value="find">
        </div>
    </form>
    <?php
    if (isset($_GET['nick'])){
        if (isset($_SESSION['data']))
            unset($_SESSION['data']);
        
        $nick=$_GET['nick'];
        if (strlen($nick)>20){
            $nick=substr($nick,0,20);
        }
        $ch = array('\\','/',':','*','?','"','<','>','|',' ');
        $nick=str_replace($ch,'',$nick);
        
        header("Location: result.php?nick=$nick");
    }
    ?>
    
</body>
</html>