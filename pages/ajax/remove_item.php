<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['item_id']) AND page::isLoggedIn()){
	$item_id = $sql->smart($_POST['item_id']);
	$find_item = $sql->query("SELECT `item_id`, `order_id` FROM `order_items` WHERE `item_id` = $item_id");
	$items = $sql->fetch_array($find_item);
	$order_id = $items['order_id'];
	$order = $sql->fetch_array($sql->query("SELECT `order_status` FROM `orders` WHERE `order_id` = $order_id"));
	if($order['order_status'] == "completed"){
		if(page::hasLevel(1)){
			$proceed = true;
		}
	}else{
		$proceed = true;
	}
	if(isset($proceed) AND $proceed){
		if($sql->num_rows($find_item) == 0){
			echo "false";
		}else{
			$sql->query("DELETE FROM `order_items` WHERE `item_id` = $item_id");
			echo "true";
		}
	}else{
		echo "false";
	}
}else{
	exit;
}
?>