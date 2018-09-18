<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['payment_id']) AND page::isLoggedIn() AND page::hasLevel(2)){
	$payment_id = (int)$_POST['payment_id'];
	$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `payment_entries` WHERE `id` = $payment_id"));
	if($check_existance == 0){
		echo "Payment not found or already removed! Please refresh page.";
	}else{
		$sql->query("DELETE FROM `payment_entries` WHERE `id` = $payment_id");
		echo "success";
	}
}else{
	exit;
}
?>