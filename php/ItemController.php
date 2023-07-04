<?php

namespace Item;
use PDO;
use stdClass;
use Settings\AppSettings;
use CicModel\CicModel;
use Group\GroupController;

class ItemController{

    public static function CHECK_ITEM_DUPLICATE_ENTRY(){
        
        $parameter = array(":STOREID" => $_POST['storeid'],":BARCODE" => $_POST['barcode'], ":SCHEDULEDATE" => $_POST['scheduledate'], ":GROUPID" => $_POST['groupid']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_ADD_FILTER,$parameter));

    }

    public static function LOAD_BRANCH_GROUP(){
        
        $parameter = array(":STOREID" => $_POST['storeid']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_BRANCH_GROUP_GET,$parameter));

    }

    public static function LOAD_ACTIVE_GROUP(){
        
        $parameter = array(":STOREID" => $_POST['storeid'],":PARAMDATE" => $_POST['paramdate']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_ACTIVE_GROUP_GET,$parameter));

    }

    public static function LOAD_ITEM(){

        if( $_POST['groupid'] === '-1'){ $groupid = null; }else{ $groupid = $_POST['groupid']; }
        if( $_POST['storeid'] === '-1'){ $storeid = null; }else{ $storeid = $_POST['storeid']; }
        if( $_POST['divisionid'] === '-1'){ $divisionid = null; }else{ $divisionid = $_POST['divisionid']; }
        if( $_POST['brandid'] === '-1'){ $brandid = null; }else{ $brandid = $_POST['brandid']; }

        $parameter = array(":BARCODE" => $_POST['barcode'],":DESCRIPTION" => $_POST['description'],":BRANDID" => $brandid,":DIVISIONID" => $divisionid, ":STOREID" => $storeid);
        echo json_encode(CicModel::LOAD_ORA_DATA(CicModel::$ORA_ITEM_GET,$parameter,'prodm'));
  
    }

    public static function SEARCH_ITEM(){
        
        if( $_POST['groupid'] === '-1'){ $groupid = null; }else{ $groupid = $_POST['groupid']; }
        if( $_POST['storeid'] === '-1'){ $storeid = null; }else{ $storeid = $_POST['storeid']; }
        if( $_POST['divisionid'] === '-1'){ $divisionid = null; }else{ $divisionid = $_POST['divisionid']; }
        if( $_POST['brandid'] === '-1'){ $brandid = null; }else{ $brandid = $_POST['brandid']; }

        $datefrom =  $_POST['datefrom'];
        $dateto =  $_POST['dateto'];
        $parameter = array(":DATEFROM" => $datefrom,":DATETO" => $dateto,":STOREID" => $_POST['storeid'],":GROUPID" => $_POST['groupid'],":DIVISIONID" => $divisionid,":BRANDID" => $brandid,":BARCODE" => $_POST['barcode'],":LEGACYBARCODE" => $_POST['legacybarcode'],":DESCRIPTION" => $_POST['description']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_ITEM_SEARCH,$parameter));

    }

    public static function CHILD_ITEM(){

        $parameter = array(":STATUSID" => $_POST['statusid']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_ITEM_CHILD,$parameter));
        
    }

    public static function CANCEL_ITEM(){

        $parameter = array(":SCHEDULEID" => $_POST['table_data']['scheduleid'],":STATUSID" => 3);
        echo json_encode(CicModel::MANAGE_SQL_DATA(CicModel::$SQL_INVENTORY_STATUS,$parameter));

    }

    public static function INSERT_REASON(){
        
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $table_data = $_POST['table_data'];
        $parameter = array();
        $statusid = 0;
        // echo json_encode($table_data);
        $ob =  new stdClass();
        $sql = CicModel::$SQL_REASON_INSERT;

        foreach ($table_data as $val ) {
            $parameter = array(":SCHEDULEID" => $val['scheduleid'],":REASON" => $_POST['extra'],":USERNAME" => $_SESSION['personnelname'] ,":USERID" => $val['scheduleid'],":STATUSID" => 3,":STATUSNAME" => 'Cancelled',":TRANSACTIONDATE" => date("Y-m-d"));
            $sth = $db->prepare($sql);
            $sth->execute($parameter);
        }

        try {
            $db->commit();
            $ob->requeststatus = 'successfully saved';
            $ob->status = 1;
        } catch (PDOException $e) {
            $db->rollBack();
            $ob->requeststatus = $sth->errorinfo();;
            $ob->status = 0;
        }

        echo json_encode($ob);

    }

    public static function ADD_ITEM(){
        GroupController::GET_PRODUCT_DW_DATA( $_POST['table_data']['barcode'], $_POST['table_data']['storeid'],"reserve");
        ob_end_clean();
        $productid = 0;
        $parameter = array(":GROUPID" => $_POST['table_data']['groupid'],":BARCODE" => $_POST['table_data']['barcode']);
        $data = CicModel::LOAD_SQL_DATA(CicModel::$SQL_ITEM_CHECK,$parameter);
        $parameter1 = array(":GROUPID" => $_POST['table_data']['groupid'],":BARCODE" => $_POST['table_data']['barcode'],":INPUTDATE" => date("Y-m-d"),':STOREID' => $_POST['table_data']['storeid']);

        // print_r($parameter1);

        // return;
        
        if(count($data) === 0){
            //change the description
            //insert product if it doesnt exist 
            $datacontainer = array();
            $ob =  new stdClass();
            $db3 = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
            $sth = $db3->prepare("
                INSERT IGNORE INTO inv_product (barcode, `description` , inputdate, `groups`, batchid)
                VALUES(:BARCODE,'GET DESC AT PRODUCT_DW',:INPUTDATE,:GROUPID,(SELECT batchid FROM inv_batch WHERE groupid = :GROUPID AND storeid = :STOREID AND recordstatus = 2))
           ");
            
            if($sth->execute($parameter1)){

                $productid = $db3->lastInsertId();

            }else{
    
                $ob->requeststatus = $sth->errorinfo();
                print_r($sth->errorinfo());
                $ob->status = 0;
            }
            // echo "naginsert";

        }else{
            $productid = $data[0]['productid'];
            // echo "else";
        }

        $parameter2 = array(":PRODUCTID" => $productid,":SCHEDULEDATE" => $_POST['table_data']['scheduledate'],":GROUPID" => $_POST['table_data']['groupid'],":STATUSID" => 1,":REMARKS" => '.',":STOREID" => $_POST['table_data']['storeid']);
        $datacontainer = CicModel::MANAGE_SQL_DATA(CicModel::$SQL_ITEM_ADD,$parameter2);
        echo json_encode($datacontainer);   

        //insert newly addded item to inv_schedule_reason
        $parameter3 = array(":SCHEDULEID" => $datacontainer->insertedId,":REASON" => 'newly added item',":USERNAME" => $_SESSION['personnelname'] ,":USERID" => 1,":STATUSID" => 1,":STATUSNAME" => 'Open',":TRANSACTIONDATE" => date("Y-m-d"));
        CicModel::MANAGE_SQL_DATA(CicModel::$SQL_REASON_INSERT,$parameter3);

        // INSERT INTO DW TO ENSURE DATA WILL EXIST


    }

    public static function ADD_PHASEOUT_ITEM(){

        $productid = 0;
        $parameter1 = array(":STOREID"=> $_POST['table_data']['storeid'],":DESCRIPTION" => $_POST['table_data']['description'],":GROUPID" => $_POST['table_data']['groupid'],":BARCODE" => $_POST['table_data']['barcode'],":INPUTDATE" => date("Y-m-d"));
   
        $datacontainer = array();
        $ob =  new stdClass();
        $db3 = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db3->prepare("  
            INSERT IGNORE INTO inv_product (legacybarcode, `description` , inputdate, groups, batchid)
            VALUES(:BARCODE,:DESCRIPTION,:INPUTDATE,:GROUPID,(SELECT batchid FROM inv_batch WHERE groupid = :GROUPID AND storeid = :STOREID AND recordstatus = 2))
       ");
        
        if($sth->execute($parameter1)){

            $productid = $db3->lastInsertId();

        }else{

            $ob->requeststatus = $sth->errorinfo();
            $ob->status = 0;

        }

        $parameter2 = array(":PRODUCTID" => $productid,":SCHEDULEDATE" => $_POST['table_data']['scheduledate'],":GROUPID" => $_POST['table_data']['groupid'],":STATUSID" => 1,":REMARKS" => '.');
        $datacontainer = CicModel::MANAGE_SQL_DATA(CicModel::$SQL_ITEM_ADD,$parameter2);
        echo json_encode($datacontainer);

        //insert newly addded item to inv_schedule_reason
        $parameter3 = array(":SCHEDULEID" => $datacontainer->insertedId,":REASON" => 'newly added item',":USERNAME" => $_SESSION['personnelname'] ,":USERID" => 1,":STATUSID" => 1,":STATUSNAME" => 'Open',":TRANSACTIONDATE" => date("Y-m-d"));
        CicModel::MANAGE_SQL_DATA(CicModel::$SQL_REASON_INSERT,$parameter3);

        // add item into inv_product
        // get product id 
        // insert into schedule

    }

    public static function UPDATE_STATUS(){

        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $table_data = $_POST['table_data'];
        $parameter = array();
        $statusid = 0;
        // echo json_encode($table_data);
        $ob =  new stdClass();
        $sql = CicModel::$SQL_INVENTORY_STATUS;

        foreach ($table_data as $val ) {
            $parameter = array(":SCHEDULEID" => $val['scheduleid'],":STATUSID" => 3);
            $sth = $db->prepare($sql);
            $sth->execute($parameter);
        }
            // $db->commit();

        try {
            $db->commit();
            $ob->requeststatus = 'successfully saved';
            $ob->status = 1;
        } catch (PDOException $e) {
            $db->rollBack();
            $ob->requeststatus = $sth->errorinfo();;
            $ob->status = 0;
        }

        echo json_encode($ob);

    }

}

?>