<?php
$page = new page("Services", 3);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2])){
	$action = $pageInfo[2];
	if($action == "add" AND page::moduleAccess(2)){
		if(isset($_POST['add_service'])){
			$errors = array();
			if(empty($_POST['title'])){
				$errors[] = "Do not forget to fill out the title of the service!";
			}
			if(empty($_POST['description'])){
				$errors[] = "Do not forget to fill out the description of the service!";
			}
			if(empty($_POST['cost'])){
				$errors[] = "Do not forget to fill out the cost of the service!";
			}
			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$title = $sql->smart($_POST['title']);
				$description = $sql->smart($_POST['description']);
				$cost = $sql->smart($_POST['cost']);
				$sql->query("INSERT INTO `services` (`title`, `description`, `cost`, `created_by_id`) VALUES ($title, $description, $cost, $global_user_id)");
				page::alert("Service successfully added!", "success");
			}
		}
		?>
		<form method="POST">
			<input type="text" class="form-control" name="title" placeholder="Title of service">
			<textarea type="text" class="form-control res-dis" name="description" placeholder="Description of service"></textarea>
			<div class="input-group">
				<span class="input-group-addon">₤</span>
				<input type="text" class="form-control" onclick="money(this)" onblur="money(this)" name="cost" placeholder="Cost (xx.xx)" value="0.00">
			</div>
			<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
				<button type="submit" class="btn btn-primary btn-sm" name="add_service">Add</button>
			</div>
		</form>
		<?php
	}elseif($action == "edit" AND page::moduleAccess(2)){
		if(isset($pageInfo[3])){
			$service_id = (int)$pageInfo[3];
			$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `services` WHERE `id` = $service_id"));
			if($check_existance == 0){
				page::alert("There are no services with the ID specified!", "danger");
			}else{
				if(isset($_POST['edit_service'])){
					$errors = array();
					if(empty($_POST['title'])){
						$errors[] = "Do not forget to fill out the title of the service!";
					}
					if(empty($_POST['description'])){
						$errors[] = "Do not forget to fill out the description of the service!";
					}
					if(empty($_POST['cost'])){
						$errors[] = "Do not forget to fill out the cost of the service!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$title = $sql->smart($_POST['title']);
						$description = $sql->smart($_POST['description']);
						$cost = $sql->smart($_POST['cost']);
						$sql->query("UPDATE `services` SET `title` = $title, `description` = $description, `cost` = $cost WHERE `id` = $service_id");
						page::alert("Service successfully edited!", "success");
						$service = $sql->fetch_array($sql->query("SELECT * FROM `services` WHERE `id` = $service_id"));
					}
				}
				$service = $sql->fetch_array($sql->query("SELECT * FROM `services` WHERE `id` = $service_id"));
				?>
				<form method="POST">
					<input type="text" class="form-control" name="title" placeholder="Title of service" value="<?=$service['title']?>">
					<textarea type="text" class="form-control res-dis" name="description" placeholder="Description of service"><?=$service['description']?></textarea>
					<div class="input-group">
						<span class="input-group-addon">₤</span>
						<input type="text" class="form-control" onclick="money(this)" onblur="money(this)" name="cost" placeholder="Cost (xx.xx)" value="<?=$service['cost']?>">
					</div>
					<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
						<button type="submit" class="btn btn-primary btn-sm" name="edit_service">Edit</button>
					</div>
				</form>
				<?php
			}
		}else{
			page::alert("No ID specified!", "danger");
		}
	}elseif($action == "del" AND page::moduleAccess(2)){
		if(isset($pageInfo[3])){
			$service_id = (int)$pageInfo[3];
			$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `services` WHERE `id` = $service_id"));
			if($check_existance == 0){
				page::alert("There are no services with the ID specified!", "danger");
			}else{
				$sql->query("DELETE FROM `services` WHERE `id` = $service_id");
				page::alert("Service successfully deleted!<br />Redirecting...", "success");
				header("refresh:2;url=".url."/services");
			}
		}else{
			page::alert("No ID specified!", "danger");
		}
	}else{
		page::alert("The specified action was not found!", "danger");
	}
}else{
	?>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#list" data-toggle="tab">List All</a></li>
		<li><a href="#search_by_name" data-toggle="tab">Search</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="list">
			<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 5px;">
				<thead>
					<tr>
						<th>Title</th>
						<th>Description</th>
						<th>Cost</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$services = $sql->query("SELECT * FROM `services` ORDER BY `title` ASC");
					while($service = $sql->fetch_array($services)){
						?>
						<tr>
							<td><?=$service['title']?></td>
							<td><?=$service['description']?></td>
							<td>£<?=$service['cost']?></td>
							<td>
								<a class="confirm" href="<?=url?>/services/del/<?=$service['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-remove" style="opacity:0.4;"></span> Delete</button></a>
								<a href="<?=url?>/services/edit/<?=$service['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="search_by_name">
			<input type="text" class="form-control gap-top" id="search_service" onkeyup="get_services()" placeholder="Search for service...">
			<div id="loader"></div>
			<div id="services"></div>
		</div>
	</div>
	<?php
}
?>