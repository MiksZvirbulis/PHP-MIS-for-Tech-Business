<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['note_id']) AND page::isLoggedIn()){
	$note_id = $sql->smart($_POST['note_id']);
	$find_note = $sql->num_rows($sql->query("SELECT `id`, `order_id` FROM `order_notes` WHERE `id` = $note_id"));
	if(page::hasLevel(1)){
		$proceed = true;
	}
	if(isset($proceed) AND $proceed){
		if($find_note == 0){
			echo "false";
		}else{
			$sql->query("DELETE FROM `order_notes` WHERE `id` = $note_id");
			echo "true";
		}
	}else{
		echo "false";
	}
}else{
	exit;
}
?>