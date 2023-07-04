<?php
try 
{ 
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');

    if($authenticate == true)
    {
        $p1 = $_POST['vcategoryid'];
        $p2 = $_POST['vcategoryname'];
        $p3 = 1;
        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("CALL `SYS_Category_ManageRecord`(?,?,?)");
        $stmt->bindValue(1, $p1, PDO::PARAM_INT);
        $stmt->bindValue(2, $p2, PDO::PARAM_STR);
        $stmt->bindValue(3, $p3, PDO::PARAM_INT);
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