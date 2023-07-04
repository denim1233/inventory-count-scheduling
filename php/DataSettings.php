<?php


namespace DataSys;
use PDO;
use stdClass;
use Settings\AppSettings;
use CicModel\CicModel;
date_default_timezone_set('Asia/Manila');


class DataSettings{
    
    protected static $SQL_DEACTIVATE_DATE = "UPDATE sys_activedate set recordstatus = 5 where datedesc = :HOLIDAYDATE";
    protected static $SQL_HOLIDAY_GET = "SELECT * FROM sys_holiday INNER JOIN sys_status ON sys_status.StatusId = sys_holiday.recordstatus WHERE sys_holiday.RECORDSTATUS = 4";
    protected static $SQL_GROUP_GET = "
        SELECT SG.groupid,SG.groupname, 
        CASE WHEN SS2.statusname = 'Submitted' THEN 'Posted' ELSE SS2.statusname END AS statusname,
        SG.recordstatus,
        SG.isUsed
        FROM 
            sys_group SG
        INNER JOIN sys_status SS2 ON SS2.statusid = SG.statusid
        WHERE 
            SG.RECORDSTATUS = 4
        AND 
            SG.ISUSED = IFNULL(:ISUSED,SG.ISUSED)
        AND 
            SG.statusid in(1,2)
        ORDER BY GROUPID DESC
    ";

    protected static $SQL_GROUP_GET_V2 = "
        SELECT SG.groupid,SG.groupname, 
        CASE WHEN SS2.statusname = 'Submitted' THEN 'Posted' ELSE SS2.statusname END AS statusname,
        SG.recordstatus,
        SG.isUsed
        FROM 
            sys_group SG
        INNER JOIN sys_status SS2 ON SS2.statusid = SG.statusid
        WHERE 
            SG.RECORDSTATUS = 4
        AND USER_CREATED  = :EMP_ID
        ORDER BY SG.groupid DESC
    ";

    protected static $SQL_ROLE_GET = "SELECT * FROM  sys_role WHERE RECORDSTATUS = 4";
    protected static $SQL_GROUP_INSERT = "
                        INSERT IGNORE INTO sys_group(GROUPNAME,RECORDSTATUS,STATUSID,DATE_CREATED,USER_CREATED) 
                        VALUES (:GROUPNAME,:RECORDSTATUS,1,:DATE_CREATED,:USER_CREATED)
                        ";
    protected static $SQL_HOLIDAY_INSERT = "
    INSERT IGNORE INTO sys_holiday(HOLIDAYNAME,HOLIDAYDATE,RECORDSTATUS,DATE_CREATED,USER_CREATED) 
    VALUES(:HOLIDAYNAME,:HOLIDAYDATE,:RECORDSTATUS,:DATE_CREATED,:USER_CREATED)
    "
    ;

    protected static $SQL_GROUP_UPDATE = "UPDATE sys_group SET GROUPNAME = :GROUPNAME, RECORDSTATUS = :RECORDSTATUS, DATE_UPDATED = :DATE_UPDATED, USER_UPDATED = :USER_UPDATED WHERE GROUPID = :GROUPID";
    protected static $SQL_HOLIDAY_UPDATE = "
    UPDATE sys_holiday 
        SET HOLIDAYNAME = :HOLIDAYNAME, 
        HOLIDAYDATE = :HOLIDAYDATE, 
        RECORDSTATUS = :RECORDSTATUS,
        DATE_UPDATED = :DATE_UPDATED,
        USER_UPDATED = :USER_UPDATED
        WHERE HOLIDAYID = :HOLIDAYID
        ";
        
    protected static $SQL_CATEGORY_GET = "SELECT * FROM sys_category WHERE RECORDSTATUS = 4";
    protected static $SQL_BRANCH_GET = "SELECT * FROM sys_branch WHERE RECORDSTATUS = 4";
    protected static $ORA_SUPPLIER_GET = "SELECT VENDORID,VENDOR_NAME FROM DWT_DIM_SUPPLIER";

    //SUPPLIER IS DIFFERENT FROM THE BASE DESIGN OF CIC
    //HARD TO REVERSE BECAUSE THE CHANGES INCLUDES THE DATABASE IMPORTATION

    protected static $ORA_SUPPLIER_GET_2 = "SELECT SEGMENT1,VENDOR_NAME FROM DWT_DIM_SUPPLIER";

    protected static $ORA_CATEGORY_GET = "SELECT DISTINCT CATEGORYID,CATEGORYNAME FROM DWT_EX_PRODUCT_CAT";
    protected static $ORA_BRAND_GET = "SELECT DISTINCT BRANDID,BRANDNAME FROM DWT_EX_PRODUCT_CAT";
    protected static $ORA_GET_DIVISION = "select distinct divisionid,divisionname from DWT_DIM_PRODUCT";
    protected static $ORA_GET_DEPARTMENT = "select distinct departmentid,departmentname from  DWT_DIM_PRODUCT";
    protected static $ORA_GET_CATEGORY = "select distinct categoryid,categoryname from  DWT_DIM_PRODUCT";

    protected static $ORA_STORE_GET = "
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

    public static function DELETE_GROUP(){

        $val = $_POST['table_data'];
        $parameter = array(":GROUPID" => $val['groupid']);
        echo json_encode(CicModel::MANAGE_SQL_DATA(CicModel::$SQL_GROUP_DELETE,$parameter));

    }

    public static function DELETE_HOLIDAY(){

        $val = $_POST['table_data'];
        $parameter = array(":HOLIDAYID" => $val['holidayid'],":HOLIDAYDATE" => $val['holidaydate']);
        echo json_encode(CicModel::MANAGE_SQL_DATA(CicModel::$SQL_HOLIDAY_DELETE,$parameter));
        print_r($parameter);

    }

    public static function GET_HOLIDAY(){
        echo json_encode(DataSettings::APP_MODEL(DataSettings::$SQL_HOLIDAY_GET));
    }
    public static function GET_BRANCH(){
        echo json_encode(DataSettings::APP_MODEL(DataSettings::$SQL_BRANCH_GET));
    }

    public static function GET_GROUP(){
        $param;
        if($_POST['isused'] === ''){$param = null;}else{$param = $_POST['isused'];}
        $parameter = array(':ISUSED' => $param);
        echo json_encode(DataSettings::APP_MODEL(DataSettings::$SQL_GROUP_GET,$parameter));
    }

     public static function GET_GROUP_V2(){
        $parameter = array(':EMP_ID' => $_SESSION['emp_id']);
        echo json_encode(DataSettings::APP_MODEL(DataSettings::$SQL_GROUP_GET_V2,$parameter));
    }


    public static function GET_ROLE(){
        echo json_encode(DataSettings::APP_MODEL(DataSettings::$ORA_ROLE_GET));
    }

    public static function LOAD_BRAND(){
        echo json_encode(CicModel::LOAD_ORA_DATA(DataSettings::$ORA_BRAND_GET,null,'dw'));
    }

    public static function LOAD_DIVISION(){
        echo json_encode(CicModel::LOAD_ORA_DATA(CicModel::$ORA_DIVISION_GET,null,'prodm'));
    }

    public static function LOAD_ROLE(){
        echo json_encode(CicModel::LOAD_ORA_DATA(CicModel::$ORA_ROLE_GET,null,'prodm'));
    }

    public static function LOAD_USER_DEPARTMENT(){
        $datacontainer = CicModel::LOAD_ORA_DATA(CicModel::$ORA_USER_DEPARTMENT_GET,null,'prodm');
        echo json_encode($datacontainer);
    }

    public static function LOAD_STATUS(){
        CicModel::$PARAMETER  = array(":IDENTIFIER" => '1');
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_STATUS_GET, CicModel::$PARAMETER));
     }

    public static function GET_SUPPLIER(){

        $parameter = array(":ORG_ID" => 101);
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
                $sth = $conn->prepare(DataSettings::$ORA_SUPPLIER_GET);
                $sth->execute();
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($datacontainer);
    
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }

    public static function GET_SUPPLIER2(){

        $parameter = array(":ORG_ID" => 101);
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
                $sth = $conn->prepare(DataSettings::$ORA_SUPPLIER_GET_2);
                $sth->execute();
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($datacontainer);
    
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

    }
    
    public static function GET_CATEGORY(){
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
                $sth = $conn->prepare(DataSettings::$ORA_CATEGORY_GET);
                $sth->execute();
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($datacontainer);
    
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
    }

    public static function GET_STORE(){
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
                $sth = $conn->prepare(DataSettings::$ORA_STORE_GET);
                $sth->execute();
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($datacontainer);
    
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
        }

    public static function MANAGE_RECORD(){
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $table_data = $_POST['table_data'];
        $parameter = array();
        switch ($_POST['module_name']){
            case 'group':
                $sql = DataSettings::$SQL_GROUP_UPDATE;
                foreach ($table_data as $val ) {
                    $parameter = array(":GROUPID" => $val['groupid'],":GROUPNAME" => $val['groupname'],":RECORDSTATUS" => $val['recordstatus']);
                    $sth = $db->prepare($sql);
                    if($sth->execute($parameter)){
                            echo "success!";
                    }else{
                        print_r($sth->errorInfo());
                }
                }
            break;
            case 'holiday':
                $sql = DataSettings::$SQL_HOLIDAY_UPDATE;
                foreach ($table_data as $val ) {
                    $parameter = array(":HOLIDAYID" => $val['holidayid'],":HOLIDAYNAME" => $val['holidayname'],":HOLIDAYDATE" => $val['holidaydate'],":RECORDSTATUS" => $val['recordstatus']);
                    $sth = $db->prepare($sql);
                    if($sth->execute($parameter)){
                        echo "success!";
                    }else{
                        print_r($sth->errorInfo());
                }
                }
            break;
            default:
            break;
        }
        $db->commit();
    }

     public static function MANAGE_SETTINGS(){
        try {
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $table_data = $_POST['table_data'];
        $parameter = array();
        $statusid = 0;

        if($_POST['module_name'] === 'group'){
            $sql;
            $parameter = array();
            foreach ($table_data as $val ) {
             // $parameter = array(":GROUPNAME" => $val['groupname'],":RECORDSTATUS" => $val['recordstatus']);
             //   print_r($parameter);
                if(intval($val['groupid']) === 999999){
                    $parameter = array(":GROUPNAME" => $val['groupname'],":RECORDSTATUS" => $val['recordstatus'],":DATE_CREATED" => date("Y-m-d h:i:sa"),":USER_CREATED" => $_SESSION['emp_id']);
                    $sql = DataSettings::$SQL_GROUP_INSERT;
                }else{
                    $parameter = array(":GROUPID" => $val['groupid'],":GROUPNAME" => $val['groupname'],":RECORDSTATUS" => $val['recordstatus'],":DATE_UPDATED" => date("Y-m-d h:i:sa"),":USER_UPDATED" => $_SESSION['emp_id']);
                    $sql = DataSettings::$SQL_GROUP_UPDATE;
                }
  
                    $sth = $db->prepare($sql);
                    if($sth->execute($parameter)){
                     
                    }else{
                        print_r($sth->errorInfo());
                    }
            }
        }else if($_POST['module_name'] === 'holiday'){
            $sql;
            $parameter = array();
            foreach ($table_data as $val ) {
                if(intval($val['holidayid']) === 999999){
                    $parameter = array(":HOLIDAYNAME" => $val['holidayname'],":HOLIDAYDATE" => $val['holidaydate'],":RECORDSTATUS" => $val['recordstatus'],":DATE_CREATED" => date("Y-m-d h:i:sa"),":USER_CREATED" => $_SESSION['emp_id']);
                    $sql = DataSettings::$SQL_HOLIDAY_INSERT;
                }else{
                    $parameter = array(":HOLIDAYID" => $val['holidayid'],":HOLIDAYNAME" => $val['holidayname'],":HOLIDAYDATE" => $val['holidaydate'],":RECORDSTATUS" => $val['recordstatus'],":DATE_UPDATED" => date("Y-m-d h:i:sa"),":USER_UPDATED" => $_SESSION['emp_id']);
                    $sql = DataSettings::$SQL_HOLIDAY_UPDATE;
                }
               
                $sth = $db->prepare($sql);
                    if($sth->execute($parameter)){
                        if(intval($val['holidayid']) === 999999){
                            DataSettings::DEACTIVATE_DATE($val['holidaydate']);
                        }
                    }else{
                        print_r($sth->errorInfo());
                }
            }
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


    public static function INSERT_DATA2(){
        $val = array();
        $val = $_POST['table_data'];
        $parameter = array();

        if($_POST['module_name'] === 'group'){
            if(intval($val['groupid']) === 999999){
            
        
            }else{
                $sql = DataSettings::$SQL_GROUP_UPDATE;
                $parameter = array(":GROUPID" => $val['groupid'],":GROUPNAME" => $val['groupname'],":RECORDSTATUS" => $val['recordstatus']);
            }
        }else if($_POST['module_name'] === 'holiday'){
            if(intval($val['holidayid']) === 999999){
                $sql = DataSettings::$SQL_HOLIDAY_INSERT;
                $parameter = array(":HOLIDAYNAME" => $val['holidayname'],":HOLIDAYDATE" => $val['holidaydate'],":RECORDSTATUS" => $val['recordstatus']);
            }else{
                $sql = DataSettings::$SQL_HOLIDAY_UPDATE;
                $parameter = array(":HOLIDAYID" => $val['holidayid'],":HOLIDAYNAME" => $val['holidayname'],":HOLIDAYDATE" => $val['holidaydate'],":RECORDSTATUS" => $val['recordstatus']);
            }

         }

        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);
        $ob =  new stdClass();

        if ($sth->execute($parameter)) 
        { 
            $ob->requeststatus = 'data successfully saved';
            $ob->requeststatusid = '1';
            $ob->id = $db->lastInsertId();
            $returndata = json_encode($ob);
            if($_POST['module_name'] === 'holiday'){
                DataSettings::DEACTIVATE_DATE($val['holidaydate']);
            }
        } 
        else 
        {
            $ob->requeststatus = $sth->errorInfo();
            $ob->requeststatusid = '0';
            $ob->id = $db->lastInsertId();
            $returndata = json_encode($ob);
        }

        echo $returndata;
    }

    public static function DEACTIVATE_DATE($param){
        DataSettings::APP_MODEL(DataSettings::$SQL_DEACTIVATE_DATE,array(":HOLIDAYDATE" => $param));
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