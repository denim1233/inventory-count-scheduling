<?php

namespace User;
use PDO;
use stdClass;
use Settings\AppSettings;
use Assignment\Assignment;
use CicModel\CicModel;
date_default_timezone_set('Asia/Manila');




class UserController{

    public static function CHECK_USER_PASSWORD(){

        $val = $_POST['table_data'];
        $parameter = array(":USERDIVISIONID" => $_SESSION['userdivisionid'],":PASSWORD" => $val['oldpassword'] );
        CicModel::$DATA_CONTAINER = CicModel::LOAD_SQL_DATA(CicModel::$SQL_CHECK_PASSWORD, $parameter );
        if(count(CicModel::$DATA_CONTAINER) === 1){
                UserController::CHANGE_USER_PASSWORD();
        }else{
                $ob =  new stdClass();
                $ob->status = '0';
                $returndata = json_encode($ob);
                echo $returndata;
        }
    }

    public static function CHANGE_USER_PASSWORD(){

        $parameter = array(":USER_UPDATED" =>  $_SESSION['emp_id'],":PASSWORD" => $_POST['table_data']['newpassword'],":DATE_UPDATED" => date("Y-m-d h:i:sa"),":USERDIVISIONID" => $_SESSION['userdivisionid']);
        echo json_encode(CicModel::MANAGE_SQL_DATA(CicModel::$SQL_CHANGE_PASSWORD,$parameter));

    }

    public static function LOAD_POS_USERS(){
        $parameter = array(":ASSIGNMENT" => $_POST['assignment']);
        echo json_encode(CicModel::LOAD_ORA_DATA(CicModel::$ORA_POS_USERS_GET,$parameter,'prodm'));
    }

    public static function LOAD_HO_USERS(){

        $storeName = '';
        //if($_POST['assignment'] === 'SAN CARLOS - NEGROS'){
         //       $storeName = 'SC NEGROS';
        //}else 
        if($_POST['assignment'] === 'SAN JOSE - NUEVA ECIJA'){
                $storeName = 'SAN JOSE NUEVA ECIJA';
        }else{
               $storeName  =  $_POST['assignment'];
        }
        $parameter = array(":ASSIGNMENT" => $storeName);


        $parameter1 = array(":STOREID" => $_POST['storeid'],":DEPARTMENTS" => $_POST['departments']);
        $container1 = CicModel::LOAD_SQL_DATA(CicModel::$SQL_USERS_GET,$parameter1);

        $parameter2 = array(":DEPARTMENTS" => $_POST['departments'],":ASSIGNMENT" => $storeName,":STOREID" => $_POST['storeid']);

        // print_r($parameter2);
        $container2 = CicModel::LOAD_ORA_DATA(CicModel::$ORA_HO_USERS_GET,$parameter2,'prodm');

        $mysql_employess = array();

          foreach ($container1 as $item) {
                $mysql_employess[] = $item['employee_number'];
            }

        $employees_not_exists_in_mysql = array_filter($container2, function ($var) use ($mysql_employess) {

            return (!in_array($var['employee_number'], $mysql_employess));

        });

        $merged_data = array_merge($employees_not_exists_in_mysql,$container1);

        echo json_encode($merged_data);
    }

    public static function AUTHENTICATE_LOGIN(){
        $user ;
        $val = $_POST['table_data'];
        $parameter = array(":USERNAME" => $val['username'],":PASSWORD" => $val['password']);
        $user = CicModel::LOAD_SQL_DATA(CicModel::$SQL_AUTHENTICATE_LOGIN,$parameter);
        if(count($user) === 1){
            $_SESSION['departmentid'] = '0';
            $_SESSION['departmentname'] = $user[0]['STORE_NAME'];   
            $_SESSION['personnelname'] = $user[0]['FULL_NAME'];
            $_SESSION['roleid'] = '4';
            $_SESSION['emp_id'] = $user[0]['emp_id'];
            $_SESSION['rolename'] = $user[0]['rolename'];
            $_SESSION['department'] = $user[0]['department'];
            $_SESSION['userdivisionid'] = $user[0]['userdivisionid'];


            $ob =  new stdClass();
            $ob->requeststatus = 1;
            $ob->roleid = '4';
            $returndata = json_encode($ob);
            echo $returndata;

        }else{
            $ob =  new stdClass();
            $ob->requeststatus = 0;
            $returndata = json_encode($ob);
            echo $returndata;
           session_unset();
        }
    }

    public static function LOAD_USER(){
        $ob =  new stdClass();
        $ob->requeststatus = 1;
        $ob->departmentname = $_SESSION['departmentname'];
        $ob->departmentid = $_SESSION['departmentid'];
        $ob->personnelname = $_SESSION['personnelname'];
        $ob->department = $_SESSION['department'];
        $ob->rolename = $_SESSION['rolename'];
        $ob->empid = $_SESSION['emp_id'];
        $ob->roleid = $_SESSION['roleid'];
        $returndata = json_encode($ob);
        echo $returndata;
    }

    public static function LOGOUT_USER(){
        session_destroy();
        $ob =  new stdClass();
        $ob->requeststatus = 1;
        $returndata = json_encode($ob);
        echo $returndata;
    }

    public static function LOAD_USER_DIVISION(){
        $parameter = array(":USERDIVISIONID" => $_POST['userdivisionid']);
        echo json_encode(CicModel::LOAD_SQL_DATA(CicModel::$SQL_USERS_DIVISION_GET,$parameter));
    }

    public static function INSERT_USER_DIVISION(){

        $val = array();
        $val = $_POST['table_data'];
        $divisionid = implode(",",$val['divisionid']);
        $parameter = array();
        $sql = CicModel::$SQL_USERS_DIVISION_INSERT;

        $parameter = array(":emp_id" => $val['emp_id'],":divisionid" =>  $divisionid,":divisionname" => $val['divisionname'],":storeid" => $val['storeid'],":username" => $val['username'],":password" => $val['password'],":full_name" => $val['full_name'],":store_name" => $val['store_name'],":department" => $val['department'],":rolename" => $val['rolename'],":job_code" => $val['job_code'],":date_created" => date("Y-m-d h:i:sa"),":user_created" => $_SESSION['emp_id']);
    
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);
            $ob =  new stdClass();

        if ($sth->execute($parameter)) { 
            $ob->requeststatus = 'data successfully saved!';
            $ob->success = 'true';
            $ob->id = $db->lastInsertId();
            $ob->action = 'update';
            $returndata = json_encode($ob);
            echo $returndata;
        } 
        else {
            $ob->requestid = '0';
            $ob->requeststatus = $sth->errorInfo();
            $ob->success = 'false';
            $ob->action = 'update';
            $returndata = json_encode($ob);
            echo $returndata;
        }

    }

     public static function UPDATE_USER_DIVISION(){

// userdivisionid

        $val = array();
        $val = $_POST['table_data'];
        $parameter = array();
        $sql = CicModel::$SQL_USERS_DIVISION_UPDATE;
        $parameter = array(":STORE_NAME" => $val['store_name'],":STOREID" => $val['storeid'],":STATUSID" => $val['statusid'],":USERNAME" => $val['username'],":PASSWORD" => $val['password'], ":DIVISIONID" => implode(",",$val['divisionid']),":USERDIVISIONID" => $val['userdivisionid'],":DIVISIONNAME" => $val['divisionname'],":DATE_UPDATED" => date("Y-m-d h:i:sa"),":USER_UPDATED" => intval($_SESSION['emp_id']));
        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);
        $ob =  new stdClass();

         // print_r($parameter);

        if ($sth->execute($parameter)) { 
            $ob->requeststatus = "Users Account has been successfully updated!";
            $ob->success = 'true';
            $ob->action = 'update';
            $returndata = json_encode($ob);
            echo $returndata;
        } 
        else {
            $ob->requestid = '0';
            $ob->requeststatus = $sth->errorInfo();
            $ob->success = 'false';
            $ob->action = 'update';
            $returndata = json_encode($ob);
            echo $returndata;
        }
    }

    public static function CHECK_IF_EXIST($action){
        $val = $_POST['table_data'];
		$sqlInsert = "SELECT username FROM sys_users_division WHERE username = :username and storeid = :storeid";
		$sqlUpdate = "SELECT username FROM sys_users_division WHERE username = :username and storeid = :storeid and userdivisionid != :userdivisionid";
        $parameter = array();
        
    	if($action === 'insert'){
    		$sql = $sqlInsert;
        	$parameter = array(":storeid" => $val['storeid'],":username" => $val['username']);
    	}else{
    		$sql = $sqlUpdate;
        	$parameter = array(":storeid" => $val['storeid'],":username" => $val['username'],":userdivisionid" => $val['userdivisionid']);
    	}


        $val = array();
        $val = $_POST['table_data'];


        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);
        $ob =  new stdClass();

        if ($sth->execute($parameter)) { 

        	$datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);

        	if(count($datacontainer) === 0){
            	return '0';
        	}else{
            	return '1';
        	}
        } 
        else {
            return 'error';
        }
    }

    public static function USER_MANAGERECORD(){

    	// echo CHECK_IF_EXIST('update');

        if(intval($_POST['table_data']['userdivisionid']) === 0){

        	if(UserController::CHECK_IF_EXIST('insert') === '0'){
            	UserController::INSERT_USER_DIVISION();
        	}else{
        		$ob =  new stdClass();
				$ob->requestid = '0';
				$ob->requeststatus = 'Username already exists';
				$returndata = json_encode($ob);
				echo $returndata;
        	}

        }else{
        	if(UserController::CHECK_IF_EXIST('update') === '0'){
            	UserController::UPDATE_USER_DIVISION();
        	}else{
        		$ob =  new stdClass();
        		$ob->requestid = '0';
	            $ob->requeststatus = 'Username already in the current store';
	            $returndata = json_encode($ob);
	            echo $returndata;
        	}
        }
    }
}

?>