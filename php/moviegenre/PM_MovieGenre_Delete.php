<?php
try 
{ 
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');
    
    if($authenticate == true)
    {
        
        $p1 = $_POST['vmoviegenreid'];
        $obj = array();
        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("CALL `PM_MovieGenre_Delete`(?)");
        $stmt->bindValue(1, $p1, PDO::PARAM_INT);
        
    	if ($stmt->execute())
        { 
            $obj['status'][0]->statusid = "1";
            $obj['status']->statusname = "Query Success";
        }
        else
        {
            $obj['status']->statusid = "0";
            $obj['status']->statusname = "Query failed";
        }
        
        $JSON = json_encode($obj);
        echo $JSON; 
        
    }
} 
catch (PDOException $e) 
{ 
   echo $e; 
} 
?>