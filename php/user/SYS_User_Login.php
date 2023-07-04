<?php
include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');
//include($_SERVER['DOCUMENT_ROOT'].'/php/settings/alphp.php');
 $AL = new ALPHP;


if($authenticate == true)
{
    try 
    { 
    	$p1 = $_POST['vusername'];
        $p2 = $_POST['vuserpassword'];
        $input_password  = hash('sha256', $p2 . $salt);
        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("CALL `SYS_User_Login`(?,?)");
        $stmt->bindValue(1, $p1, PDO::PARAM_STR);
        $stmt->bindValue(2, $input_password , PDO::PARAM_STR);
        
    	if ($stmt->execute()) 
        { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            echo $JSON;
        } 
        else 
        {
        //display error information
        //var_dump($rs);
        // print_r($stmt->errorInfo());
        
        }
    }
    catch (PDOException $e) 
    { 
       echo $e; 
    }
}
?>