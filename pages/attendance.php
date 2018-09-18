<?php
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}

if($action == "all"): ?>
<?php
$page = new page("Manage Attendance", 1);
$from_date = $sql->smart((isset($_POST['from'])) ? $_POST['from'] : date("d/m/Y"));
$to_date = $sql->smart((isset($_POST['from'])) ? $_POST['to'] : date("d/m/Y"));
$user = "";
if(isset($_POST['user_id']) AND $_POST['user_id'] != "all"){
	$user_id = $_POST['user_id'];
	$user = "AND `user_id` = $user_id";
}
$attendance = $sql->query("SELECT * FROM `attendance` WHERE `date` BETWEEN $from_date AND $to_date $user ORDER BY `id` DESC");
$from_d = isset($_POST['from']) ? $_POST['from'] : date("d/m/Y");
$to_d = isset($_POST['to']) ? $_POST['to'] : date("d/m/Y");
?>
<form class="form-inline bottom-space pull-left" role="form" method="POST">
	<div class="form-group">
		<select class="form-control no-space" name="user_id">
			<option value="all">All Users</option>
			<?php
			$users = $sql->query("SELECT `id`, `name`, `surname`, `username` FROM `users` ORDER BY `id` ASC");
			while($user = $sql->fetch_array($users)){
				if(isset($_POST['user_id'])){
					$user_d = ($_POST['user_id'] == $user['id']) ? " selected" : "";
				}else{
					$user_d = "";
				}
				?>
				<option value="<?=$user['id']?>"<?=$user_d?>><?=$user['name']?> <?=$user['surname']?> (<?=$user['username']?>)</option>
				<?php
			}
			?>
		</select>
	</div>
	<div class="form-group">
		<label class="sr-only" for="from">Date From</label>
		<input type="text" name="from" class="form-control datepicker no-space" placeholder="Date From..." value="<?php echo $from_d; ?>">
	</div>
	<div class="form-group">
		<label class="sr-only" for="to">Date To</label>
		<input type="text" name="to" class="form-control datepicker no-space" placeholder="Date To..." value="<?php echo $to_d; ?>">
	</div>
	<button type="submit" class="btn btn-default" name="view">View</button>
</form>
<legend>Attendance period between <?php echo $from_d; ?> & <?php echo $to_d; ?></legend>
<table class="table table-bordered">
	<thead>
		<th>User</th>
		<th>Date</th>
		<th>Start Time</th>
		<th>Actual Start Time</th>
		<th>Start IP Address</th>
		<th>Finish Time</th>
		<th>Actual Finish Time</th>
		<th>Finish IP Address</th>
		<th>Total Time</th>
		<th>Actions</th>
	</thead>
	<tbody>
		<?php $total_seconds = 0; ?>
		<?php while($data = $sql->fetch_array($attendance)): ?>
			<tr>
				<td><?php echo page::getUserInfo($data['user_id'], "name") . " " . page::getUserInfo($data['user_id'], "surname") . " (<strong>" . page::getUserInfo($data['user_id'], "username") . "</strong>)"; ?></td>
				<td><strong><?php echo $data['date']; ?></strong></td>
				<td><strong><?php echo $data['start_time']; ?></strong></td>
				<td><?php echo (!empty($data['finish_timestamp'])) ? date("<b>d/m/Y</b> H:i", $data['start_timestamp']) : ""; ?></td>
				<td><?php echo $data['start_ip_address']; ?></td>
				<td><strong><?php echo $data['finish_time']; ?></strong></td>
				<td><?php echo (!empty($data['finish_timestamp'])) ? date("<b>d/m/Y</b> H:i", $data['finish_timestamp']) : ""; ?></td>
				<td><?php echo $data['finish_ip_address']; ?></td>
				<?php
				$time1 = $data['start_time'];
				if(empty($data['finish_time'])){
					$time2 = $data['start_time'];
				}else{
					$time2 = $data['finish_time'];
				}
				list($hours, $minutes) = explode(":", $time1);
				$startTimestamp = mktime($hours, $minutes);
				list($hours, $minutes) = explode(":", $time2);
				$endTimestamp = mktime($hours, $minutes);
				$seconds = $endTimestamp - $startTimestamp;
				$minutes = ($seconds / 60) % 60;
				$hours = floor($seconds / (60 * 60));
				$hours_string = ($hours == 1) ? "hour" : "hours";
				$total_seconds += $seconds;
				?>
				<td width="150px"><?php echo "<b>$hours</b> $hours_string, <b>$minutes</b> minutes"; ?></td>
				<td>
					<a href="<?=url?>/attendance/edit/<?=$data['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
				</td>
			</tr>
		<?php endwhile; ?>
		<tr>
			<td colspan="8" class="text-right"><strong>Total:</strong></td>
			<?php
			$minutes = ($total_seconds / 60) % 60;
			$hours = floor($total_seconds / (60 * 60));
			$hours_string = ($hours == 1) ? "hour" : "hours";
			?>
			<td colspan="2"><?php echo "<b>$hours</b> $hours_string, <b>$minutes</b> minutes"; ?></td>
		</tr>
	</tbody>
</table>
<?php elseif($action == "edit"): ?>
	<?php
	$page = new page("Edit Attendance", 1);
	if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
		$id = (int)$pageInfo[3];
		$find = $sql->query("SELECT * FROM `attendance` WHERE `id` = $id");
		if($sql->num_rows($find) == 0){
			page::alert("No attendance was found with this ID!", "danger");
		}else{
			if(isset($_POST['edit'])){
				$start_time = $sql->smart($_POST['start_time']);
				$finish_time = $sql->smart($_POST['finish_time']);
				$sql->query("UPDATE `attendance` SET `start_time` = $start_time, `finish_time` = $finish_time WHERE `id` = $id");
				$find = $sql->query("SELECT * FROM `attendance` WHERE `id` = $id");
			}
			$attendance = $sql->fetch_array($find);
			?>
			<legend>Mark Attendance for <?php echo page::getUserInfo($attendance['user_id'], "name") . " " . page::getUserInfo($attendance['user_id'], "surname") . " (<strong>" . page::getUserInfo($attendance['user_id'], "username") . "</strong>)"; ?> on <?php echo $attendance['date']; ?></legend>
			<form method="POST" style="width: 300px; margin: 0 auto;">
				<label for="start_time">Start Time</label>
				<div class="input-append bootstrap-timepicker input-group">
					<input type="text" class="form-control timepicker" name="start_time" value="<?php echo $attendance['start_time']; ?>">
					<span class="input-group-addon"><span class="add-on"><span class="glyphicon glyphicon-time"></span></span></span>
				</div>
				<label for="finish_time">Finish Time</label>
				<div class="input-append bootstrap-timepicker input-group">
					<input type="text" class="form-control timepicker" name="finish_time" value="<?php echo $attendance['finish_time']; ?>">
					<span class="input-group-addon"><span class="add-on"><span class="glyphicon glyphicon-time"></span></span></span>
				</div>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="edit">Edit Attendance</button>
				</div>
			</form>
			<?php
		}
	}else{
		page::alert("No ID was specified!", "danger");
	}
	?>
<?php else: ?>
	<?php
	$page = new page("My Attendance", 3);
	$from_date = $sql->smart( (isset($_POST['from'])) ? $_POST['from'] : date("d/m/Y") );
	$to_date = $sql->smart( (isset($_POST['from'])) ? $_POST['to'] : date("d/m/Y") );
	$user_id = (int)$_COOKIE['user_id'];
	$attendance = $sql->query("SELECT * FROM `attendance` WHERE `date` BETWEEN $from_date AND $to_date AND `user_id` = $user_id ORDER BY `id` DESC");
	$from_d = isset($_POST['from']) ? $_POST['from'] : date("d/m/Y");
	$to_d = isset($_POST['to']) ? $_POST['to'] : date("d/m/Y");
	?>
	<form class="form-inline bottom-space pull-left" role="form" method="POST">
		<div class="form-group">
			<label class="sr-only" for="from">Date From</label>
			<input type="text" name="from" class="form-control datepicker no-space" placeholder="Date From..." value="<?php echo $from_d; ?>">
		</div>
		<div class="form-group">
			<label class="sr-only" for="to">Date To</label>
			<input type="text" name="to" class="form-control datepicker no-space" placeholder="Date To..." value="<?php echo $to_d; ?>">
		</div>
		<button type="submit" class="btn btn-default" name="view">View</button>
	</form>
	<legend>Attendance period between <?php echo $from_d; ?> & <?php echo $to_d; ?></legend>
	<table class="table table-bordered">
		<thead>
			<th>User</th>
			<th>Date</th>
			<th>Start Time</th>
			<th>Finish Time</th>
			<th>Total Time</th>
		</thead>
		<tbody>
			<?php $total_seconds = 0; ?>
			<?php while($data = $sql->fetch_array($attendance)): ?>
				<tr>
					<td><?php echo page::getUserInfo($data['user_id'], "name") . " " . page::getUserInfo($data['user_id'], "surname") . " (<strong>" . page::getUserInfo($data['user_id'], "username") . "</strong>)"; ?></td>
					<td><strong><?php echo $data['date']; ?></strong></td>
					<td><strong><?php echo $data['start_time']; ?></strong></td>
					<td><strong><?php echo $data['finish_time']; ?></strong></td>
					<?php
					$time1 = $data['start_time'];
					if(empty($data['finish_time'])){
						$time2 = $data['start_time'];
					}else{
						$time2 = $data['finish_time'];
					}
					list($hours, $minutes) = explode(":", $time1);
					$startTimestamp = mktime($hours, $minutes);
					list($hours, $minutes) = explode(":", $time2);
					$endTimestamp = mktime($hours, $minutes);
					$seconds = $endTimestamp - $startTimestamp;
					$minutes = ($seconds / 60) % 60;
					$hours = floor($seconds / (60 * 60));
					$hours_string = ($hours == 1) ? "hour" : "hours";
					$total_seconds += $seconds;
					?>
					<td><?php echo "<b>$hours</b> $hours_string, <b>$minutes</b> minutes"; ?></td>
				</tr>
			<?php endwhile; ?>
			<tr>
				<td colspan="4" class="text-right"><strong>Total:</strong></td>
				<?php
				$minutes = ($total_seconds / 60) % 60;
				$hours = floor($total_seconds / (60 * 60));
				$hours_string = ($hours == 1) ? "hour" : "hours";
				?>
				<td><?php echo "<b>$hours</b> $hours_string, <b>$minutes</b> minutes"; ?></td>
			</tr>
		</tbody>
	</table>
<?php endif; ?>