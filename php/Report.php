<?php

namespace Report;
use Settings\AppSettings;
use PDO;
use SpreadsheetReader;
use stdClass;
use oci_connect;
use CicModel\CicModel;

class Report{

	public static function PRODUCT_SCHEDULE(){

		if($_POST['statusid'] === '-1'){ $statusid = null; }else{ $statusid = $_POST['statusid']; }
		if($_POST['groupid'] === '-1'){ $groupid = null; }else{ $groupid = $_POST['groupid']; }
		
		$parameter = array(":DATEFROM" => $_POST['datefrom'],":DATETO" => $_POST['dateto'],":GROUPID" => $groupid,":STOREID" => $_POST['storeid'],":STATUSID" => $statusid);
		echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_PRODUCT_SCHEDULE_GET,$parameter));

	}

	public static function SCHEDULED_GROUP(){

		if($_POST['statusid'] === '-1'){ $statusid = null; }else{ $statusid = $_POST['statusid']; }
		if($_POST['groupid'] === '-1'){ $groupid = null; }else{ $groupid = $_POST['groupid']; }
		
		$parameter = array(":GROUPID" => $groupid,":STOREID" => $_POST['storeid'],":STATUSID" => $statusid);
		echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_SCHEDULED_GROUP_GET,$parameter));

	}

}