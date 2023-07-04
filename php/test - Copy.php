<?php
ini_set('max_execution_time', '1000');

GET_INSERTED_DATA();


 function GET_INSERTED_DATA($storeid = 102,$batchid = 1){
  
        $datacontainer = array();
            $db = new PDO('mysql:host=192.168.11.103;port=3307;dbname=dev_cicmonitoring;charset=utf8','helpdesk','citihelpdesk');
            $sth = $db->prepare(
    
                "
                    SELECT BARCODE FROM INV_PRODUCT IP
                    INNER JOIN INV_BATCH IB ON IB.BATCHID = IP.BATCHID
                    WHERE IB.STOREID = :STOREID AND IB.BATCHID = :BATCHID
                "
            );

        $parameter = array(":STOREID" => $storeid,":BATCHID" => $batchid);

        $ctr = 0;

            if($sth->execute($parameter)){
                     $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                     $barcode = array();
                     $parambarcode = '';

// echo count($datacontainer);
// return;
                     // print_r($datacontainer);
    
            foreach ($datacontainer as $item) {
                $barcode[] = $item['BARCODE'];

                // print_r($item['BARCODE']);

                if($ctr === 500){
                    $parambarcode =  implode(",",$barcode);
                    GET_PRODUCT_DW_DATA($parambarcode,$storeid);
                    $barcode = array();
                    $ctr = 0;
                    $parambarcode = '';
                }

                $ctr = $ctr + 1;
            }

         if (count($barcode) != 0){
            $parambarcode =  implode(",",$barcode);
            GET_PRODUCT_DW_DATA($parambarcode,$storeid);
            $barcode = array();
            $ctr = 0;
            $parambarcode = '';

         }
            // print_r($barcode);

                    // echo json_encode($parambarcode);
                   
            }else{
                print_r($sth->errorInfo());
            }
        }

   function GET_PRODUCT_DW_DATA($parambarcode,$storeid){
    
        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];
            /*Oracle*/
            $myServer = '192.168.3.115';
            $myDB = 'DW';
            $oci_uname = 'dw';
            $oci_pass = 'dw';
            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->query("
                SELECT 
                    INVENTORY_ITEM_ID,
                    ORGANIZATION_ID,
                    BARCODE,
                    DESCRIPTION,   
                    SUPPLIERID,
                    SUPPLIERNAME,
                    DIVISIONID,
                    DIVISIONNAME,
                    DEPARTMENTID,
                    DEPARTMENTNAME,
                    CATEGORYID,
                    CATEGORYNAME,
                    DDP.EXTRACTEDDATE
                FROM
                    DWT_DIM_PRODUCT DDP
                INNER JOIN DWT_EX_PRODUCT_ASSIGN PA ON PA.INVENTORY_ITEM_ID = DDP.INVENTORYITEMID
                WHERE BARCODE IN (".$parambarcode.") AND ORGANIZATION_ID = ".$storeid."

                ");
   // WHERE ORGANIZATION_ID = ".$storeid" AND BARCODE IN(".$barcode.")
                if($sth->execute()){
                    $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                        //echo json_encode($datacontainer);
                    UPDATE_DATA($datacontainer);

                }else{
                     print_r($sth->errorInfo());
                }
                
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }



     function UPDATE_DATA($data){

        $paramarray = $data;
        $db = new PDO('mysql:host=192.168.11.103;port=3307;dbname=dev_cicmonitoring;charset=utf8','helpdesk','citihelpdesk');
        $db->beginTransaction();
        $insert_values = array();
        $ctr = 0;
   
        // print_r($paramarray);
        foreach($paramarray as $d){
            // print_r($d);
            // echo "<br>";
            // echo sizeof($d);
            // echo "<br>";
            $insert_values = array_merge($insert_values, array_values($d));
            $question_marks[] = '('  . placeholders('?', sizeof($d),',','update') . ')';
        
            if($ctr === 1999){
                sqlexcution($db,$insert_values,$question_marks,0,'update');   
                unset($question_marks);
                $insert_values = array();
                $ctr = 0;
            }

            $ctr = $ctr + 1;
        }

        // insert if there is still data in the array
        if (count($insert_values) != 0){
           sqlexcution($db,$insert_values,$question_marks,0,'update');
            $insert_values = array();
            unset($question_marks);
            $ctr = 0;
        }

        $db->commit();

    }

      function sqlexcution($db,$data_to_insert,$qm,$batchid,$identifier){
        //4498
        //409
        //4499
        //  print_r($data_to_insert);
        // echo $identifier;

   

        if($identifier === 'upload'){
            $stmt = $db->prepare("
            INSERT INTO inv_product (description, barcode , inputdate, groups, batchid)
            VALUES ".implode(',', $qm)."
            ");
        }else if ($identifier === 'update'){
  
            $stmt = $db->prepare("
            INSERT INTO inv_product_dw (inventory_item_id,organization_id,barcode,description,supplierid,suppliername,divisionid,divisionname,departmentid,departmentname,categoryid,categoryname,extracteddate)
            VALUES ".implode(',', $qm)."
            ON DUPLICATE KEY 
            UPDATE 
                organization_id = values(organization_id),
                barcode = values(barcode),
                description = values(description),
                supplierid = values(supplierid),
                suppliername = values(suppliername),
                divisionid = values(divisionid),
                divisionname = values(divisionname),
                departmentid = values(departmentid),
                departmentname = values(departmentname),
                categoryid = values(categoryid),
                categoryname = values(categoryname),
                extracteddate = values(extracteddate)
                ;
            ");
        }
        
        if ($stmt->execute($data_to_insert))
        { 
            $ob =  new stdClass();
            $ob->requeststatus = 'data successfully saved';
            $ob->batchid = $batchid;
            $returndata = json_encode($ob);
            echo $returndata;
        } 
        else 
        {
          print_r($stmt->errorInfo());
          //print_r($stmt);

          //print_r($qm);
        }
    }

    function placeholders($text, $count=0, $separator=",",$identifier){

        if ($identifier === 'upload'){
            $count = $count + 3;
        }
      
        // $count = $count + 1;
        $result = array();
        if($count > 0){
            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }
        return implode($separator, $result);
    }
 function APP_MODEL($sql,$parameter){
        $datacontainer = array();
          $db = new PDO('mysql:host=192.168.11.103;port=3307;dbname=dev_cicmonitoring;charset=utf8','helpdesk','citihelpdesk');
        $sth = $db->prepare($sql);
        if($sth->execute($parameter)){

        }else{
            print_r($sth->errorInfo());
        }
        $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
        // echo json_encode($datacontainer);
        return $datacontainer;
    }



?>

           
                    
