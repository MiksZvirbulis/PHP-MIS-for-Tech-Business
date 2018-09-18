<?php
$page = new page("My profile", 3);
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "edit";
}
$user_id = $sql->smart($_COOKIE['user_id']);
if($action == "changepw"){
	if(isset($_POST['change_password'])){
		$errors = array();
		if(empty($_POST['current_password'])){
			$errors[] = "You forgot to enter the current password!";
		}
		if(empty($_POST['password'])){
			$errors[] = "You forgot to enter the new password!";
		}
		if(empty($_POST['password2'])){
			$errors[] = "You forgot to repeat the new password!";
		}
		if($_POST['password'] != $_POST['password2']){
			$errors[] = "Both passwords do not match!";
		}
		$username = $sql->smart($_POST['username']);
		$current_password = $_POST['current_password'];
		$usernameTest = $sql->fetch_array($sql->query("SELECT `salt` FROM `users` WHERE `username` = $username"));
		$salt = $usernameTest['salt'];
		$pphash = page::hashPassword($current_password, false, $salt);
		$pHash = $sql->smart($pphash['hash']);
		$loginTest = $sql->query("SELECT `id` FROM `users` WHERE `username` = $username AND `password` = $pHash");
		if($sql->num_rows($loginTest) == 0){
			$errors[] = "The current password you entered was incorrect!";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$password = $_POST['password'];
			$hashP = page::hashPassword($password, true);
			$newpHash = $sql->smart($hashP['hash']);
			$newsalt = $sql->smart($hashP['salt']);
			$sql->query("UPDATE `users` SET `password` = $newpHash, `salt` = $newsalt WHERE `id` = $user_id");
			setcookie("user_id", null, -1, "/");
			setcookie("password", null, -1, "/");
			header("Location: /login");
		}
	}
	$user = $sql->fetch_array($sql->query("SELECT * FROM `users` WHERE `id` = $user_id"));
	page::alert("A successful password change will destroy the session and redirect you to the login page!", "info");
	?>
	<form method="POST">
		<input type="hidden" name="username" value="<?=$user['username']?>">
		<input type="text" class="form-control" placeholder="Username*" value="<?=$user['username']?>" disabled>
		<input type="password" class="form-control" name="current_password" placeholder="Current password*">
		<input type="password" class="form-control" name="password" placeholder="New password*">
		<input type="password" class="form-control" name="password2" placeholder="Repeat new password*">
		* Required
		<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
			<button type="submit" class="btn btn-primary btn-sm" name="change_password">Change password</button>
		</div>
	</form>
	<?php
}else{
	if(isset($_POST['edit_profile'])){
		$errors = array();
		if(empty($_POST['email'])){
			$errors[] = "You forgot to enter the email!";
		}else{
			$current_email = $sql->fetch_array($sql->query("SELECT `email` FROM `users` WHERE `id` = $user_id"));
			if($current_email['email'] != $_POST['email']){
				$emailTest = $sql->smart($_POST['email']);
				$test_email = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `email` = $emailTest"));
				if($test_email == 1){
					$errors[] = "The email already exists!";
				}
			}
		}
		if(empty($_POST['name'])){
			$errors[] = "You forgot to enter the name!";
		}
		if(empty($_POST['surname'])){
			$errors[] = "You forgot to enter the surname!";
		}
		if(empty($_POST['phone_number'])){
			$errors[] = "You forgot to enter the phone number!";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$email = $sql->smart($_POST['email']);
			$name = $sql->smart($_POST['name']);
			$surname = $sql->smart($_POST['surname']);
			$skype = $sql->smart($_POST['skype']);
			$phone_number = $sql->smart($_POST['phone_number']);
			$sql->query("UPDATE `users` SET `email` = $email, `name` = $name, `surname` = $surname, `skype` = $skype, `phone_number` = $phone_number WHERE `id` = $user_id");
			$user = $sql->fetch_array($sql->query("SELECT * FROM `users` WHERE `id` = $user_id"));
			page::alert("Your profile has been updated successfully!", "success");
		}
	}
	$user = $sql->fetch_array($sql->query("SELECT * FROM `users` WHERE `id` = $user_id"));
	?>
	<form method="POST">
		<input type="text" class="form-control" placeholder="Username*" value="<?=$user['username']?>" disabled>
		<input type="text" class="form-control" name="email" placeholder="Email*" value="<?=$user['email']?>">
		<input type="text" class="form-control" name="name" placeholder="Name*" value="<?=$user['name']?>">
		<input type="text" class="form-control" name="surname" placeholder="Surname*" value="<?=$user['surname']?>">
		<input type="text" class="form-control" name="skype" placeholder="Skype" value="<?=$user['skype']?>">
		<input type="text" class="form-control" name="phone_number" placeholder="Phone number*" value="<?=$user['phone_number']?>">
		* Required
		<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
			<button type="submit" class="btn btn-primary btn-sm" name="edit_profile">Edit profile</button>
		</div>
	</form>
	<?php
}
?>