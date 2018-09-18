<?php
$page = new page("Authorisation", 3);
if(page::isLoggedIn()){
	if(page::attendanceMarked("morning") === false OR page::attendanceMarked("evening") === false){
		header("Location: /mark");
	}else{
		if(isset($_SERVER['HTTP_REFERER'])){
			header("Location: " . $_SERVER['HTTP_REFERER']);
		}else{
			header("Location: /main");
		}
	}
}
if(isset($_POST['login'])){
	$errors = array();
	if(empty($_POST['username'])){
		$errors[] = "You forgot to enter the username!";
	}
	if(empty($_POST['password'])){
		$errors[] = "You forgot to enter the password!";
	}
	$username = $sql->smart($_POST['username']);
	$password = $_POST['password'];
	$usernameTest = $sql->fetch_array($sql->query("SELECT `salt` FROM `users` WHERE `username` = $username"));
	$salt = $usernameTest['salt'];
	$pphash = page::hashPassword($password, false, $salt);
	$pHash = $pphash['hash'];
	$loginTest = $sql->query("SELECT `id` FROM `users` WHERE `username` = $username AND `password` = '$pHash' AND `active` = 1");
	$loginRow = $sql->fetch_array($loginTest);
	if($sql->num_rows($loginTest) == 0){
		$errors[] = "The username or password you entered is not correct!";
	}
	if(count($errors) > 0){
		foreach($errors as $error){
			page::alert($error, "danger");
		}
	}else{
		setcookie("user_id", $loginRow['id'], time() + 86400, "/");
		setcookie("password", $pHash, time() + 86400, "/");
		setcookie("auth_time", time(), time() + 86400, "/");
		if(page::attendanceMarked("morning", $loginRow['id']) === false OR page::attendanceMarked("evening", $loginRow['id']) === false){
			header("Location: /mark");
		}else{
			if(isset($_SERVER['HTTP_REFERER'])){
				header("Location: " . $_SERVER['HTTP_REFERER']);
			}else{
				header("Location: /main");
			}
		}
	}
}
?>
<center>
	<img src="<?=url?>/assets/images/logo.png" style="width: 200px; height: 200px; margin-bottom: 20px;">
</center>
<form method="POST">
	<div class="inner-addon left-addon">
		<span class="glyphicon glyphicon-user"></span>
		<input type="text" class="form-control" name="username" placeholder="Username" autofocus>
	</div>

	<div class="inner-addon left-addon">
		<span class="glyphicon glyphicon-lock"></span>
		<input type="password" class="form-control" name="password" placeholder="Password">
	</div>

	<div class="form-button">
		<button type="submit" class="btn btn-primary btn-sm" name="login">Login</button>
	</div>
</form>