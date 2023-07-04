<?php
include 'sqlcon.php';

	//$query = 'SELECT BARCODE FROM XXCH_CIC_PRODUCT_DATA_V WHERE SCHEDULEID = 167474';
	$query = 'SELECT "barcode" barcode FROM cic_inv_product@DWDEV2MYWEB06.CITIHARDWARE.COM WHERE "barcode" = 2080100700657';
	$sql_redbox = oci_parse($connect, $query);
	oci_execute($sql_redbox);
	
	
	while ($result_sqlRedbox = oci_fetch_array($sql_redbox)) {
		echo $PRICE_LIST_NAME = $result_sqlRedbox['BARCODE'];
		echo '<br>';
	}

?>