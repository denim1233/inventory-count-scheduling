<?php
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/alphp.php');
    $AL = new ALPHP;
    
    if(!isset($_SESSION['A31axwebtokenLo0192s']))
    {
        $AL->CREATE_TOKEN();
        $myObj->sessionid = $_SESSION["A31axwebtokenLo0192s"] ;
        $myJSON = json_encode($myObj);
        echo $myJSON;
    }
 
?>