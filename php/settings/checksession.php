<?php
    session_start();
    if(isset($_SESSION['A31axwebtokenLo0192s']) && !empty($_SESSION['A31axwebtokenLo0192s']))
    {
        $status->sessionexists = 'true';
    }
    else
    {
        $status->sessionexists = 'false';
    }
    $myJSON = json_encode($status);
    echo $myJSON;
?>