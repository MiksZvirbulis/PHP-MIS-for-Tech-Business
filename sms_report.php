<?php
if(getenv('REMOTE_ADDR') != "213.175.74.35"){
	header("Location: /");
	exit;
}

require "system/config.php";

try{
	$status = new Text2reach_SMS_Status(text2reach_api_key);
	$status->create_from_report($_GET);
	$message_id = $status->id;
	$retries = $sql->smart($status->retries);
	$status = $sql->smart($status->status);
	$time = $sql->smart(time());
	$sql->query("UPDATE `sms_logs` SET `retries` = $retries, `status` = $status, `time_responded` = $time WHERE `message_id` = $message_id");

}
catch (Text2reach_Exception $e){

}