<?php	

	$filename="item_ledger.xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	$output = "
	<table>
	".$_POST['txttableContainer']."
	</table>
	";

	echo $output;

?>