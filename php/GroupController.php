<?php

namespace Group;

use Settings\AppSettings;
use PDO;
use SpreadsheetReader;
use stdClass;
use oci_connect;
use CicModel\CicModel;
use DateTime;

class GroupController{
    
    protected static $pcountperyear;
    protected static $pfilereference;
    protected static $pgroupid;
    protected static $pbatchdate;
    protected static $Months = 12;  /*12 months in a year*/
    protected static $precordstatus = 1;
    protected static $puser = 1;
    protected static $repeatevery = 0;
    protected static $productdata;
    protected static $activedate;
    protected static $batchid;
    protected static $datevariable = array();
    protected static $SQL_PRODUCT = "select * from inv_product WHERE batchid = :batchid";
    protected static $SQL_ACTIVE_DATE = "SELECT datedesc FROM `sys_activedate` WHERE (datedesc BETWEEN :datefrom AND :dateto) AND recordstatus = 4";
    protected static $SQL_CREATE_SCHEDULE = "INSERT IGNORE INTO inv_schedule(productid,batchid,countbatch,scheduledate,warehousearea,sellingarea,statusid,remarks,adminremarks) VALUES(:productid,:batchid,:countbatch,:scheduledate,:warehousearea,:sellingarea,:statusid,'.','.')";
    protected static $SQL_PARAMETER = array();
    protected static $TOTAL_PRODUCTS = 0;
    protected static $SQL_LOAD_BATCH = " 
      SELECT 
        INB.batchid,SG.groupname,INB.countperyear,INB.batchdate,INB.storename,
        INB.batchname,INB.canceldate,INB.startdate,INB.enddate,USD.full_name,
        CASE WHEN SS.statusname = 'Submitted' THEN 'Posted' ELSE SS.statusname  END AS statusname
    FROM inv_batch INB
        INNER JOIN sys_group SG ON SG.groupid  = INB.groupid
        INNER JOIN sys_status SS on SS.statusid  = INB.recordstatus
        LEFT JOIN (SELECT * FROM sys_users_division WHERE storeid = - 1) USD on USD.emp_id  = INB.user
    WHERE user = IFNULL(:USER,user) or user = 1
    ORDER BY INB.batchdate DESC
    ";
    
    protected static $SQL_LOAD_CHILD = "SELECT * FROM inv_schedule 
    INNER JOIN inv_product on inv_product.productid = inv_schedule.productid
    INNER JOIN inv_batch on inv_batch.batchid =  inv_schedule.batchid
    INNER JOIN sys_group on sys_group.groupid = inv_batch.groupid
    WHERE inv_schedule.batchid = :BATCHID
    LIMIT :START
    ";
    
    protected static $SQL_CHECK_BRANCH_GROUP = "
        SELECT groupid from inv_batch
        WHERE storeid = :STOREID AND GROUPID = :GROUPID
        AND recordstatus != 3
    ";

    protected static $SQL_LOAD_CHILD_TOTAL = "SELECT count(*) AS total FROM inv_schedule WHERE batchid = :BATCHID";
    
    protected static $ORA_LOAD_STORES = "
    SELECT 
    DISTINCT ORGANIZATION_ID,ORGANIZATION_NAME 
    FROM 
    DWT_DIM_EX_SITE 
    WHERE 
    OPERATION_STATUS = 'OPEN' 
    AND
    SITE_STATUS = 'ACTIVE'
    AND
    SITE_TYPE = 'CITI_STORE'
    ORDER BY ORGANIZATION_NAME
    ";
    protected static $SQL_LOAD_BATCH2 = " SELECT * FROM inv_batch WHERE batchid = :BATCHID";
    protected static $LOAD_SCHEDULED_PRODUCTS = "SELECT * FROM `inv_schedule` WHERE batchid = 1 and statusid = 1";
    
    protected static $SQL_CANCEL_BATCH = "
    UPDATE `inv_batch` set recordstatus = 3, canceldate = NOW() where batchid = :BATCHID;
    UPDATE `inv_schedule` set statusid = 3 where batchid = :BATCHID and statusid = 1;
    ";
    
    // UPDATE `sys_group` set statusid = 3,isUsed = 1 where groupid = (select groupid from inv_batch where batchid = :BATCHID);

    public static function CHECK_BRANCH_GROUP(){

        $parameter = array(":STOREID" => $_POST['storeid'],":GROUPID" => $_POST['groupid']);
        echo json_encode(CicModel::LOAD_SQL_DATA(GroupController::$SQL_CHECK_BRANCH_GROUP,$parameter));

     }

    public static function LOAD_BRANCH(){
        
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
            $sth = $conn->prepare(GroupController::$ORA_LOAD_STORES);
            $sth->execute();
            $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($datacontainer);
            
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
        
    }
    
    public static function CANCEL_BATCH(){
        $pbatchid = $_POST['pbatchid'];
        $parameter = array(":BATCHID" => $pbatchid);
        echo json_encode(CicModel::MANAGE_SQL_DATA(GroupController::$SQL_CANCEL_BATCH,$parameter));
    }
    
    public static function UPDATE_BATCH(){

       CicModel::MANAGE_SQL_DATA(CicModel::$SQL_GROUP_STATUS_UPDATE,null);

    }
    
    public static function LOAD_BATCH(){

                //9999999 = admin empid
        //to identify the admin user and show all data in group

        if($_SESSION['emp_id'] === '9999999'){
            $parameter = array(":USER" => null);
        }else{
            $parameter = array(":USER" => $_SESSION['emp_id']);
        }

        // GroupController::UPDATE_BATCH();
        echo json_encode(GroupController::APP_MODEL(GroupController::$SQL_LOAD_BATCH,$parameter));

    }
    
    public static function LOAD_CHILD(){
        $pbatchid = $_POST['pbatchid'];
        $parameter = array(":BATCHID" => $pbatchid,":START" => $_POST['start']);
        $totaldata = GroupController::APP_MODEL(GroupController::$SQL_LOAD_CHILD_TOTAL,array(":BATCHID" => $pbatchid));
        $data = GroupController::APP_MODEL(
            "
            SELECT 
            barcode,scheduledate,groupname,remarks,
            CASE WHEN onhand IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM onhand))) END AS onhand,
            CASE WHEN sellout IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellout))) END AS sellout,
            CASE WHEN receiving IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM receiving))) END AS receiving,
            CASE WHEN adjustment IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adjustment))) END AS adjustment,
            CASE WHEN sellingarea IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellingarea))) END AS sellingarea,
            CASE WHEN warehousearea IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM warehousearea))) END AS warehousearea,
            CASE WHEN variance IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM variance))) END AS variance,
            CASE WHEN adj_issue IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_issue))) END AS adj_issue,
            CASE WHEN adj_receipt IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_receipt))) END AS adj_receipt,
            CASE WHEN rcv_ir IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_ir))) END AS rcv_ir,
            CASE WHEN rcv_er IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_er))) END AS rcv_er
            FROM inv_schedule INS
            INNER JOIN inv_product INP on INP.productid = INS.productid
            INNER JOIN inv_batch INB on INB.batchid =  INS.batchid
            INNER JOIN sys_group SG on SG.groupid = INB.groupid
            WHERE INS.batchid = ".$_POST['pbatchid']." 
            LIMIT ".$_POST['start']."
            ",null
        );
        $json_data = array(
            "draw"            => intval(1),   
            "recordsTotal"    => $totaldata[0]['total'],  
            "recordsFiltered" => $totaldata[0]['total'],
            "data"            => $data
        );
        
        echo json_encode($json_data);
        
    }
    
    public static function INITIALIZE_VARIABLES(){
        GroupController::$pcountperyear = $_POST['pcountperyear'];
        GroupController::$pgroupid = $_POST['pgroupid'];
        GroupController::$precordstatus = $_POST['precordstatus'];
        GroupController::$puser = $_POST['puser'];
        GroupController::$pfilereference = $_POST['pfilereference'];
        $repeatevery = 12/GroupController::$pcountperyear;
        $datenow = $_POST['pstartdate'];
        
        GroupController::$pbatchdate = date("Y-m-d");
        $datefrom = $datenow;
        $dateto;
        for ($i = 1; $i <= GroupController::$pcountperyear; $i++ ){
            $dateto = date('Y-m-d', strtotime("+".$repeatevery." months", strtotime($datefrom)));
            $dateto = date('Y-m-d', strtotime("-1 days", strtotime($dateto)));
            array_push(GroupController::$datevariable,array("datefrom"=>$datefrom,"dateto"=>$dateto));
            $datefrom = $dateto;
            $datefrom = date('Y-m-d', strtotime("+1 days", strtotime($dateto)));
        }
    }

    public static function INITIALIZE_VARIABLES_CUSTOM(){

        GroupController::$pcountperyear = $_POST['pcountperyear'];
        GroupController::$pgroupid = $_POST['pgroupid'];
        GroupController::$precordstatus = $_POST['precordstatus'];
        GroupController::$puser = $_POST['puser'];
        GroupController::$pfilereference = $_POST['pfilereference'];
        $datenow = $_POST['pstartdate'];
        $dateto = $_POST['penddate'];
        
        GroupController::$pbatchdate = date("Y-m-d");
        $datefrom = $datenow;
        $dateto_temp;

        $date1 = new DateTime($_POST['pstartdate']);
        $date2 = new DateTime($_POST['penddate']);
        $interval = $date1->diff($date2);
        // get the whole number here
        $days_per_set = $interval->days/GroupController::$pcountperyear;
        
        for ($i = 1; $i <= GroupController::$pcountperyear; $i++ ){
            
            $dateto_temp = date('Y-m-d', strtotime("+".floor($days_per_set)." days", strtotime($datefrom)));
            array_push(GroupController::$datevariable,array("datefrom"=>$datefrom,"dateto"=>$dateto_temp));

            $datefrom = $dateto_temp;
            $datefrom = date('Y-m-d', strtotime("+1 days", strtotime($dateto_temp)));
        }

    }
    
    //you called this functiomn without passing a parameter
    public static function GET_INSERTED_DATA($storeid = '0',$batchid = '0'){
        
        $datacontainer = array();
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare(
            
            "
            SELECT BARCODE,LEGACYBARCODE FROM inv_product IP
            INNER JOIN inv_batch IB ON IB.BATCHID = IP.BATCHID
            WHERE IB.STOREID = :STOREID AND IB.BATCHID = :BATCHID
            "
        );
        
        $parameter = array(":STOREID" => $storeid,":BATCHID" => $batchid);
        
        $ctr = 0;
        
        if($sth->execute($parameter)){
            $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
            $barcode = array();
            $legacybarcode = array();
            $paramlegacybarcode = '';
            $parambarcode = '';
            
            // print_r($datacontainer);
            
            foreach ($datacontainer as $item) {
                $barcode[] = $item['BARCODE'];
                $legacybarcode[] = $item['LEGACYBARCODE'];
                
                if($ctr === 500){
                    $parambarcode =  implode(",",$barcode);
                    $paramlegacybarcode =  implode(",",$legacybarcode);
                    GroupController::GET_PRODUCT_DW_DATA($parambarcode,$storeid,$paramlegacybarcode);
                    $barcode = array();
                    $legacybarcode = array();
                    $ctr = 0;
                    $parambarcode = '';
                }
                
                $ctr = $ctr + 1;
            }
            
            if (count($barcode) != 0){
                $parambarcode =  implode(",",$barcode);
                $paramlegacybarcode =  implode(",",$legacybarcode);
                GroupController::GET_PRODUCT_DW_DATA($parambarcode,$storeid,$paramlegacybarcode);
                $barcode = array();
                $legacybarcode = array();
                $ctr = 0;
                $parambarcode = '';
            }
            // print_r($barcode);
            
            // echo json_encode($parambarcode);
            
        }else{
            print_r($sth->errorInfo());
        }
    }
    
    public static function GET_PRODUCT_DW_DATA($parambarcode,$storeid,$legacybarcode){
        
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
            $sth = $conn->query("
            SELECT 
            MTC.INVENTORY_ITEM_ID AS INVENTORYITEMID,
            '87' AS ORGANIZATION_ID,
            MTP.BARCODE,
            MTP.LEGACYBARCODE,
            MTP.DESCRIPTION,   
            '0' SUPPLIERID,
            'SUPPNAME' SUPPLIERNAME,
            MTC.DIVISIONID,
            MTC.DIVISIONNAME,
            MTC.DEPARTMENTID,
            MTC.DEPARTMENTNAME,
            MTC.CATEGORYID,
            MTC.CATEGORYNAME,
            (SELECT TO_CHAR(SYSDATE, 'YYYY-MM-DD') as datenow FROM dual) EXTRACTEDDATE,
            MTC.BRANDID,
            MTC.BRANDNAME,
            MTC.ITEMTYPE,
            MTC.ITEMTYPENAME
            from xxch_mtlcategories MTC
            left join XXCH_MTLCAT_PRODUCT_V MTP ON MTP.INVENTORY_ITEM_ID = MTC.INVENTORY_ITEM_ID 
            where MTP.barcode IN (".$parambarcode.") 
            AND organization_id = 87 
            ");

            if($sth->execute()){
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($datacontainer);
                GroupController::UPDATE_DATA($datacontainer);
                
            }else{
                print_r($sth->errorInfo());
            }
            
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
        
    }
    
    public static function UPDATE_DATA($data){
        
        $paramarray = $data;
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $insert_values = array();
        $ctr = 0;
        
        // print_r($paramarray);
        foreach($paramarray as $d){

            $insert_values = array_merge($insert_values, array_values($d));
            $question_marks[] = '('  . GroupController::placeholders('?', sizeof($d),',','update') . ')';
            
            if($ctr === 1999){
                GroupController::sqlexcution($db,$insert_values,$question_marks,0,'update');   
                unset($question_marks);
                $insert_values = array();
                $ctr = 0;
            }
            
            $ctr = $ctr + 1;
        }
        
        // insert if there is still data in the array
        if (count($insert_values) != 0){
            GroupController::sqlexcution($db,$insert_values,$question_marks,0,'update');
            $insert_values = array();
            unset($question_marks);
            $ctr = 0;
        }
        
        $db->commit();
        
    }
    
    
    public static function SCHEDULE_PRODUCTS(){
        
        $SCHEDULED_DATE = array();
        $SCHEDULED_DATE_DAYS = array();
        $PRODUCT_DATA = array();
        $PRODUCT_COUNT = 0;
        $tempcontainer = array();
        $PRODUCT_PER_DAY = 0;
        $ctr = 0;
        $pctr = 0; /*Product ctr*/
        $bctr = 0; /*Schedule Batch Counter*/
        $PRODUCT_DATA = GroupController::APP_MODEL(GroupController::$SQL_PRODUCT,array(":batchid" => GroupController::$batchid));
        $PRODUCT_COUNT = count($PRODUCT_DATA);
        
        foreach (GroupController::$datevariable as $key => $value) {
            $tempcontainer = GroupController::APP_MODEL(GroupController::$SQL_ACTIVE_DATE,array(":datefrom" => $value["datefrom"],":dateto"=>$value["dateto"]));
            array_push($SCHEDULED_DATE,$tempcontainer);
            array_push($SCHEDULED_DATE_DAYS,array("batch".$ctr => count($tempcontainer)));
            $tempcontainer = array();
            $ctr = $ctr + 1;
        }
        
        //access through array using index
        // echo $PRODUCT_DATA[1]['barcode'];
        
        $batchcount = 1;
        
        for ($a = 0; $a < count($SCHEDULED_DATE); $a++) {
            $PRODUCT_PER_DAY = $PRODUCT_COUNT/count($SCHEDULED_DATE[$a]);
            $PRODUCT_PER_DAY = intval($PRODUCT_PER_DAY);
            $pctr = 0;
            shuffle($PRODUCT_DATA);
            foreach ( $SCHEDULED_DATE[$a] as $dates ){
                foreach ( $dates as $key=>$val ){
                    $parameter = array();
                    $PRODUCT_PER_DAY = intval($PRODUCT_PER_DAY);
                    for ($i = 0; $i < $PRODUCT_PER_DAY; $i++) {
                        $parameter = array(":productid" => $PRODUCT_DATA[$pctr]['productid'],":batchid" => GroupController::$batchid,":countbatch" => $batchcount,":scheduledate" => $val,":warehousearea" =>0,":sellingarea" => 0,":statusid" => 1);
                        GroupController::APP_MODEL(GroupController::$SQL_CREATE_SCHEDULE,$parameter);
                        $pctr++;
                    }
                    
                }
            }
            // print_r($parameter);
            //Check if there is product that is not scheduled yet
            //I used array index and last total items to compare
            if($pctr != count($PRODUCT_DATA) - 1){
                GroupController::SCHEDULE_REMAINING_PRODUCTS(array_slice($PRODUCT_DATA,$pctr,count($PRODUCT_DATA)),$SCHEDULED_DATE[$a],$batchcount);
            }
            $batchcount++;
        }
        
        GroupController::GET_INSERTED_DATA($_POST['pstoreid'],GroupController::$batchid);
        
    }
    
    public static function SCHEDULE_REMAINING_PRODUCTS($product,$datelist,$batchcount){
        $productindex = 0;
        foreach ( $datelist as $dates ){
            foreach ( $dates as $key=>$val ){
                if($productindex < count($product)){
                    $parameter = array(":productid" => $product[$productindex]['productid'],":batchid" => GroupController::$batchid,":countbatch" => $batchcount,":scheduledate" => $val,":warehousearea" =>0,":sellingarea" => 0,":statusid" => 1);
                    GroupController::APP_MODEL(GroupController::$SQL_CREATE_SCHEDULE,$parameter);
                }
                $productindex ++;
            }
        }
    }
    
    public static function UPLOAD_FILE(){

        $name = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $stmt = $db->prepare("
        
        INSERT IGNORE INTO inv_batch (batchname,batchdate,user,recordstatus,groupid,filereference,countperyear,storeid,storename,startdate,enddate) 
        VALUES((SELECT CONCAT(variablecharacter, '-000', nextnumber) FROM res_variable WHERE variableid = 1),?,?,?,?,?,?,?,?,?,?);
        UPDATE
        res_variable
        SET
        nextnumber = nextnumber + 1
        WHERE variableid = 1;
        
        UPDATE
        sys_group
        SET
        isUsed = 1,
        statusid = 2
        WHERE groupid = ".GroupController::$pgroupid.";
        
        ");
        
        $stmt->bindValue(1, GroupController::$pbatchdate, PDO::PARAM_STR);
        $stmt->bindValue(2, $_SESSION['emp_id'], PDO::PARAM_INT);
        $stmt->bindValue(3, GroupController::$precordstatus, PDO::PARAM_INT);
        $stmt->bindValue(4, GroupController::$pgroupid, PDO::PARAM_INT);
        $stmt->bindValue(5, GroupController::$pfilereference, PDO::PARAM_STR);
        $stmt->bindValue(6, GroupController::$pcountperyear, PDO::PARAM_INT);
        $stmt->bindValue(7, $_POST['pstoreid'], PDO::PARAM_INT);
        $stmt->bindValue(8, $_POST['pstorename'], PDO::PARAM_STR);
        $stmt->bindValue(9, $_POST['pstartdate'], PDO::PARAM_STR);
        $stmt->bindValue(10, $_POST['penddate'], PDO::PARAM_STR);
        
        
        if ($stmt->execute()) 
        { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            GroupController::$batchid = $db->lastInsertId();
            $path = $_SERVER['DOCUMENT_ROOT'].'/cicmonitoring_v2/files/';
            if (!empty($name)) { move_uploaded_file($tmp_name, $path.$name); }
            GroupController::UPLOAD_DATA(GroupController::$pfilereference, GroupController::$batchid);
        } 
        else 
        {
            //display error information
            //var_dump($rs);
            print_r($stmt->errorInfo());
        }
    }
    
    public static function UPLOAD_DATA($filepath,$batchid){
        GroupController::$TOTAL_PRODUCTS = 0;
        require('spreadsheet/SpreadsheetReader.php');
        $Reader = new SpreadsheetReader($_SERVER['DOCUMENT_ROOT'].'/cicmonitoring_v2/'.$filepath);
        $Sheets = $Reader -> Sheets();
        $paramarray = array();
        
        $doc_ctr = 0;
        foreach ($Sheets as $Index => $Name)
        {
            $Reader -> ChangeSheet($Index);
            foreach ($Reader as $Row)
            {
                
                if($doc_ctr != 0){
                    if($Row[2] != ''){
                        array_push($paramarray,array($Row[0], $Row[1], $Row[2]));
                        GroupController::$TOTAL_PRODUCTS = GroupController::$TOTAL_PRODUCTS + 1;
                    }
                }

                $doc_ctr =  $doc_ctr + 1;
            }
        }
        
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
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
            $question_marks[] = '('  . GroupController::placeholders('?', sizeof($d),',','upload') . ')';
            if($ctr === 1999){
                GroupController::sqlexcution($db,$insert_values,$question_marks,$batchid,'upload');   
                unset($question_marks);
                $insert_values = array();
                $ctr = 0;
            }
            
            $ctr = $ctr + 1;
        }
        
        // insert if there is still data in the array
        if (count($insert_values) != 0){
            GroupController::sqlexcution($db,$insert_values,$question_marks,$batchid,'upload');
            $insert_values = array();
            unset($question_marks);
            $ctr = 0;
        }
        
        try {
            $db->commit();
        } catch (PDOException $e) {
            $dbh->rollBack();
        }
        
    }
    
    public static function sqlexcution($db,$data_to_insert,$qm,$batchid,$identifier){
        if($identifier === 'upload'){
            $stmt = $db->prepare("
            INSERT IGNORE INTO inv_product (barcode,legacybarcode,`description`,inputdate,`groups`,batchid)
            VALUES ".implode(',', $qm)."
            ");
        }else if ($identifier === 'update'){
            
            $stmt = $db->prepare("
            INSERT IGNORE INTO inv_product_dw (inventory_item_id,organization_id,barcode,legacybarcode,`description`,supplierid,suppliername,divisionid,divisionname,departmentid,departmentname,categoryid,categoryname,extracteddate,brandid,brandname,itemtype,itemtypename)
            VALUES ".implode(',', $qm)."
            ON DUPLICATE KEY 
            UPDATE 
            organization_id = values(organization_id),
            barcode = values(barcode),
            legacybarcode = values(legacybarcode),
            `description` = values(`description`),
            supplierid = values(supplierid),
            suppliername = values(suppliername),
            divisionid = values(divisionid),
            divisionname = values(divisionname),
            departmentid = values(departmentid),
            departmentname = values(departmentname),
            categoryid = values(categoryid),
            categoryname = values(categoryname),
            extracteddate = values(extracteddate),
            brandid = values(brandid),
            brandname = values(brandname),
            itemtype = values(itemtype),
            itemtypename = values(itemtypename)
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
            echo "<br>";
            echo $identifier;
        }
    }
    
    public static function placeholders($text, $count=0, $separator=",",$identifier){
        
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
    public static function APP_MODEL($sql,$parameter){
        $datacontainer = array();
      
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);
        if($sth->execute($parameter)){
            
        }else{
            print_r($sth->errorInfo());
        }
        $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
        // echo json_encode($datacontainer);
        return $datacontainer;
    }

    public static function CHECK_HOLIDAY(){

        $parameter = array(":DATEDESC" => $_POST['datedesc']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_CHECK_HOLIDAY,$parameter));

    }
}

?>