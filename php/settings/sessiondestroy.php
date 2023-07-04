<?php
    session_start();
    unset($_SESSION['A31axwebtokenLo0192s']); // will delete just the name data
    session_destroy(); 
    
    $myObj->sessionid = 'empty' ;
    $myJSON = json_encode($myObj);
    echo $myJSON;
?>