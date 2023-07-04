<?php
try 
{ 
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');

    if($authenticate == true)
    {
        $p1 = 0;
    	$p2 = $_POST['vmovieid'];
        $p3 = $_POST['vmoviegenreid'];
        $p4 = 1;
        $obj = array();
    
        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("CALL `PM_MovieGenre_Insert`(?,?,?,?)");
        $stmt->bindValue(1, $p1, PDO::PARAM_INT);
        $stmt->bindValue(2, $p2, PDO::PARAM_INT);
        $stmt->bindValue(3, $p3, PDO::PARAM_INT);
        $stmt->bindValue(4, $p4, PDO::PARAM_INT);
        if ($stmt->execute())
        { 
            $obj['dbdata'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $obj['status'][0]->statusid = "1";
            $obj['status'][0]->statusname = "Query Success";
        }
        else
        {
            $obj['status'][0]->statusid = "0";
            $obj['status'][0]->statusname = "Query failed";
        }
        
        $JSON = json_encode($obj);
        echo $JSON; 
        
        // print_r($stmt->errorInfo());
        
    }
} 
catch (PDOException $e) 
{ 
   echo $e; 
} 
?>