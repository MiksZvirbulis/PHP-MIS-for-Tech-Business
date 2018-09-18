<?php
$page = new page("My To-Do List", 3);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2]) AND !empty($pageInfo[2]) AND $pageInfo[2] == "add"){
	if(isset($_POST['add_todo'])){
		$errors = array();
		if(empty($_POST['content'])){
			$errors[] = "You forgot to enter what you have to do!";
		}
		if(empty($_POST['do_by'])){
			$errors[] = "You forgot to enter the date you have to do it by!";
		}
		if(empty($_POST['do_by_time'])){
			$errors[] = "You forgot to enter the time you have to do it by!";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$added = $sql->smart(date("d/m/Y", time()));
			$do_by = $sql->smart($_POST['do_by']);
			$do_by_time = $sql->smart($_POST['do_by_time']);
			$content = $sql->smart($_POST['content']);
			$sql->query("INSERT INTO `todo` (`added`, `do_by`, `do_by_time`, `content`, `done`, `user_id`) VALUES ($added, $do_by, $do_by_time, $content, 0, $global_user_id)");
			page::alert("To-Do List item added successfully!", "success");
			header("Location: " . url . "/todo");
		}
	}
	?>
	<form method="POST" class="form-horizontal" style="width: 700px; margin: 0 auto;">
		<textarea type="text" class="form-control" name="content" placeholder="What do you have to do?"></textarea>
		<div class="form-group">
			<div class="col-md-8">
				<div class="form-group row">
					<div class="col-md-8">
						<div class="input-append bootstrap-timepicker input-group">
							<input class="form-control datepicker" type="text" name="do_by" data-date-format="dd/mm/yyyy" placeholder="When do you have to do it by?" value="<?php date("d/m/Y"); ?>">
							<span class="input-group-addon"><span class="add-on"><span class="glyphicon glyphicon-calendar"></span></span></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-append bootstrap-timepicker input-group">
							<input type="text" class="form-control timepicker" name="do_by_time" value="<?php echo date("H:i"); ?>">
							<span class="input-group-addon"><span class="add-on"><span class="glyphicon glyphicon-time"></span></span></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm" name="add_todo">Add To-Do</button>
		</div>
	</form>
	<?php
}elseif(isset($pageInfo[2]) AND !empty($pageInfo[2]) AND $pageInfo[2] == "extend"){
	if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
		$todo_id = (int)$pageInfo[3];
		$find_todo = $sql->num_rows($sql->query("SELECT `id` FROM `todo` WHERE `id` = $todo_id AND `user_id` = $global_user_id AND `done` = 0"));
		if($find_todo == 0){
			echo page::alert("No To-Do was found with this ID or it is marked as done!", "danger");
		}else{
			if(isset($_POST['extend_todo'])){
				$errors = array();
				if(empty($_POST['do_by'])){
					$errors[] = "You forgot to enter the date you have to do it by!";
				}
				if(empty($_POST['do_by_time'])){
					$errors[] = "You forgot to enter the time you have to do it by!";
				}
				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$do_by = $sql->smart($_POST['do_by']);
					$do_by_time = $sql->smart($_POST['do_by_time']);
					$sql->query("UPDATE `todo` SET `do_by` = $do_by, `do_by_time` = $do_by_time, `dismissed` = 0, `extended` = `extended` + 1 WHERE `id` = $todo_id");
					page::alert("To-Do List item extended successfully!", "success");
					header("Location: " . url . "/todo");
				}
			}
			$todo = $sql->fetch_array($sql->query("SELECT * FROM `todo` WHERE `id` = $todo_id"));
			?>
			<form method="POST" class="form-horizontal" style="width: 700px; margin: 0 auto;">
				<textarea type="text" class="form-control" placeholder="What do you have to do?" disabled><?=$todo['content']?></textarea>
				<div class="form-group">
					<div class="col-md-8">
						<div class="form-group row">
							<div class="col-md-8">
								<input type="text" class="form-control datepicker" name="do_by" placeholder="When do you have to do it by?" value="<?=$todo['do_by']?>">
							</div>
							<div class="col-md-4">
								<div class="input-append bootstrap-timepicker input-group">
									<input type="text" class="form-control timepicker" name="do_by_time" value="<?=$todo['do_by_time']?>">
									<span class="input-group-addon"><span class="add-on"><span class="glyphicon glyphicon-time"></span></span></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="extend_todo">Extend To-Do</button>
				</div>
			</form>
			<?php
		}
	}else{
		echo page::alert("No ID was specified!", "danger");
	}
}else{
	?>
	<div style="margin-bottom: 10px;"><span class="glyphicon glyphicon-plus"></span> <a href="<?=url?>/todo/add">Add To-Do</a></div>
	<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>To Do</th>
				<th>Added</th>
				<th>Do By</th>
				<th>Done</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(isset($_POST['done'])){
				$id = $sql->smart($_POST['id']);
				$time = $sql->smart(time());
				$sql->query("UPDATE `todo` SET `done` = 1, `done_on` = $time WHERE `id` = $id AND `user_id` = $global_user_id");
				page::alert("To-Do List item updated as done!", "success");
			}
			$todos = $sql->query("SELECT * FROM `todo` WHERE `user_id` = $global_user_id ORDER BY `id` DESC");
			while($todo = $sql->fetch_array($todos)){
				$done = ($todo['done'] == 1) ? "ok" : "remove";
				$do_by = strtotime(str_replace("/", "-", $todo['do_by'] . " " . $todo['do_by_time']));
				$now = time();
				if($do_by < $now AND $todo['done'] == 0){
					// To-Do is not done and it has went over the deadline
					$row = "danger";
				}elseif($todo['do_by'] == date("d/m/Y") AND $todo['done'] == 0){
					// To-Do is not done and today's date is the deadline
					$row = "warning";
				}elseif($todo['done'] == 1){
					// To-Do is Done
					$row = "success";
				}else{
					// To-Do status is unkown
					$row = "";
				}
				?>
				<form method="POST">
					<input type="hidden" name="id" value="<?=$todo['id']?>">
					<tr class="<?=$row?>">
						<td><?=$todo['content']?></td>
						<td><?=$todo['added']?></td>
						<td width="150px"><?=$todo['do_by']?> <?=$todo['do_by_time']?></td>
						<td><span class="glyphicon glyphicon-<?=$done?>"></span></td>
						<?php 
						$button = ($todo['done'] == 1) ? " disabled" : "";
						?>
						<td width="300px">
							<button type="submit" class="btn btn-success btn-sm" name="done"<?=$button?>>Done <?php echo (empty($todo['done_on'])) ? "" : "[ " . date("d/m/Y", $todo['done_on']) . " " . date("H:i", $todo['done_on']) . " ]"; ?></button>
							<a href="<?=url?>/todo/extend/<?=$todo['id']?>"><button type="button" class="btn btn-primary btn-sm" name="done"<?=$button?>>Extend [ <?php echo $todo['extended']; ?> ]</button></a>
						</td>
					</tr>
				</form>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}
?>