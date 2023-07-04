<?php
namespace Inventory;
use PDO;
use stdClass;
use Settings\AppSettings;
use CicModel\CicModel;
date_default_timezone_set('Asia/Manila');


class InvController{

    protected static $ORA_DB_DATE = "select TO_CHAR (max(extracteddate), 'YYYY-MON-DD HH12:MI:SS') as dbdate from DWT_EX_PRODUCT_CIC_V2 ";
    protected static $ORA_ONHAND = "SELECT barcode,onhand FROM XXCH_ALLSITE_PRODUCT_V WHERE barcode IN (?) AND ORGANIZATION_ID = ?";
    protected static $SQL_INVENTORY_UPDATE = "UPDATE inv_schedule SET WAREHOUSEAREA = :WAREHOUSEAREA, SELLINGAREA = :SELLINGAREA, REMARKS = :REMARKS, VARIANCE = :VARIANCE WHERE SCHEDULEID = :SCHEDULEID";
    protected static $SQL_INVENTORY_STATUS = "UPDATE inv_schedule SET STATUSID = :STATUSID WHERE SCHEDULEID = :SCHEDULEID";
    protected static $parameter = array();
    protected static $ORA_GET_INVENTORY = "
    SELECT 
    SCHEDULEID,ONHAND_QTY,0 as SELLING_QTY, 0 as RECEIVING_QTY,0 as ADJUSTMENT_QTY,0 as RESERVATION_QTY,0 as ADJ_ISSUE,0 as ADJ_RECEIPT,0 as RCV_IR,0 as RCV_ER,(SELECT TO_CHAR(SYSDATE, 'YYYY-MM-DD HH:MI:SS AM') FROM dual) as UPDATEDATE
    FROM XXCH_CIC_PRODUCT_DATA_V
    WHERE scheduledate BETWEEN ( TO_DATE(SYSDATE , 'DD-MM-YY')) AND ( TO_DATE(SYSDATE , 'DD-MM-YY'))
    AND statusid = 1
    ";

    protected static $ORA_GET_INVENTORY_V2 = "
    SELECT 
    :SCHEDULEID AS SCHEDULEID,ONHAND_QTY,'0' as SELLING_QTY,SHIPCON_QTY,ADJUSTMENT_QTY,'0' as RESERVATION_QTY,RCV_IR,RCV_ER,ADJ_ISSUE,ADJ_RECEIPT,
    (SELECT TO_CHAR(SYSDATE, 'YYYY-MM-DD HH:MI:SS AM') FROM dual) as UPDATEDATE
        FROM XXCH_PRODUCT_CIC_UPDATER2_V 
    WHERE 
        barcode = :BARCODE
    AND 
        ORGANIZATION_ID = :STOREID
    AND 
    SCHEDULEDATE = (SELECT TO_DATE(TO_DATE(:SCHEDULEDATE, 'YYYY-MM-DD')) FROM dual)
    ";

    protected static $ORA_GET_INVENTORY_PRODM = "
        SELECT 
            :SCHEDULEID AS SCHEDULEID,
               (
					SELECT 
					NVL(SUM(RSL.QUANTITY_SHIPPED), 0) AS SHIPCON_QTY
					FROM RCV_SHIPMENT_LINES RSL
					LEFT JOIN RCV_SHIPMENT_HEADERS RSH
					ON RSL.SHIPMENT_HEADER_ID = RSH.SHIPMENT_HEADER_ID
					LEFT JOIN ORG_ORGANIZATION_DEFINITIONS OODA
					ON RSL.FROM_ORGANIZATION_ID = OODA.ORGANIZATION_ID
					LEFT JOIN ORG_ORGANIZATION_DEFINITIONS OODB
					ON RSL.TO_ORGANIZATION_ID = OODB.ORGANIZATION_ID
					LEFT JOIN MTL_SYSTEM_ITEMS_B MSIB
					ON RSL.ITEM_ID = MSIB.INVENTORY_ITEM_ID
					AND MSIB.ORGANIZATION_ID = RSL.FROM_ORGANIZATION_ID
					WHERE TRUNC(RSH.SHIPPED_DATE) = TO_DATE(TO_DATE(:SCHEDULEDATE, 'YYYY-MM-DD'), 'dd-Mon-yy')
					AND RSL.FROM_ORGANIZATION_ID = :STOREID
					AND MSIB.INVENTORY_ITEM_ID = :INVENTORY_ITEM_ID
                )
        AS SHIPCON_QTY,
                (SELECT  NVL(SUM(RSLI.QUANTITY_SHIPPED),0) QTYA FROM RCV_SHIPMENT_LINES RSLI
                    WHERE RSLI.SHIPMENT_LINE_STATUS_CODE IN('FULLY RECEIVED', 'PARTIALLY RECEIVED')
                    AND RSLI.REQUISITION_LINE_ID IS NOT NULL 
                    AND TRUNC(RSLI.LAST_UPDATE_DATE) = TO_DATE(TO_DATE(:SCHEDULEDATE, 'YYYY-MM-DD'), 'dd-Mon-yy')
                    AND RSLI.ITEM_ID =  :INVENTORY_ITEM_ID
                    AND RSLI.TO_ORGANIZATION_ID = :STOREID
                    )
        AS RCV_IR,
                (SELECT NVL(SUM(RSLE.QUANTITY_SHIPPED),0) QTYA FROM RCV_SHIPMENT_LINES RSLE
                    WHERE RSLE.SHIPMENT_LINE_STATUS_CODE IN('FULLY RECEIVED', 'PARTIALLY RECEIVED')
                    AND RSLE.PO_HEADER_ID IS NOT NULL 
                    AND TRUNC(RSLE.LAST_UPDATE_DATE) = TO_DATE(TO_DATE(:SCHEDULEDATE, 'YYYY-MM-DD'), 'dd-Mon-yy')
                    AND RSLE.ITEM_ID =  :INVENTORY_ITEM_ID
                    AND RSLE.TO_ORGANIZATION_ID = :STOREID
                    )
         AS RCV_ER,
                (SELECT  NVL(SUM(LNE_AI.TRANSACTION_QUANTITY),0) QTY FROM XXCH_INV_MISC_ISSUE_HDR_STG  HDR_AI
                    LEFT JOIN XXCH_INV_MISC_ISSUE_LINE_STG LNE_AI ON HDR_AI.MISC_ISSUE_HDR_ID = LNE_AI.MISC_ISSUE_HDR_ID 
                    WHERE  HDR_AI.APPROVE_STATUS = 'APPROVED'   AND UPPER(SUBSTR(HDR_AI.TRANSACTION_TYPE, 1, (INSTR(HDR_AI.TRANSACTION_TYPE, ':', 1) - 1))) = 'ISSUE'
                AND HDR_AI.ORGANIZATION_ID = :STOREID
                AND LNE_AI.INVENTORY_ITEM_ID =  :INVENTORY_ITEM_ID
                AND  TRUNC(HDR_AI.TRANSACTION_DATE) = TO_DATE(TO_DATE(:SCHEDULEDATE, 'YYYY-MM-DD'), 'dd-Mon-yy')
                )
        AS ADJ_ISSUE,
                (SELECT  NVL(SUM(LNE_AR.TRANSACTION_QUANTITY), 0) QTY FROM XXCH_INV_MISC_ISSUE_HDR_STG  HDR_AR
                    LEFT JOIN XXCH_INV_MISC_ISSUE_LINE_STG LNE_AR ON HDR_AR.MISC_ISSUE_HDR_ID = LNE_AR.MISC_ISSUE_HDR_ID 
                    WHERE  HDR_AR.APPROVE_STATUS = 'APPROVED'   AND UPPER(SUBSTR(HDR_AR.TRANSACTION_TYPE, 1, (INSTR(HDR_AR.TRANSACTION_TYPE, ':', 1) - 1))) = 'RECEIPT'
                AND HDR_AR.ORGANIZATION_ID = :STOREID
                AND LNE_AR.INVENTORY_ITEM_ID = :INVENTORY_ITEM_ID
                AND  TRUNC(HDR_AR.TRANSACTION_DATE) = TO_DATE(TO_DATE(:SCHEDULEDATE, 'YYYY-MM-DD'), 'dd-Mon-yy')
                )
        AS ADJ_RECEIPT,
        UPDATEDATE
        FROM ( SELECT TO_CHAR(SYSDATE, 'YYYY-MM-DD HH:MI:SS AM') as UPDATEDATE FROM dual ) 
";


    protected static $SQL_REMARKS_MANAGE = "
    INSERT INTO inv_remarks(REMARKSID,ROLEID,ROLENAME,SCHEDULEID,REMARKS)
        VALUES (:REMARKSID,:ROLEID,:ROLENAME,:SCHEDULEID,:REMARKS)
        ON DUPLICATE KEY 
        UPDATE 
            REMARKS = values(REMARKS)
    ";

    protected static $SQL_ITEM_OPEN_GET = "
        SELECT BARCODE FROM inv_schedule INS
        INNER JOIN inv_product INP ON INP.PRODUCTID = INS.PRODUCTID
        WHERE STATUSID = 1
        AND SCHEDULEDATE BETWEEN CURDATE() AND CURDATE()
    ";

    protected static $SQL_REMARKS_GET = "
    SELECT 
        GROUP_CONCAT( INR.remarks
        SEPARATOR '<br>') AS remarks,
        remarksid
    FROM inv_remarks INR
    WHERE roleid = IFNULL(:ROLEID,INR.`roleid`) AND scheduleid = IFNULL(:SCHEDULEID,INR.`scheduleid`)
    ";

    public static function GET_REMARKS(){

        $tempid;

        if($_POST['roleid'] === '0'){
            $tempid = null;
        }else{
            $tempid = $_POST['roleid'];
        }

        $parameter = array(":ROLEID" => $tempid,":SCHEDULEID" => $_POST['scheduleid']);
        $data = InvController::APP_MODEL(InvController::$SQL_REMARKS_GET, $parameter );
        echo json_encode($data);
    }

    public static function MANAGE_REMARKS(){

        $table_data = $_POST['table_data'];
        $parameter = array(":REMARKSID" => $table_data['remarksid'],":ROLEID" => $_SESSION['roleid'], ":ROLENAME" => $_SESSION['rolename'], ":SCHEDULEID" => $table_data['scheduleid'],":REMARKS" => $table_data['remarks'] );

        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare(InvController::$SQL_REMARKS_MANAGE);
        $ob =  new stdClass();

        // :REMARKSID,:ROLEID,:ROLENAME,:SCHEDULEID,:REMARKS

        if($sth->execute($parameter)){
            $ob->requeststatus = 'data successfully saved';
        }else{
            $ob->requeststatus = 'saving failed';
        }
        $returndata = json_encode($ob);
        echo $returndata;
    }

    public static function LOAD_INVENTORY(){

        if($_POST['groupid'] === '-1'){$groupid = null;}else{$groupid = $_POST['groupid'];}
        $parameter = array(":DATEFROM" => $_POST['datefrom'],":DATETO" => $_POST['dateto'],":STATUSID" => $_POST['statusid'],":STOREID" => $_POST['storeid'],":GROUPID" => $groupid);
        $data = CicModel::LOAD_SQL_DATA(CicModel::$SQL_INVENTORY_GET, $parameter );

        // print_r($data);
        echo json_encode($data);

    }

    public static function LOAD_PRODUCT_DATA(){

    $myServer = '192.168.4.215';
    $myDB = 'DW';
    $oci_uname = 'dw';
    $oci_pass = 'dw';

    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";

    $connect = oci_connect('dw', 'dw', $db);
    $query = InvController::$ORA_GET_INVENTORY;
    $sql_dw = oci_parse($connect, $query);
    $dataContainer = array();
    $data = array();
    if(oci_execute($sql_dw)){
        while ($row = oci_fetch_assoc($sql_dw)) {
            array_push($dataContainer,$row);
        }
        InvController::UPDATE_DATA($dataContainer,'schedule');

    }else{
        echo "failed!";
    }

        // $options = [
        //     PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
        //     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        //     PDO::ATTR_CASE => PDO::CASE_LOWER,
        //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        // ];
        //     /*Oracle*/
        //     $myServer = '192.168.4.215';
        //     $myDB = 'DW';
        //     $oci_uname = 'dw';
        //     $oci_pass = 'dw';
        //     $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
        //     try {
        //         $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
        //         $sth = $conn->query(InvController::$ORA_GET_INVENTORY);

        //         if($sth->execute()){
        //             $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);  
        //             //   echo json_encode($datacontainer);
        //             InvController::UPDATE_DATA($datacontainer,'schedule');
        //         }else{
        //              print_r($sth->errorInfo());
        //         }
                
        //     } catch(PDOException $e) {
        //         echo 'ERROR: ' . $e->getMessage();
        //     }

    }

    public static function LOAD_PRODUCT_DATA_V2(){

        $sql = 'select inventory_item_id from inv_product_dw where barcode = :BARCODE';
        $parameter1 = array(":BARCODE" =>  $_POST['barcode']);
        $dataContainer = CicModel::LOAD_SQL_DATA($sql,$parameter1);
        $parameter = array();
        $parameter = array(':SCHEDULEID' => $_POST['scheduleid'],':STOREID' => $_POST['storeid'],':SCHEDULEDATE' => $_POST['scheduledate'],":INVENTORY_ITEM_ID" => $dataContainer[0]['inventory_item_id']);

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];
            /*Oracle*/
            $myServer = '192.168.4.55';
            $myDB = 'PROD';
            $oci_uname = 'appsro';
            $oci_pass = 'appsro';
            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->prepare(InvController::$ORA_GET_INVENTORY_PRODM);
                if($sth->execute($parameter)){
                    
                    $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);  

                    InvController::UPDATE_DATA($datacontainer,'schedule_v2');

                }else{
                     print_r($sth->errorInfo());
                }
                
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }

     public static function LOAD_DIVISION_DATA(){

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];
            /*Oracle*/
            $myServer = '192.168.4.215';
            $myDB = 'DW';
            $oci_uname = 'dw';
            $oci_pass = 'dw';
            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->query(InvController::$ORA_GET_DIVISION);

                if($sth->execute()){
                    $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                    //   echo json_encode($datacontainer);
                    InvController::UPDATE_DATA($datacontainer,'division');
                }else{
                     print_r($sth->errorInfo());
                }
                
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }

    public static function LOAD_DEPARTMENT_DATA(){

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];
            /*Oracle*/
            $myServer = '192.168.4.215';
            $myDB = 'DW';
            $oci_uname = 'dw';
            $oci_pass = 'dw';
            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->query(InvController::$ORA_GET_DEPARTMENT);

                if($sth->execute()){
                    $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                    //   echo json_encode($datacontainer);
                    InvController::UPDATE_DATA($datacontainer,'department');
                }else{
                     print_r($sth->errorInfo());
                }
                
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }

    public static function LOAD_CATEGORY_DATA(){

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];
            /*Oracle*/
            $myServer = '192.168.4.215';
            $myDB = 'DW';
            $oci_uname = 'dw';
            $oci_pass = 'dw';
            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->query(InvController::$ORA_GET_CATEGORY);

                if($sth->execute()){
                    $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                    //   echo json_encode($datacontainer);
                    InvController::UPDATE_DATA($datacontainer,'category');
                }else{
                     print_r($sth->errorInfo());
                }
                
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }

    public static function UPDATE_STATUS(){
        try {
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
        $ob =  new stdClass();
        $ob->requeststatus = 'data successfully saved';
        $returndata = json_encode($ob);
        echo $returndata;
        } catch (Exception $e) {
            $db->rollback();
            $ob =  new stdClass();
            $ob->requeststatus = 'data saving failed';
            $returndata = json_encode($ob);
            echo $returndata;
        }
    }

    public static function LOAD_DB_DATE(){

         $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];
            /*Oracle*/
            $myServer = '192.168.4.215';
            $myDB = 'DW';
            $oci_uname = 'dw';
            $oci_pass = 'dw';
            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->query(InvController::$ORA_DB_DATE);

                if($sth->execute()){
                    $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($datacontainer);
                   
                }else{
                     print_r($sth->errorInfo());
                }
                
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }


    public static function UPDATE_INVENTORY(){
        $val = array();
        $val = $_POST['table_data'];
        
        $variance = ($val['sellingarea'] + $val['warehousearea']) - ($val['adjustment'] + $val['receiving'] + $val['sellout'] + $val['onhand']);
        $parameter = array();
        $sql = InvController::$SQL_INVENTORY_UPDATE;
        // $variance = ($val['sellingarea'] +  $val['warehousearea']);
        $parameter = array(":WAREHOUSEAREA" => $val['warehousearea'],":SELLINGAREA" => $val['sellingarea'],":SCHEDULEID" => $val['scheduleid'],":REMARKS" => $val['remarks'], ":VARIANCE" => $variance);
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
        $ob =  new stdClass();

        if ($sth->execute($parameter))
        { 
            $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
        } 
        else 
        {
            $ob->requeststatus = 'loading data failed';
            $ob->query = $sql;
            $ob->errorinfo = $sth->errorInfo();
            $datacontainer = $ob;
        }

        return $datacontainer;
    }

    public static function UPDATE_DATA($data,$action){

        CicModel::$BOOLEAN === 'false';
      
        $paramarray = $data;

        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $insert_values = array();
        $ctr = 0;

        foreach($paramarray as $d){
            
            $insert_values = array_merge($insert_values, array_values($d));
            $question_marks[] = '('  . InvController::placeholders('?', sizeof($d)) . ')';

            if($ctr === 1999){
                InvController::sqlexcution($db,$insert_values,$question_marks,$action);   
                unset($question_marks);
                $insert_values = array();
                $ctr = 0;
            }

            $ctr = $ctr + 1;
        }

        if (count($insert_values) != 0){
            InvController::sqlexcution($db,$insert_values,$question_marks,$action);
            $insert_values = array();
            unset($question_marks);
            $ctr = 0;
        }

        try {
            $db->commit();
            if($action != 'schedule'){
                      if($action != 'schedule_v2'){

                InvController::INSERT_DATABASE_LOG('success');
                InvController::DISABLE_UPDATE('store_data_update');
                      }
            }
        } catch (PDOException $e) {
            $db->rollBack();
        }

    }

    public static function INSERT_DATABASE_LOG($status){

        CicModel::$PARAMETER = array(':STOREID' => $_POST['table_data']['storeid'],':DESCRIPTION' => $_POST['table_data']['storename'], ':STATUS' => $status );
        echo json_encode(CicModel::MANAGE_SQL_DATA(CicModel::$SQL_LOG_INSERT,CicModel::$PARAMETER));

    }

    public static function LOAD_DATABASE_LOG(){

        CicModel::$DATA_CONTAINER = CicModel::LOAD_SQL_DATA(CicModel::$SQL_LOG_GET,null);
        echo json_encode(CicModel::$DATA_CONTAINER);

    }

    public static function LOAD_DATABASE_CREDENTIALS(){

        CicModel::$PARAMETER = array(":STOREID" => $_POST['table_data']['storeid']);
        CicModel::$DATA_CONTAINER = CicModel::LOAD_SQL_DATA(CicModel::$SQL_STORE_DB_CREDENTIALS_GET,CicModel::$PARAMETER);
        InvController::STORE_TO_HO_DB_UPDATE();

    }

    public static function UPDATE_BATCH_STORE(){
        
        $url = ''.CicModel::$DATA_CONTAINER[0]['serverip'].'/cicmonitoring_v2_res/php/AdminController.php';
        $data = array('action' => 'updatebatch');

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ }
        // var_dump($result);
    }

    public static function STORE_TO_HO_DB_UPDATE(){
        //pass parameter here
        //$_POST['dateto']
        InvController::UPDATE_BATCH_STORE();
        $parameter = array();
        // $parameter = array(":DATEFROM" => date("Y-m-d"),":DATETO" => date("Y-m-d"));
        $parameter = array(":DATEFROM" => $_POST['table_data']['datefrom'],":DATETO" => $_POST['table_data']['dateto']);
        $db = new PDO(CicModel::$DATA_CONTAINER[0]['dsn'],CicModel::$DATA_CONTAINER[0]['username'],CicModel::$DATA_CONTAINER[0]['password']);
        $sth = $db->prepare(CicModel::$SQL_INV_STORE_INPUT_GET);
        $ob =  new stdClass();


        if ($sth->execute($parameter))
        { 
            $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
            // print_r($parameter);
            InvController::UPDATE_DATA($datacontainer,'store_data_update');

        }else{

            print_r($sth->errorInfo());

        }
        
    }

    public static function sqlexcution($db,$data_to_insert,$qm,$action){
        // echo $db;
        // echo "<br>";
        // print_r($data_to_insert);
        // echo "<br>";
        // return;
        // echo "path test!";
        // echo "<br>";
        // return;
        switch ($action) {
            case 'schedule':
                  $stmt = $db->prepare("
                    INSERT IGNORE INTO inv_schedule(scheduleid,onhand,sellout,receiving,adjustment,reservation,rcv_ir,rcv_er,adj_issue,adj_receipt,updatedate)
                    VALUES ".implode(',', $qm)."
                    ON DUPLICATE KEY 
                    UPDATE 
                        onhand = values(onhand),
                        sellout = values(sellout),
                        receiving = values(receiving),
                        adjustment = values(adjustment),
                        reservation = values(reservation),
                        rcv_ir = values(rcv_ir),
                        rcv_er = values(rcv_er),
                        adj_issue = values(adj_issue),
                        adj_receipt = values(adj_receipt),
                        updatedate = values(updatedate)
                        ;
                    ");
                break;
            case 'schedule_v2':
                     $stmt = $db->prepare("
                    INSERT IGNORE INTO  inv_schedule(scheduleid,receiving,rcv_ir,rcv_er,adj_issue,adj_receipt,updatedate)
                    VALUES ".implode(',', $qm)."
                    ON DUPLICATE KEY 
                    UPDATE 
                        receiving = values(receiving),
                        rcv_ir = values(rcv_ir),
                        rcv_er = values(rcv_er),
                        adj_issue = values(adj_issue),
                        adj_receipt = values(adj_receipt),
                        updatedate = values(updatedate)
                        ;
                    ");
            break;

             case 'category':
                    $stmt = $db->prepare("
                    INSERT IGNORE INTO sys_category(categoryid,categoryname)
                    VALUES ".implode(',', $qm)."
                    ON DUPLICATE KEY 
                    UPDATE 
                        categoryname = values(categoryname)
                    ");
             break;

             case 'division':
                    $stmt = $db->prepare("
                    INSERT IGNORE INTO sys_division(divisionid,divisionname)
                    VALUES ".implode(',', $qm)."
                    ON DUPLICATE KEY 
                    UPDATE 
                        divisionname = values(divisionname)
                    ");

             break;

             case 'department':
                    $stmt = $db->prepare("
                    INSERT IGNORE INTO sys_department(departmentid,departmentname)
                    VALUES ".implode(',', $qm)."
                    ON DUPLICATE KEY 
                    UPDATE 
                        departmentname = values(departmentname)
                    ");
             break;
             case 'store_data_update':

                  $stmt = $db->prepare("
                    INSERT IGNORE INTO inv_schedule(scheduleid,onhand,sellout,receiving,adjustment,reservation,variance,remarks,tlremarks,atlremarks,statusid,sellingarea,warehousearea,adj_issue,adj_receipt,rcv_ir,rcv_er)
                    VALUES ".implode(',', $qm)."
                    ON DUPLICATE KEY 
                    UPDATE 
                        onhand = values(onhand),
                        sellout = values(sellout),
                        receiving = values(receiving),
                        adjustment = values(adjustment),
                        reservation = values(reservation),
                        variance = values(variance),
                        remarks = values(remarks),
                        tlremarks = values(tlremarks),
                        atlremarks = values(atlremarks),
                        statusid = values(statusid),
                        sellingarea = values(sellingarea),
                        warehousearea = values(warehousearea),
                        adj_issue = values(adj_issue),
                        adj_receipt = values(adj_receipt),
                        rcv_ir = values(rcv_ir),
                        rcv_er = values(rcv_er)
                        ;

                    ");
                break;
            default:
                # code...
                break;
        }

        if ($stmt->execute($data_to_insert)){ 

            if($action === 'store_data_update'){
                CicModel::$BOOLEAN === 'true';
         
            }
            
        } 
        else{

            echo $action;
            echo "<br>";
            print_r($stmt->errorInfo());
            // InvController::INSERT_DATABASE_LOG($stmt->errorInfo());
            CicModel::$BOOLEAN === 'false';
            echo "error!";

        }

    }

    public static function DISABLE_UPDATE($table){

        switch ($table) {
            case 'store_data_update':
                $sql = CicModel::$SQL_INV_STORE_INPUT_UPDATE;
            break;
        }

        $db = new PDO(CicModel::$DATA_CONTAINER[0]['dsn'],CicModel::$DATA_CONTAINER[0]['username'],CicModel::$DATA_CONTAINER[0]['password']);
        $sth = $db->prepare($sql);
        $ob =  new stdClass();

        if($sth->execute()){
            $ob->requeststatus = 'data successfully saved';
        }else{
             print_r($sth->errorInfo());

            //$ob->requeststatus = 'saving failed';
        }

        // $returndata = json_encode($ob);
        // echo $returndata;

    }


    public static function placeholders($text, $count=0, $separator=","){
        $result = array();
        if($count > 0){
            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }
        return implode($separator, $result);
    }

}

?>