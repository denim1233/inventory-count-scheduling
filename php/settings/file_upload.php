<?php
    $obj = array();
    $callback;
    $path= $_SERVER['DOCUMENT_ROOT'].'/filestorage/';
    $name = $_FILES['file']['name'];
    $tmp_name = $_FILES['file']['tmp_name'];
    
    if (!empty($name))
    {
        if(move_uploaded_file($tmp_name, $path.$name))
        {
            $obj[0]->status = "upload success";
            $obj[0]->statusid = 1;
        }
        else
        {
            $obj[0]->status = "uploading file error";
            $obj[0]->statusid = 0;
        }
    }
    
    $callback = json_encode($obj);
    echo $callback;
?>