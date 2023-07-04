<?php
try 
{ 
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');

    if($authenticate == true)
    {
    	$p1 = $_POST['vgenreid'];
        $p2 = $_POST['vgenrename'];
        $p3 = 1;
        $p4 = $_POST['vgenredescription'];
        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("CALL `SYS_Genre_ManageRecord`(?,?,?,?)");
        $stmt->bindValue(1, $p1, PDO::PARAM_INT);
        $stmt->bindValue(2, $p2, PDO::PARAM_STR);
        $stmt->bindValue(3, $p3, PDO::PARAM_INT);
        $stmt->bindValue(4, $p4, PDO::PARAM_STR);
        
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
} 
catch (PDOException $e) 
{ 
   echo $e; 
} 
?>