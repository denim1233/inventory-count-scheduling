<?php
ini_set('max_execution_time', '1000');

IMPORT_DATA();

    function IMPORT_DATA(){
  
        $db = new PDO('mysql:host=192.168.4.57;port=3306;dbname=dw_dblink;charset=utf8','root','password');
        $sth = $db->prepare(
            "
                select * dw_dblink.CIC_INV_BATCH

            "
        );

        if($sth->execute()){
             echo "get data succss!";

        }else{
            print_r($sth->errorInfo());
        }
    }
?>                    