<?php
if(page::isLoggedIn()){
	setcookie("user_id", null, -1, "/");
	setcookie("password", null, -1, "/");
	setcookie("auth_time", null, -1, "/");
	header("Location: /");
}else{
	header("Location: /");
}
?>