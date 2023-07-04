<?php

namespace Dashboard;
use PDO;
use stdClass;
include('AppSettings.php');
use Settings\AppSettings;
use CicModel\CicModel;

class DashboardController{

    public static function LOAD_DASHBOARD(){
        $dashboarddata = array();
        $dwdata = array();
        if($_POST['storeid'] === ''){$storeid = null;}else{$storeid = $_POST['storeid'];}
        if($_POST['groupid'] === '-1'){$groupid = null;}else{$groupid = $_POST['groupid'];}
        $parameter = array(":DATEFROM" => $_POST['datefrom'],":DATETO" => $_POST['dateto'],":STOREID" => $storeid,":GROUPID" => $groupid);
        // print_r($parameter);
        // print_r($parameter);
        $dashboarddata = CicModel::LOAD_SQL_DATA(CicModel::$SQL_DASHBOARD_GET, $parameter);
        // print_r($dashboarddata);
        echo json_encode($dashboarddata);

    }

    public static function LOAD_OPEN_DATA(){
        if($_POST['storeid'] === ''){$storeid = null;}else{$storeid = $_POST['storeid'];}
        $parameter = array(":STOREID" => $storeid);
        // print_r($parameter);
        $data = CicModel::LOAD_ORA_DATA(CicModel::$ORA_INVENTORY_GET, $parameter,'dw');
        return $data;
    }

    public static function LOAD_CURRENTCOUNT(){

        if($_POST['storeid'] === ''){$storeid = null;}else{$storeid = $_POST['storeid'];}
        if($_POST['groupid'] === '-1'){$groupid = null;}else{$groupid = $_POST['groupid'];}

        if($storeid != '-1'){
            $parameter = array(":DATEFROM" => date("Y-m-d"),":DATETO" => date("Y-m-d"),":STOREID" => $storeid,":GROUPID" => $groupid);
            echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_DASHBOARD_CURRENTCOUNT, $parameter ));

        }else{
            // ,":EMP_ID" => $_SESSION['emp_id']
            $parameter = array(":DATEFROM" => date("Y-m-d"),":DATETO" => date("Y-m-d"),":STOREID" => $storeid,":GROUPID" => $groupid);
            echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_DASHBOARD_CURRENTCOUNT_STORE, $parameter ));
        }

    }

    public static function LOAD_LEDGER(){

        $parameter = array(":STOREID" => $_POST['storeid'],":BARCODE" => $_POST['barcode']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_DASHBOARD_LEDGER, $parameter ));

    }

}

?>