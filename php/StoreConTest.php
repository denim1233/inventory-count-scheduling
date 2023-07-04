<?php
ini_set('max_execution_time', '1000');

TEST();


 function TEST(){
  
    $datacontainer = array();
    $db = new PDO('mysql:host=192.168.4.44:3307;dbname=cicmonitoring_store;charset=utf8','helpdesk','citihelpdesk');
    $sth = $db->prepare("select * from inv_schedule where scheduledate = '2021-10-06' ");


    $ctr = 0;

        if($sth->execute()){
                 $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                    print_r($datacontainer);

        }else{
            print_r($sth->errorInfo());
        }
    }
?>