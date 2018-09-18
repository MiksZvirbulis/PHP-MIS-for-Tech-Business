<?php
$page = new page("Manage users", 1);
$global_user_id = $_COOKIE['user_id'];
$users = $sql->query("SELECT * FROM `users` ORDER BY `id` ASC");
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}
if($action == "del"){
	if(isset($pageInfo[3])){
		$user_id = (int)$pageInfo[3];
		$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `id` = $user_id"));
		if($check_existance == 0){
			page::alert("The user with that ID was not found!", "danger");
		}else{
			$sql->query("DELETE FROM `users` WHERE `id` = $user_id");
			page::alert("User successfully deleted!<br />Redirecting...", "success");
			header("refresh:2;url=" . url . "/users");
		}
	}else{
		page::alert("No ID was specified!", "danger");
	}
}elseif($action == "edit"){
	if(isset($pageInfo[3])){
		$user_id = (int)$pageInfo[3];
		$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `id` = $user_id"));
		if($check_existance == 0){
			page::alert("The user with that ID was not found!", "danger");
		}else{
			if(isset($_POST['change_password'])){
				$errors = array();
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
					page::alert("Password successfully changed!", "success");
				}
			}
			if(isset($_POST['edit_user'])){
				$errors = array();
				if(empty($_POST['username'])){
					$errors[] = "You forgot to enter the username!";
				}else{
					$current_username = $sql->fetch_array($sql->query("SELECT `username` FROM `users` WHERE `id` = $user_id"));
					if($current_username['username'] != $_POST['username']){
						$usernameTest = $sql->smart($_POST['username']);
						$test_username = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `username` = $usernameTest"));
						if($test_username == 1){
							$errors[] = "The username already exists!";
						}
					}
				}
				if(empty($_POST['level'])){
					$errors[] = "You forgot to select the access level!";
				}
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
					$username = $sql->smart($_POST['username']);
					$level = $sql->smart($_POST['level']);
					$email = $sql->smart($_POST['email']);
					$name = $sql->smart($_POST['name']);
					$surname = $sql->smart($_POST['surname']);
					$skype = $sql->smart($_POST['skype']);
					$phone_number = $sql->smart($_POST['phone_number']);
					$mark_attendance = (isset($_POST['mark_attendance'])) ? 1 : 0;
					$active = (isset($_POST['active'])) ? 1 : 0;
					$sql->query("UPDATE `users` SET `username` = $username, `level` = $level, `email` = $email, `name` = $name, `surname` = $surname, `skype` = $skype, `phone_number` = $phone_number, `mark_attendance` = $mark_attendance, `active` = $active WHERE `id` = $user_id");
					$user = $sql->fetch_array($sql->query("SELECT * FROM `users` WHERE `id` = $user_id"));
					page::alert("User has been edited successfully!", "success");
				}
			}
			$user = $sql->fetch_array($sql->query("SELECT * FROM `users` WHERE `id` = $user_id"));
			$level1 = ($user['level'] == "1") ? " selected" : "";
			$level2 = ($user['level'] == "2") ? " selected" : "";
			$level3 = ($user['level'] == "3") ? " selected" : "";
			$mark_attendance = ($user['mark_attendance'] == 1) ? " checked" : "";
			$active = ($user['active'] == 1) ? " checked" : "";
			?>
			<form method="POST">
				<input type="hidden" name="username" value="<?=$user['username']?>">
				<input type="text" class="form-control" placeholder="Username*" value="<?=$user['username']?>" disabled>
				<input type="password" class="form-control" name="password" placeholder="New password*">
				<input type="password" class="form-control" name="password2" placeholder="Repeat new password*">
				* Required
				<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
					<button type="submit" class="btn btn-primary btn-sm" name="change_password">Change password</button>
				</div>
			</form>
			<form method="POST">
				<input type="text" class="form-control" name="username" placeholder="Username*" value="<?=$user['username']?>">
				<select name="level" class="form-control">
					<option selected disabled>Select access level*</option>
					<option value="1"<?=$level1?>>1</option>
					<option value="2"<?=$level2?>>2</option>
					<option value="3"<?=$level3?>>3</option>
				</select>
				<input type="text" class="form-control" name="email" placeholder="Email*" value="<?=$user['email']?>">
				<input type="text" class="form-control" name="name" placeholder="Name*" value="<?=$user['name']?>">
				<input type="text" class="form-control" name="surname" placeholder="Surname*" value="<?=$user['surname']?>">
				<input type="text" class="form-control" name="skype" placeholder="Skype" value="<?=$user['skype']?>">
				<input type="text" class="form-control" name="phone_number" placeholder="Phone number*" value="<?=$user['phone_number']?>">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="mark_attendance"<?=$mark_attendance?>>
						<span class="lbl"> Mark Attendance</span>
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="active"<?=$active?>>
						<span class="lbl"> Active</span>
					</label>
				</div>
				* Required
				<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
					<button type="submit" class="btn btn-primary btn-sm" name="edit_user">Edit user</button>
				</div>
			</form>
			<?php
		}
	}else{
		page::alert("No ID was specified!", "danger");
	}
}elseif($action == "new"){
	if(isset($_POST['create_user'])){
		$errors = array();
		if(empty($_POST['username'])){
			$errors[] = "You forgot to enter the username!";
		}else{
			$usernameTest = $sql->smart($_POST['username']);
			$test_username = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `username` = $usernameTest"));
			if($test_username == 1){
				$errors[] = "The username already exists!";
			}
		}
		if(empty($_POST['password'])){
			$errors[] = "You forgot to enter the password!";
		}
		if(empty($_POST['password2'])){
			$errors[] = "You forgot to repeat the password!";
		}
		if($_POST['password'] != $_POST['password2']){
			$errors[] = "Both passwords do not match!";
		}
		if(empty($_POST['level'])){
			$errors[] = "You forgot to select the access level!";
		}
		if(empty($_POST['email'])){
			$errors[] = "You forgot to enter the email!";
		}else{
			$emailTest = $sql->smart($_POST['email']);
			$test_email = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `email` = $emailTest"));
			if($test_email == 1){
				$errors[] = "The email already exists!";
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
			$username = $sql->smart($_POST['username']);
			$password = $_POST['password'];
			$hashP = page::hashPassword($password, true);
			$pHash = $sql->smart($hashP['hash']);
			$salt = $sql->smart($hashP['salt']);
			$level = $sql->smart($_POST['level']);
			$email = $sql->smart($_POST['email']);
			$name = $sql->smart($_POST['name']);
			$surname = $sql->smart($_POST['surname']);
			$skype = $sql->smart($_POST['skype']);
			$last_action_time = $sql->smart(time());
			$phone_number = $sql->smart($_POST['phone_number']);
			$sql->query("INSERT INTO `users` (`username`, `password`, `salt`, `level`, `email`, `name`, `surname`, `skype`, `last_action_time`, `phone_number`, `created_by_id`) VALUES ($username, $pHash, $salt, $level, $email, $name, $surname, $skype, $last_action_time, $phone_number, $global_user_id)");
			page::alert("User has been added successfully!", "success");
		}
	}
	?>
	<form method="POST">
		<input type="text" class="form-control" name="username" placeholder="Username*">
		<input type="password" class="form-control" name="password" placeholder="Password*">
		<input type="password" class="form-control" name="password2" placeholder="Repeat password*">
		<select name="level" class="form-control">
			<option selected disabled>Select access level*</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
		</select>
		<input type="text" class="form-control" name="email" placeholder="Email*">
		<input type="text" class="form-control" name="name" placeholder="Name*">
		<input type="text" class="form-control" name="surname" placeholder="Surname*">
		<input type="text" class="form-control" name="skype" placeholder="Skype">
		<input type="text" class="form-control" name="phone_number" placeholder="Phone number*">
		* Required
		<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
			<button type="submit" class="btn btn-primary btn-sm" name="create_user">Create user</button>
		</div>
	</form>
	<?php
}else{
	?>
	<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Username</th>
				<th>Name, Surname</th>
				<th>Email</th>
				<th>Skype</th>
				<th>Phone number</th>
				<th>Level</th>
				<th>Last action</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			while($user = $sql->fetch_array($users)){
				?>
				<tr>
					<td><b><?=$user['username']?></b></td>
					<td><?=$user['name']?> <?=$user['surname']?></td>
					<td><a href="mailto:<?=$user['email']?>"><?=$user['email']?></a></td>
					<td><a href="skype:<?=$user['skype']?>?chat"><?=$user['skype']?></a></td>
					<td><?=$user['phone_number']?></td>
					<td><?=$user['level']?></td>
					<td><?=date("d-M-Y H:i", $user['last_action_time'])?></td>
					<td>
						<a class="confirm" href="<?=url?>/users/del/<?=$user['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-remove" style="opacity:0.4;"></span> Delete</button></a>
						<a href="<?=url?>/users/edit/<?=$user['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}
?>