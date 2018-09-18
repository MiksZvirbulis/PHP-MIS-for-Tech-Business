<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['service_id']) AND page::isLoggedIn()){
	$service_id = $_POST['service_id'];
	$service = $sql->fetch_array($sql->query("SELECT `cost` FROM `services` WHERE `id` = $service_id"));
	echo $service['cost'];
}else{
	exit;
}
?>