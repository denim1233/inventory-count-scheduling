<?php

include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');

// if($authenticate == true)
// {
    $p1 = $_POST['vmovieid'];
    $db = new PDO($dsn,$username,$password);
    $stmt = $db->prepare("CALL `PM_MovieGenre_Get`(?)");
    $stmt->bindValue(1, $p1, PDO::PARAM_INT);
    $obj = array();
    
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
    
// }

?>
 