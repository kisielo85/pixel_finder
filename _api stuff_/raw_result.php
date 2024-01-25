<?php
    header('Access-Control-Allow-Origin: *');
    if ($nick = @$_GET['nick']){
        error_reporting(0);
        $year="all";
        if (isset($_GET['year']))
            $year=$_GET['year'];
        
        include 'config.php';
        $data = file_get_contents("http://$ip/find/$nick/$year");
        //$data = file_get_contents("http://localhost:2139/find/$nick/$year");
        error_reporting(1);
        if ($data){
            echo $data;
        }
        else{
            echo "{'error':'no_response'}";
        }
    }
    ?>