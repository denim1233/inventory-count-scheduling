<?php

namespace Assignment;
use PDO;
use stdClass;
use Settings\AppSettings;
use CicModel\CicModel;


class Assignment{

	public static function LOAD_POS_USERS(){

        $tempdata = array();
        $userparam;
        $tempdata = Assignment::LOAD_EXISTING_USER();
        $users = array();

        if(count($tempdata) === 0){
            $userparam = 0;
        }else{

            foreach ($tempdata as $item) {
                $users[] = $item['user_id'];
            }

         $userparam =  implode(",",$users);
        }

        // echo $existinguser;
        $parameter = array(":STOREID" => $_POST['storeid'],":ROLEID" => $_POST['roleid']);

            $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];
    
            $myServer = '192.168.3.55';
            $myDB = 'PROD';
            $oci_uname = 'appsro';
            $oci_pass = 'appsro';
            $ob =  new stdClass();
            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->prepare("
                       SELECT OU.USER_ID,OU.FIRST_NAME|| ' ' ||OU.LAST_NAME AS EMPLOYEE_NAME,OU.EMPLOYEE_NUM ,
                        OUS.STORE_ID,OS.STORE_NAME,OSR.ROLE_ID,OSR.ROLE_NAME,
                        0 AS DIVISION_ID, 'NOT SET' AS DIVISION_NAME, 0 AS ASSIGNMENTID
                        FROM OSIPOS_USER OU
                        LEFT JOIN OSIPOS_USER_STORES OUS ON OUS.USER_ID = OU.USER_ID
                        LEFT JOIN OSIPOS_STORE OS ON OS.STORE_ID = OUS.STORE_ID
                        LEFT JOIN OSIPOS_USER_ROLES OUR ON OUR.USER_ID = OU.USER_ID
                        LEFT JOIN OSIPOS_ROLES OSR ON OSR.ROLE_ID = OUR.ROLE_ID
                        WHERE STATUS = 1 AND OS.STORE_ID = :STOREID AND OUR.ROLE_ID = NVL(:ROLEID,OUR.ROLE_ID) AND OU.USER_ID NOT IN(".$userparam.")
                        GROUP BY OU.USER_ID,OU.FIRST_NAME,OU.LAST_NAME,OU.EMPLOYEE_NUM,OUS.STORE_ID,OSR.ROLE_ID,OSR.ROLE_NAME,OS.STORE_NAME
                        ORDER BY EMPLOYEE_NUM
                ");
                $sth->execute($parameter);
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);


                if(count($tempdata) != 0){
                    $datacontainer = array_merge($tempdata,$datacontainer);
                }
               echo json_encode($datacontainer);
    
            } catch(PDOException $e) {
                // echo 'ERROR: ' . $e->getMessage();
                $ob->requeststatus = 'oracle loading data failed';
                $ob->errorinfo = $e->getMessage();
                $returndata = json_encode($ob);
                echo $returndata;
            }
	}

    public static function LOAD_ASSIGNMENT(){
        $datacontainer = array();
        $parameter = array(":STOREID" => $_POST['storeid'],":ROLEID" => $_POST['roleid']);
        $datacontainer = CicModel::LOAD_SQL_DATA(CicModel::$SQL_USERS_GET,$parameter);
        echo json_encode($datacontainer);   
        // print_r(array_values($datacontainer));   
    }

    public static function LOAD_EXISTING_USER(){

        $datacontainer = array();
        if($_POST['roleid'] === ''){
            $roleid = null;
        }else{
            $roleid = $_POST['roleid'] ;
        }
        $parameter = array(":STOREID" => $_POST['storeid'],":ROLEID" =>  $roleid);
        $datacontainer = CicModel::LOAD_SQL_DATA(CicModel::$SQL_USERS_GET,$parameter);
   
        return $datacontainer;
    }

    public static function MANAGE_ASSIGNMENT(){
       $paramarray = $_POST['table_data'];

        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $db->beginTransaction();
        $insert_values = array();
        $ctr = 0;

        foreach($paramarray as $d){
            
            $insert_values = array_merge($insert_values, array_values($d));
            $question_marks[] = '('  . Assignment::placeholders('?', sizeof($d)) . ')';

            if($ctr === 1999){
                Assignment::sqlexcution($db,$insert_values,$question_marks);   
                unset($question_marks);
                $insert_values = array();
                $ctr = 0;
            }

            $ctr = $ctr + 1;
        }

        // insert if there is still data in the array
        if (count($insert_values) != 0){
            Assignment::sqlexcution($db,$insert_values,$question_marks);
            $insert_values = array();
            unset($question_marks);
            $ctr = 0;
        }

        $db->commit();


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

    public static function sqlexcution($db,$data_to_insert,$qm,$action= ''){

    // print_r($qm);
    // echo "<br>";
    //  print_r($data_to_insert);

    $ob =  new stdClass();
    $stmt = $db->prepare("
            INSERT INTO user_cic_assignment(user_id,employee_name,employee_num,store_id,store_name, role_id,role_name,division_id,division_name,assignmentid)
            VALUES ".implode(',', $qm)."
            ON DUPLICATE KEY 
            UPDATE 
            division_id = values(division_id),
            division_name = values(division_name)
        ");

    if ($stmt->execute($data_to_insert)){ 

        $ob->requeststatus = 'saving success';

     } 
     else {

        $ob->requeststatus = 'saving failed';
        $ob->errorinfo = $stmt->errorInfo();

    }

    echo json_encode($ob);

}

}

?>