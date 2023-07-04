<?php
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');
    
    if($authenticate == true)
    {
        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("CALL `SYS_Genre_Get`()");
        
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
    }
?>