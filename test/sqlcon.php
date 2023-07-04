<?php
$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.215)(PORT = 1521)))(CONNECT_DATA=(SID=DW)))" ;
//CONNECTION IN DW
$connect = oci_connect("dw", "dw", $db);
if (!$connect) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}  



$con_103_reportLogs=mysqli_connect('localhost:3307','root','4.44d3c04rts','reports_accesslogs');
if (mysqli_connect_errno()) 
{
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>