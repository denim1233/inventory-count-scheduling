<?php

    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/alphp.php');
    $AL = new ALPHP;
    $authenticate = false;
    
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    
    if($AL->CHECK_REQUEST_POST() == true && parse_url($actual_link, PHP_URL_HOST) == $webname)
    {
         $authenticate = true;
    }
    
?>