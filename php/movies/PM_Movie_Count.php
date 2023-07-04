<?php
try 
{
    $p1 = $_POST['varmovieid'];
    $db = new PDO("mysql:host=localhost;dbname=id9549775_dbmovies;charset=utf8","id9549775_denim1233","JK9090");
    $stmt = $db->prepare("CALL `PM_Movie_AddView`(?)");
    $stmt->bindValue(1, $p1, PDO::PARAM_INT);
	$rs = $stmt->execute();
	//$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$myObj->status = 'tae worked' ;
	$JSON = json_encode($myObj);
	echo $JSON; 
} 
catch (PDOException $e) 
{ 
   echo $e; 
} 
?>







