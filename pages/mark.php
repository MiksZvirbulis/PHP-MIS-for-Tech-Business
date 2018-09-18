<?php
$page = new page("Mark Attendance", 3);
if(!page::attendanceMarked()):
	$minute = date("i");
$nearest = round($minute / 15);
$minutes = $nearest * 15;
$hour = date("H");
if($minute >= 53){
	$hour = date("H", strtotime("+ 1 hour"));
	$minutes = "00";
}elseif($minutes == 0){
	$minutes = "00";
}

if(isset($_POST['mark'])){
	$user_id = (int)$_COOKIE['user_id'];
	$date = $sql->smart(date("d/m/Y"));
	$start_time = $sql->smart($hour . ":" . $minutes);
	$start_timestamp = $sql->smart(time());
	$start_ip_address = $sql->smart(page::getIP());
	$sql->query("INSERT INTO `attendance` (`user_id`, `date`, `start_time`, `start_timestamp`, `start_ip_address`) VALUES ($user_id, $date, $start_time, $start_timestamp, $start_ip_address)");
	if(isset($_SERVER['HTTP_REFERER'])){
		header("Location: " . $_SERVER['HTTP_REFERER']);
	}else{
		header("Location: /");
	}
}
?>
<legend>Mark Attendance - Entering</legend>
<form method="POST" style="width: 300px; margin: 0 auto;">
	<select class="form-control">
		<option selected disabled>Attended at <? echo $hour . ":" . $minutes; ?></option>
	</select>
	<div class="form-button">
		<button type="submit" class="btn btn-primary btn-sm" name="mark">Mark Attendance</button>
	</div>
</form>
<?php
elseif(!page::attendanceMarked("evening")):
	$minute = date("i");
$nearest = round($minute / 15);
$minutes = $nearest * 15;
$hour = date("H");
if($minute >= 53){
	$hour = date("H", strtotime("+ 1 hour"));
	$minutes = "00";
}elseif($minutes == 0){
	$minutes = "00";
}

if(isset($_POST['mark'])){
	$user_id = (int)$_COOKIE['user_id'];
	$date = $sql->smart(date("d/m/Y"));
	$finish_time = $sql->smart($hour . ":" . $minutes);
	$finish_timestamp = $sql->smart(time());
	$finish_ip_address = $sql->smart(page::getIP());
	$sql->query("UPDATE `attendance` SET `finish_time` = $finish_time, `finish_timestamp` = $finish_timestamp, `finish_ip_address` = $finish_ip_address WHERE `user_id` = $user_id AND `date` = $date");
	if(isset($_SERVER['HTTP_REFERER'])){
		header("Location: " . $_SERVER['HTTP_REFERER']);
	}else{
		header("Location: /");
	}
}
?>
<legend>Mark Attendance - Leaving</legend>
<form method="POST" style="width: 300px; margin: 0 auto;">
	<select class="form-control">
		<option selected disabled>Leaving at <? echo $hour . ":" . $minutes; ?></option>
	</select>
	<div class="form-button">
		<button type="submit" class="btn btn-primary btn-sm" name="mark">Mark Attendance</button>
	</div>
</form>
<?php else: ?>
	<legend>No attendance options!</legend>
	<strong>Why?</strong><br />
	<ol>
		<li>You have already marked your attendance for today;</li>
		<li>You are not set to mark your attendance</li>
	</ol>
<?php endif; ?>