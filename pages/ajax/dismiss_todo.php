<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['todo_id']) AND page::isLoggedIn()){
	$todo_id = $sql->smart($_POST['todo_id']);
	$sql->query("UPDATE `todo` SET `dismissed` = 1 WHERE `id` = $todo_id");
}else{
	exit;
}
?>