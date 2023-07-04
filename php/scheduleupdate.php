<?php

$url = 'http://192.168.4.57/cicmonitoring_v2/php/AdminController.php';
$data = array('action' => 'import_store_settings');

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { echo "error!";}
else{echo "update success testing copy!";}

var_dump($result);


?>