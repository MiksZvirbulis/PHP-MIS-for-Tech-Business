<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['phone_number']) AND isset($_POST['message']) AND isset($_POST['prefix']) AND page::isLoggedIn()){
	$global_user_id = $_COOKIE['user_id'];
	$errors = array();

	if(empty($_POST['phone_number'])){
		$errors[] = "You forgot to enter the phone number!";
	}else{
		if($_POST['prefix'] == "44"){
			if(strlen($_POST['phone_number']) != 10){
				$errors[] = "The phone number entered does not meet 10 digits!";
			}
		}else{
			if(strlen($_POST['phone_number']) != 8){
				$errors[] = "The phone number entered does not meet 8 digits!";
			}
		}
	}

	if(empty($_POST['message'])){
		$errors[] = "You forgot to enter the message!";
	}else{
		if(strlen($_POST['message']) > 160){
			$errors[] = "The message is longer than the 160 character limit!";
		}
	}

	if(count($errors) > 0){
		foreach($errors as $error){
			page::alert($error, "danger");
		}
	}else{
		try{
			$sms = new Text2reach_SMS_Bulk(text2reach_api_key);
			$sms->from = $_POST['from'];
			$sms->phone = $_POST['prefix'] . $_POST['phone_number'];
			$sms->message = $_POST['message'];
			$sms->report_url = url . "/sms_report.php";;
			$sms->send();
			$message_id = $sms->id;
			$phone_number = $sql->smart($_POST['prefix'] . $_POST['phone_number']);
			$message = $sql->smart($_POST['message']);
			$time = $sql->smart(time());
			$sql->query("INSERT INTO `sms_logs` (`message_id`, `phone_number`, `message`, `sent_by_id`, `status`, `time_sent`) VALUES ($message_id, $phone_number, $message, $global_user_id, 'pending', $time)");
			echo "success";
		}
		catch(Text2reach_Exception $e){
			page::alert($e->getMessage(), "danger");
			page::alert($page->sms_errors[$sms->response()], "danger");
		}
	}
}else{
	exit;
}