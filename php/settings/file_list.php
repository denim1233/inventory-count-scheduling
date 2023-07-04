<?php

$files;
$obj = array();

if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/filestorage/')) 
{
    $ctr = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        if ($entry != "." && $entry != "..") 
        {
            $obj[$ctr]->filename = $entry;
            $obj[$ctr]->filepath = $_SERVER['DOCUMENT_ROOT'].'/filestorage/'.$entry;
            $ctr = $ctr + 1;
        }
    }
    closedir($handle);
}

$files = json_encode($obj);
echo $files;

?>