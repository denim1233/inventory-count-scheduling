<?php
try 
{
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
    include($_SERVER['DOCUMENT_ROOT'].'/php/settings/bridge.php');

    if($authenticate == true)
    {
    	$p1 = $_POST['vmovieid'];
        $p2 = $_POST['vmoviename'];
        $p3 = 1;
        $p4 = $_POST['vmoviepicture'];
        $p5 = $_POST['vmoviedescription'];
        $p6 = $_POST['vmoviecategoryid'];
        $p7 = $_POST['vmoviefeature'];
        $name = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];

            $path= $_SERVER['DOCUMENT_ROOT'].'/images/';
            $tmp_name = $_FILES['file']['tmp_name'];
            if (!empty($name)) {move_uploaded_file($tmp_name, $path.$name); }
        
        $db = new PDO($dsn,$username,$password);
        $stmt = $db->prepare("CALL `PM_Movies_ManageRecord`(?,?,?,?,?,?,?)");
        $stmt->bindValue(1, $p1, PDO::PARAM_INT);
        $stmt->bindValue(2, $p2, PDO::PARAM_STR);
        $stmt->bindValue(3, $p3, PDO::PARAM_INT);
        $stmt->bindValue(4, $p4, PDO::PARAM_STR);
        $stmt->bindValue(5, $p5, PDO::PARAM_STR);
        $stmt->bindValue(6, $p6, PDO::PARAM_INT);
        $stmt->bindValue(7, $p7, PDO::PARAM_INT);
    	if ($stmt->execute()) 
        { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            
            $path= $_SERVER['DOCUMENT_ROOT'].'/images/';
            if (!empty($name)) {move_uploaded_file($tmp_name, $path.$name); }
            
            echo $JSON;
        } 
        else 
        {
        //display error information
        //var_dump($rs);
        // print_r($stmt->errorInfo());
        
        }
    } 
}
catch (PDOException $e) 
{ 
  echo $e; 
} 
?>