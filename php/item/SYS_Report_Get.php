<?php
    // include('php/settings/websettings.php');
    // include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');
    // if($authenticate == true)
    // {

        $webname = 'apitinfo.ml';
        $dsn = 'mysql:host=192.168.11.103;dbname=cicmonitoring;charset=utf8';
        $username = 'root';
        $password = 'root';

        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("SELECT * FROM reporttype");
        
        if ($stmt->execute()) 
        { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            echo $JSON;
        } 
        else 
        {
        //display error information
        //var_dump($rs);
        // print_r($stmt->errorInfo());
        
        }
    // }
?>