<?php

namespace Inventory;
use PDO;
use stdClass;
use Settings\AppSettings;

class InvController{

    // UPDATE DATA AS POSTED
    // GET THE BY APPROVED AND POSTED

    protected static $SQL_INVENTORY_GET = "
    SELECT scheduleid,remarks,inv_product.barcode,inv_product.description,inv_schedule.scheduledate,sys_group.groupname,
    inv_schedule.sellingarea,inv_schedule.warehousearea,sys_status.statusname
    FROM INV_SCHEDULE 
    INNER JOIN SYS_STATUS ON SYS_STATUS.StatusId = INV_SCHEDULE.statusid 
    INNER JOIN INV_BATCH ON INV_BATCH.batchid = INV_SCHEDULE.batchid
    INNER JOIN INV_PRODUCT ON INV_PRODUCT.productid = INV_SCHEDULE.productid
    INNER JOIN SYS_GROUP ON SYS_GROUP.groupid  = INV_BATCH.groupid
    WHERE INV_SCHEDULE.statusid = :STATUSID
    AND `scheduledate` BETWEEN :SCHEDULEDATE AND :SCHEDULEDATE
    ;
    ";

    //GET ON HAND
    protected static $ORA_GETID = "SELECT BARCODE FROM XXCH_ALLSITE_PRODUCT_V WHERE BARCODE IN (:BARCODE)";
    protected static $SQL_INSERT_TEMP = "INSERT INTO TEMP_DATA (barcode,onhand) VALUES(:BARCODE,:ONHAND) ";
    protected static $SQL_GET_DATA;
    protected static $ORA_LOAD_STORES = "SELECT * FROM DWT_DIM_SITE WHERE SITETYPE = 'CITI_STORE' AND SITESTATUS = 'ACTIVE' ";

    // protected static $SQL_INVENTORY_GET = "
    // SELECT * FROM INV_SCHEDULE 
    // INNER JOIN SYS_STATUS ON SYS_STATUS.StatusId = INV_SCHEDULE.statusid 
    // INNER JOIN INV_BATCH ON INV_BATCH.batchid = INV_SCHEDULE.batchid
    // INNER JOIN INV_PRODUCT ON INV_PRODUCT.productid = INV_SCHEDULE.productid
    // INNER JOIN SYS_GROUP ON SYS_GROUP.groupid  = INV_BATCH.groupid
    // WHERE INV_SCHEDULE.statusid = :STATUSID
    // AND `scheduledate` BETWEEN :SCHEDULEDATE AND :SCHEDULEDATE
    // ;
    // ";


//     SELECT inv_product.barcode,inv_product.description,inv_schedule.scheduledate,sys_group.groupname,temp_product.ONHAND,
// inv_schedule.sellingarea,inv_schedule.warehousearea,sys_status.statusname
// FROM INV_SCHEDULE 
//     INNER JOIN SYS_STATUS ON SYS_STATUS.StatusId = INV_SCHEDULE.statusid 
//     INNER JOIN INV_BATCH ON INV_BATCH.batchid = INV_SCHEDULE.batchid
//     INNER JOIN INV_PRODUCT ON INV_PRODUCT.productid = INV_SCHEDULE.productid
//     INNER JOIN SYS_GROUP ON SYS_GROUP.groupid  = INV_BATCH.groupid
//     INNER JOIN temp_product on temp_product.BARCODE  = inv_product.barcode


    protected static $SQL_INVENTORY_UPDATE = "UPDATE INV_SCHEDULE SET WAREHOUSEAREA = :WAREHOUSEAREA, SELLINGAREA = :SELLINGAREA, REMARKS = :REMARKS WHERE SCHEDULEID = :SCHEDULEID";
    protected static $SQL_INVENTORY_STATUS = "UPDATE INV_SCHEDULE SET STATUSID = :STATUSID WHERE SCHEDULEID = :SCHEDULEID";

    public static function GET_INVENTORY(){
        $parameter = array(":SCHEDULEDATE" => date("Y-m-d"),":STATUSID" => $_POST['statusid']);
        echo json_encode(InvController::APP_MODEL(InvController::$SQL_INVENTORY_GET, $parameter ));
    }

    public static function UPDATE_STATUS(){
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $table_data = $_POST['table_data'];
        $parameter = array();
        $statusid = 0;

        if($_POST['action'] === 'inventory_approve'){
            $statusid = 6;
        }else if($_POST['action'] === 'inventory_post'){
            $statusid = 2;
        }
        
        // echo json_encode($statusid);
        
        $sql = InvController::$SQL_INVENTORY_STATUS;
        foreach ($table_data as $val ) {
            $parameter = array(":SCHEDULEID" => $val['scheduleid'],":STATUSID" => $statusid);
            $sth = $db->prepare($sql);
            $sth->execute($parameter);
        }
        $db->commit();
    }

    public static function UPDATE_INVENTORY(){
        $val = array();
        $val = $_POST['table_data'];
        $parameter = array();
        $sql = InvController::$SQL_INVENTORY_UPDATE;
        $parameter = array(":WAREHOUSEAREA" => $val['warehousearea'],":SELLINGAREA" => $val['sellingarea'],":SCHEDULEID" => $val['scheduleid'],":REMARKS" => $val['remarks']);
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);
        $sth->execute($parameter);
        $ob =  new stdClass();
        $ob->requeststatus = 'data successfully saved';
        $returndata = json_encode($ob);
        echo $returndata;
    }

    public static function APP_MODEL($sql,$parameter = null){
        $datacontainer = array();
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);
        $sth->execute($parameter);
        $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $datacontainer;
    }

}

?>