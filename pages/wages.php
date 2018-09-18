<?php
$page = new page("Wages", 1);

if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}

if($action == "add"){
	if(isset($_POST['add'])){
		$errors = array();

		if(empty($_POST['user_id'])){
			$errors[] = "You to select the user!";
		}

		if(empty($_POST['by'])){
			$errors[] = "You forgot to enter the given by field!";
		}

		if(empty($_POST['date'])){
			$errors[] = "You forgot to enter the date of payment!";
		}

		if(empty($_POST['amount'])){
			$errors[] = "You forgot to enter the amount that was given!";
		}

		if(empty($_POST['reason'])){
			$errors[] = "You forgot to enter the reason for the payment!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				echo page::alert($error, "danger");
			}
		}else{
			$user_id = $sql->smart((int)$_POST['user_id']);
			$by = $sql->smart($_POST['by']);
			$date = $sql->smart($_POST['date']);
			$amount = $sql->smart($_POST['amount']);
			$reason = $sql->smart($_POST['reason']);
			$sql->query("INSERT INTO `wages` (`user_id`, `by`, `date`, `amount`, `reason`) VALUES ($user_id, $by, $date, $amount, $reason)");
			echo page::alert("Payment added successfully!", "success");
		}
	}
	?>
	<form method="POST">
		<select name="user_id" class="form-control">
			<option selected disabled>Select user</option>
			<?php $users = $sql->query("SELECT `id`, `name`, `surname` FROM `users` ORDER BY `id` ASC"); ?>
			<?php while($user = $sql->fetch_array($users)): ?>
				<option value="<?php echo $user['id']; ?>"><?php echo $user['name'] . " " . $user['surname']; ?></option>
			<?php endwhile; ?>
		</select>
		<input type="text" class="form-control" name="by" placeholder="Given by">
		<input type="text" class="form-control datepicker" name="date" placeholder="Date" value="<?php echo date("d/m/Y"); ?>">
		<input type="text" class="form-control" onclick="money(this)" onBlur="money(this)" name="amount" placeholder="Amount" value="0.00">
		<textarea type="text" class="form-control" name="reason" placeholder="Reason for Payment"></textarea>
		<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
			<button type="submit" class="btn btn-primary btn-sm" name="add">Add payment</button>
		</div>
	</form>
	<?php
}else{
	?>
	<table class="table table-bordered">
		<thead>
			<th>User</th>
			<th>Given by</th>
			<th>Date</th>
			<th>Amount</th>
			<th>Reason</th>
		</thead>
		<tbody>
			<?php $wages = $sql->query("SELECT * FROM `wages` ORDER BY `date` DESC"); ?>
			<?php while($wage = $sql->fetch_array($wages)): ?>
				<tr>
					<td><?php echo page::getUserInfo($wage['user_id'], "name") . " " . page::getUserInfo($wage['user_id'], "surname"); ?></td>
					<td><?php echo $wage['by']; ?></td>
					<td><?php echo $wage['date']; ?></td>
					<td><?php echo $wage['amount']; ?></td>
					<td><?php echo $wage['reason']; ?></td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	<?php
}