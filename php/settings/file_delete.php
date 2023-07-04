<?php
    // $path= $_SERVER['DOCUMENT_ROOT'].'/filestorage/';
    // $path;
    $file = $_POST['vfilepath'];
    $obj = array();
    $callback;

    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/filestorage/'.$file)) 
    {
        if (!unlink($_SERVER['DOCUMENT_ROOT'].'/filestorage/'.$file))
          {  
            $obj[0]->status = "Deleting file error";
            $obj[0]->statusid = 0;
          }
        else
          {
            $obj[0]->status = "File successfully removed";
            $obj[0]->statusid = 1;
          }
    } else 
    {
        $obj[0]->status = "File not found";
        $obj[0]->statusid = 0;
    }
    
    $callback = json_encode($obj);
    echo $callback;

?>