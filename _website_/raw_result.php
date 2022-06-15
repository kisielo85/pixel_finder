<?php   
    if (isset($_GET['nick'])){
        $nick=$_GET['nick'];

        $raw_data="";
        $ip="";
        $cipher= "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $tag=file_get_contents("server_ip_tag.txt");
            $txt=file_get_contents("server_ip.txt");
            $ip = openssl_decrypt($txt, $cipher, "pass_B", $options=0, "pass_C", $tag);
        }
        error_reporting(0);
        $nm="data_";
        if(isset($_GET['tr']) && $_GET['tr']=="false"){
            $nm="data_notr_";
        }
        $raw_data= file_get_contents("http://$ip/static/results/$nm$nick.txt");
        error_reporting(1);
        if ($raw_data != ""){
            if ($raw_data=="_end_"){
                echo "not_found";
            }
            else{
                $data = explode(".",$raw_data);
                if($data[count($data)-1]=="_end_")
                    echo $raw_data;
                else
                    echo "processing";
            }
            
        }
        else{
            $nick=$_GET['nick'];
            if (strlen($nick)>20){
                $nick=substr($nick,0,20);
            }
            $ch = array('\\','/',':','*','?','"','<','>','|',' ');
            $nick=str_replace($ch,'',$nick);

            $ip="";
            $cipher= "aes-128-gcm";
            if (in_array($cipher, openssl_get_cipher_methods()))
            {
                $tag=file_get_contents("server_ip_tag.txt");
                $txt=file_get_contents("server_ip.txt");
                $ip = openssl_decrypt($txt, $cipher, "pass_B", $options=0, "pass_C", $tag);
            }
            $tr="true";
            if(isset($_GET['tr']))
                $tr=$_GET['tr'];
            
            $postdata = http_build_query(
                array(
                    'name' => $nick,
                    'tr' => $tr,
                )
            );
            $opts = array('http' =>
                array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );
            $context = stream_context_create($opts);
            $result = file_get_contents("http://$ip/", false, $context);
            $_SESSION['nick']=$nick;
            echo "request_sent";
        }
    }
    ?>