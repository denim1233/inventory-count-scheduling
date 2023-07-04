
<?php

$myServer = '192.168.4.215';
$myDB = 'DW';
$oci_uname = 'dw';
$oci_pass = 'dw';

$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";

$connect = oci_connect('dw', 'dw', $db);
$query = "SELECT 
SCHEDULEDATE,SCHEDULEID,ONHAND_QTY,0 as SELLING_QTY, 0 as RECEIVING_QTY,0 as ADJUSTMENT_QTY,0 as RESERVATION_QTY,0 as ADJ_ISSUE,0 as ADJ_RECEIPT,0 as RCV_IR,0 as RCV_ER
FROM XXCH_CIC_PRODUCT_DATA_V
WHERE scheduledate BETWEEN ( TO_DATE(SYSDATE , 'DD-MM-YY')) AND ( TO_DATE(SYSDATE , 'DD-MM-YY'))
AND statusid = 1";
$sql_redbox = oci_parse($connect, $query);
if(oci_execute($sql_redbox)){
    echo "success!";
}else{
    echo "failed!";
}




// while ($result_sqlRedbox = oci_fetch_array($sql_redbox)) {
//   $PRICE_LIST_NAME = $result_sqlRedbox;
//     print_r($PRICE_LIST_NAME);
// }

//   $options = [
//             PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
//             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//             PDO::ATTR_CASE => PDO::CASE_LOWER,
//             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
//         ];
//             /*Oracle*/
//             $myServer = '192.168.4.215';
//             $myDB = 'DW';
//             $oci_uname = 'dw';
//             $oci_pass = 'dw';
//             $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
//             try {
//                 $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
//                 $sth = $conn->query(
//                         "
//                         select * from cic_inv_product@DWDEV2MYWEB06.CITIHARDWARE.COM INP
//                         where rownum <= 10
//                         "
//                 );
//                 // select * from cic_inv_product@DWDEV2MYWEB06.CITIHARDWARE.COM INP

//                 // SELECT 
//                 // SCHEDULEDATE,SCHEDULEID,ONHAND_QTY,0 as SELLING_QTY, 0 as RECEIVING_QTY,0 as ADJUSTMENT_QTY,0 as RESERVATION_QTY,0 as ADJ_ISSUE,0 as ADJ_RECEIPT,0 as RCV_IR,0 as RCV_ER
//                 // FROM CIC_ORA_MYSQL
//                 // WHERE scheduledate BETWEEN ( TO_DATE(SYSDATE , 'DD-MM-YY')) AND ( TO_DATE(SYSDATE , 'DD-MM-YY'))
//                 // AND statusid = 1

//                 // select * from cic_inv_product@DWDEV2MYWEB06.CITIHARDWARE.COM 
//                 // select * from cic_inv_product@DWDEV2MYWEB06.CITIHARDWARE.COM INP

//                 if($sth->execute()){
//                     $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);  
//                     echo json_encode($datacontainer);
//                 }else{
//                      print_r($sth->errorInfo());
//                 }
                
//             } catch(PDOException $e) {
//                 echo 'ERROR: ' . $e->getMessage();
//             }
?>