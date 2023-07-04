<?php

include "autoload.php";
$functionopener = new AdminController();
$functionopener->Portal();

class AdminController {

    function Portal(){
        if(isset($_POST['action'])){
            if($_POST['action'] === 'index'){
               $this->LOAD_DATA();
            }
            else if($_POST['action'] === 'inventoryonhand'){
                $this->LOAD_DATA();
            }
            else if($_POST['action'] === 'item'){
                $this->LOAD_DATA();
            }
            else if($_POST['action'] === 'group'){
                $this->LOAD_BATCH();
            }
            else if($_POST['action'] === 'uploadfile'){
                $this->UPLOAD_FILE();
            }
            else if($_POST['action'] === 'set_group_data'){
                $this->SET_GROUP();
            }
            else if($_POST['action'] === 'groupchild'){
                $this->LOAD_CHILD();
            }
            else if($_POST['action'] === 'postproduct'){
                $this->LOAD_CHILD();
            }
            else if($_POST['action'] === 'settings_group'){
                // GO TO SETTINGS CONTROLLER
            }
        }
        else{
            return;
        }
    }

    function LOAD_BATCH(){

        $db = new PDO(env('DB_DSN'),env('DB_USERNAME'),env('DB_PASSWORD'));
        $stmt = $db->prepare("
            SELECT * FROM inv_batch
            INNER JOIN sys_group ON sys_group.groupid = inv_batch.groupid
            ");
        
        if ($stmt->execute()) { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            echo $JSON;
        } 
        else {
            $ob =  new stdClass();
            $ob->requeststatus = $stmt->errorInfo();
            $returndata = json_encode($ob);
            echo $returndata;
        }
    }

    function LOAD_CHILD(){

        $pbatchid = $_POST['pbatchid'];

        $db = new PDO(env('DB_DSN'),env('DB_USERNAME'),env('DB_PASSWORD'));
        $stmt = $db->prepare("
            SELECT * FROM inv_product WHERE batchid = ?;
            ");

        $stmt->bindValue(1, $pbatchid, PDO::PARAM_STR);
        
        if ($stmt->execute()) { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            echo $JSON;
        } 
        else {
            $ob =  new stdClass();
            $ob->requeststatus = $stmt->errorInfo();
            $returndata = json_encode($ob);
            echo $returndata;
        }
    }

    function LOAD_DATA(){
        $db = new PDO(env('DB_DSN'),env('DB_USERNAME'),env('DB_PASSWORD'));
        $stmt = $db->prepare("SELECT * FROM Admin");

        if ($stmt->execute()) { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            echo $JSON;
        } 
        else {
            $ob =  new stdClass();
            $ob->requeststatus = $stmt->errorInfo();
            $returndata = json_encode($ob);
            echo $returndata;
        }
    }

    function UPLOAD_DATA($filepath,$batchid){

            require('spreadsheet/SpreadsheetReader.php');
            $Reader = new SpreadsheetReader($_SERVER['DOCUMENT_ROOT'].'/cicmonitoring/'.$filepath);
            $Sheets = $Reader -> Sheets();
            $paramarray = array();

            foreach ($Sheets as $Index => $Name)
            {
                $Reader -> ChangeSheet($Index);
                foreach ($Reader as $Row)
                {
                    if($Row[0] != ''){
                        array_push($paramarray,$Row);
                    }
                }
            }

            $db = new PDO(env('DB_DSN'),env('DB_USERNAME'),env('DB_PASSWORD'));
            $db->beginTransaction();

            $insert_values = array();
            $ctr = 0;
            $question_marks;
            $arraybatchid = array($batchid);
            $groupparam = array($_POST['pgroupid']);
            $datenow = array(date("Y-m-d"));
            $ctrperyear = $_POST['pcountperyear'];


            foreach($paramarray as $d){
                
                $insert_values = array_merge($insert_values, array_values($d),$datenow,$groupparam,$arraybatchid);
                $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';

                if($ctr === 1999){
                    $this->sqlexcution($db,$insert_values,$question_marks,$batchid);   
                    unset($question_marks);
                    $insert_values = array();
                    $ctr = 0;
                }

                $ctr = $ctr + 1;
            }

            // insert if there is still data in the array
            if (count($insert_values) != 0){
                $this->sqlexcution($db,$insert_values,$question_marks,$batchid);
                $insert_values = array();
                unset($question_marks);
                $ctr = 0;
            }

            $db->commit();
    }

    function sqlexcution($db,$data_to_insert,$qm,$batchid){

        $stmt = $db->prepare("
        INSERT INTO inv_product (barcode, description, inputdate, groups, batchid)
        VALUES ".implode(',', $qm)."
        ");

        //         $stmt = $db->prepare("
        // INSERT INTO inv_product (barcode, description, inputdate, groups, systemonhand,sellout,receipts,invadj,sellingarea,whsearea,variance,remarks,remarks2,batchid)
        // VALUES ".implode(',', $qm)."
        // ");

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
        //  print_r($stmt->errorInfo());
         print_r($stmt);
        }
    }

    function placeholders($text, $count=0, $separator=","){
        $count = $count + 3;
        // $count = $count + 1;
        $result = array();
        if($count > 0){
            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }
        return implode($separator, $result);
    }

    function UPLOAD_FILE(){

        $pbatchname = $_POST['pbatchname'];
        $pbatchdate = $_POST['pbatchdate'];
        $precordstatus = 1;
        $puser = 1;
        $pgroupid = $_POST['pgroupid'];
        $pcountperyear = $_POST['pcountperyear'];
        $pfilereference = $_POST['pfilereference'];
        $name = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];
        $db = new PDO(env('DB_DSN'),env('DB_USERNAME'),env('DB_PASSWORD'));
        $stmt = $db->prepare("
        INSERT INTO inv_batch (batchname,batchdate,user,recordstatus,groupid,filereference,countperyear) 
        VALUES((SELECT  CONCAT(variablecharacter, '-000 ', nextnumber) FROM res_variable WHERE variableid = 1),?,?,?,?,?,?);
        UPDATE
            res_variable
        SET
            nextnumber = nextnumber + 1
        WHERE variableid = 1;
            ");

        // $stmt->bindValue(1, $pbatchname, PDO::PARAM_STR);
        $stmt->bindValue(1, $pbatchdate, PDO::PARAM_STR);
        $stmt->bindValue(2, $precordstatus, PDO::PARAM_INT);
        $stmt->bindValue(3, $puser, PDO::PARAM_INT);
        $stmt->bindValue(4, $pgroupid, PDO::PARAM_INT);
        $stmt->bindValue(5, $pfilereference, PDO::PARAM_STR);
        $stmt->bindValue(6, $pcountperyear, PDO::PARAM_INT);

        if ($stmt->execute()) 
        { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            $batchid = $db->lastInsertId();
            $path = $_SERVER['DOCUMENT_ROOT'].'/cicmonitoring/files/';
            if (!empty($name)) { move_uploaded_file($tmp_name, $path.$name); }
            $this->UPLOAD_DATA($pfilereference, $batchid);
        } 
        else 
        {
            //display error information
            //var_dump($rs);
            print_r($stmt->errorInfo());
        
        }
    }

    function SET_GROUP(){

        $db = new PDO(env('DB_DSN'),env('DB_USERNAME'),env('DB_PASSWORD'));
        
        $stmt = $db->prepare("
            UPDATE 
                inv_batch
            SET
                groupid = ?,
                countperyear = ?
            WHERE
                batchid  = ?;
            ");

        $stmt->bindValue(1, $pgroupid, PDO::PARAM_INT);
        $stmt->bindValue(2, $pcountperyear, PDO::PARAM_INT);
        $stmt->bindValue(3, $pbatchid, PDO::PARAM_INT);

        if ($stmt->execute()) 
        { 
            $ob =  new stdClass();
            $ob->requeststatus = "group was setted successfully!";
            $returndata = json_encode($ob);
            echo $returndata;
        } 
        else 
        {
            $ob =  new stdClass();
            $ob->requeststatus = $stmt->errorInfo();
            $returndata = json_encode($ob);
            echo $returndata;
        }
    }

    function POST_PRODUCT(){

        $pgroupid = $_POST['pgroupid'];
        $pcountperyear = $_POST['pcountperyear'];
        $pbatchid = $_POST['pbatchid'];

        $db = new PDO(env('DB_DSN'),env('DB_USERNAME'),env('DB_PASSWORD'));
        $stmt = $db->prepare("
                UPDATE 
                inv_batch
            SET
                groupid = ?,
                countperyear = ?
            WHERE
                batchid  = ?;
            ");

        $stmt->bindValue(1, $pgroupid, PDO::PARAM_INT);
        $stmt->bindValue(2, $pcountperyear, PDO::PARAM_INT);
        $stmt->bindValue(3, $pbatchid, PDO::PARAM_INT);

        if ($stmt->execute()) 
        { 
            $ob =  new stdClass();
            $ob->requeststatus = "group was setted successfully!";
            $returndata = json_encode($ob);
            echo $returndata;
        } 
        else 
        {
            $ob =  new stdClass();
            $ob->requeststatus = $stmt->errorInfo();
            $returndata = json_encode($ob);
            echo $returndata;
        }
    }
}
?>