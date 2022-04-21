<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>result - pixel_finder</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>    
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

        $cnt=false;
        if($l<5){
            $img = "http://localhost:2137/static/results/result_$nick.png";//change localhost to ip with python server
            error_reporting(0);
            $cnt=file_get_contents($img);
            error_reporting(1);
        }
        

        if ($cnt !== false) {
            $b64image = base64_encode($cnt);
            echo '<img id="res" src="data:image/png;base64,'.$b64image.'"/>';
            include("footer.html");
            $l=5;
        } else {
            echo "<div class='box'>";
            echo "looking for: <strong>u/$nick</strong>..<br>";

            if ($l<=0){
                header("Location: index.php?nick=$nick");
            }
            $l-=1;
            echo "<p id='msg'><p>";

            echo "<form action='index.php'><input type='submit' value='return' /></form>";
            echo "</div>";

        }
    }

    $_SESSION['loop']=$l;
    ?>
    <script>
        var res=document.getElementById("res")
        if (res){
            document.title="result - pixel_finder"
            //counter copied from w3schools lol
            var countDownDate = new Date("Jun 1, 2022 00:00:00").getTime();

            var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            document.getElementById("timer").innerHTML = days + "d " + hours + "h "
            + minutes + "m ";

            if (distance < 0) {
                clearInterval(x);
                document.getElementById("timer").innerHTML = "it should be already gone lol.";
            }
            }, 1000);
        }
        else{
            async function reload(x) {
            await new Promise(resolve => setTimeout(resolve, x));
            window.location.reload()
            }
            var l =document.getElementById("l").value
            if (l>3){
                document.getElementById("msg").innerHTML="please stay on this site"
                reload(3000)
            }
            else{
                document.getElementById("msg").innerHTML="looks like the database is not responding<br>wait here or come back later<br><br>re-trying in "+l*8+" seconds.."
                reload(8000)
            }
            
        }
    </script>



</body>
</html>	