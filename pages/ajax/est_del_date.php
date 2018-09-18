<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['saturday']) AND isset($_POST['shipment_date']) AND isset($_POST['delivery_method']) AND page::isLoggedIn()){
	if($_POST['delivery_method'] == "collection"){
		echo $_POST['shipment_date'];
	}else{
		$saturday = $_POST['saturday'];
		$eur_shipment_date = str_replace("/", "-", $_POST['shipment_date']);
		$shipment_date = strtotime($eur_shipment_date);
		$next_day = strtotime("+1 day", $shipment_date);
		if(date("D", $next_day) == "Sat" AND $saturday == 0){
			$delivery_date = strtotime("+2 days", $next_day);
		}elseif(date("D", $next_day) == "Sun"){
			$delivery_date = strtotime("+1 day", $next_day);
		}else{
			$delivery_date = $next_day;
		}
		echo date("d/m/Y", $delivery_date);
	}
}else{
	exit;
}
?>