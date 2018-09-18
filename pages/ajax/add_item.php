<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['spec_id']) AND isset($_POST['upc']) AND isset($_POST['quantity']) AND isset($_POST['order_id']) AND page::isLoggedIn()){
	$spec_id = $sql->smart($_POST['spec_id']);
	$upc = $sql->smart($_POST['upc']);
	$quantity = $sql->smart($_POST['quantity']);
	$order_id = $sql->smart($_POST['order_id']);
	$order = $sql->fetch_array($sql->query("SELECT `order_status` FROM `orders` WHERE `order_id` = $order_id"));
	if($order['order_status'] == "completed"){
		if(page::hasLevel(1)){
			$proceed = true;
		}
	}else{
		$proceed = true;
	}
	if(isset($proceed) AND $proceed){
		$find_spec = $sql->num_rows($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `spec_id` = $spec_id"));
		$find_item = $sql->num_rows($sql->query("SELECT `upc` FROM `stock_items` WHERE `upc` = $upc"));
		if($find_spec == 0 OR $find_item == 0){
			echo "false";
		}else{
			$sql->query("INSERT INTO `order_items` (`order_spec_id`, `upc`, `quantity`, `order_id`, `cost_exc_vat`) VALUES ($spec_id, $upc, $quantity, $order_id, '0.00')");
			echo "true";
		}
	}else{
		echo "false";
	}
}else{
	exit;
}
?>