<?php

namespace COVID19;

use Settings\AppSettings;
use PDO;
use SpreadsheetReader;
use stdClass;
use oci_connect;
ini_set('max_execution_time', 300); 


class ImportController{

    protected static $ORA_LOAD_PRODUCT = "SELECT * FROM XXCH_ALLSITE_PRODUCT_V WHERE ORGANIZATION_ID = 101";

    public static function IMPORT_PRODUCT_TO_MYSQL(){

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
                $sth = $conn->prepare(ImportController::$ORA_LOAD_PRODUCT);
                $sth->execute();
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                // echo json_encode($datacontainer);
                ImportController::UPLOAD_DATA($datacontainer);
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
        }

    public static function UPLOAD_DATA($data){
    
        $paramarray = $data;

        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $insert_values = array();
        $ctr = 0;

        foreach($paramarray as $d){
            
            $insert_values = array_merge($insert_values, array_values($d));
            $question_marks[] = '('  . ImportController::placeholders('?', sizeof($d)) . ')';

            if($ctr === 1999){
                ImportController::sqlexcution($db,$insert_values,$question_marks);   
                unset($question_marks);
                $insert_values = array();
                $ctr = 0;
            }

            $ctr = $ctr + 1;
        }

        // insert if there is still data in the array
        if (count($insert_values) != 0){
            ImportController::sqlexcution($db,$insert_values,$question_marks);
            $insert_values = array();
            unset($question_marks);
            $ctr = 0;
        }

        $db->commit();
    }

    public static function sqlexcution($db,$data_to_insert,$qm){

        $stmt = $db->prepare("
        INSERT INTO temp_product (
            INVENTORY_ITEM_ID, ORGANIZATION_ID , BARCODE, DESCRIPTION, LEGACYBARCODE,
            PRIMARY_UNIT_OF_MEASURE,VENDORCODE,VENDOR_NAME,BRANDID,BRANDNAME,DIVISIONID,
            DIVISIONNAME,DEPARTMENTID,DEPARTMENTNAME,CATEGORYID,CATEGORYNAME,SUBCATEGORYID,
            SUBCATEGORYNAME,FIXED_LOT_MULTIPLIER,STOCK_ENABLED_FLAG,ONHAND
        )
        VALUES ".implode(',', $qm)."
        ");

        if ($stmt->execute($data_to_insert))
        { 
            // $ob =  new stdClass();
            // $ob->requeststatus = 'data successfully saved';
            // $ob->batchid = $batchid;
            // $returndata = json_encode($ob);
            // echo $returndata;
        } 
        else 
        {
         print_r($stmt->errorInfo());
        // print_r($stmt);
        }
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