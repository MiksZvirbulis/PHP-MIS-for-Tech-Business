<?php
$page = new page("Manage Warranty", 3);
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}

if($action == "add"){
	echo "add";
}elseif($action == "edit"){
	echo "edit";
}elseif($action == "del"){
	echo "del";
}elseif($action == "options"){
	?>
	<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>#</th>
				<th>Length</th>
				<th>Description</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			<form method="POST">
				<?php
				$i = 1;
				if(isset($_POST['update'])){
					$errors = array();
					$options = array();
					if(isset($_POST['id']) AND isset($_POST['length']) AND isset($_POST['description'])){
						$id = $_POST['id'];
						$lengts = $_POST['length'];
						$descriptions = $_POST['description'];
						$delete = isset($_POST['delete']) ? $_POST['delete'] : array();
						foreach($id as $key => $option_id){
							if(empty($lengts[$key])){
								$errors[] = "You left the options length empty!";
								$length = false;
							}else{
								$length = $lengts[$key];
							}
							if(empty($descriptions[$key])){
								$errors[] = "You left the options description empty!";
								$description = false;
							}else{
								$description = $descriptions[$key];
							}
							if(isset($delete[$key])){
								$delete_option = true;
							}else{
								$delete_option = false;
							}
							if($length != false AND $description != false){
								$options[] = array("id" => $option_id, "length" => $length, "description" => $description, "delete" => $delete_option);
							}
						}
					}

					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						foreach($options as $option){
							$id = $sql->smart($option['id']);
							$length = $sql->smart($option['length']);
							$description = $sql->smart($option['description']);
							if($option['delete']){
								$sql->query("DELETE FROM `warranty_options` WHERE `id` = $id");
							}else{
								$sql->query("UPDATE `warranty_options` SET `length` = $length, `description` = $description WHERE `id` = $id");
							}
						}
						page::alert("Warranty options successfully updated!", "success");
					}
				}
				if(isset($_POST['add'])){
					$errors = array();
					if(empty($_POST['length'])){
						$errors[] = "You forgot to enter the length!";
					}
					if(empty($_POST['description'])){
						$errors[] = "You forgot to enter the description!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$length = $sql->smart($_POST['length']);
						$description = $sql->smart($_POST['description']);
						$sql->query("INSERT INTO `warranty_options` (`length`, `description`) VALUES ($length, $description)") or die(mysql_error());
						page::alert("Warranty option successfully added!", "success");
					}
				}
				$warranty_options = $sql->query("SELECT * FROM `warranty_options` ORDER BY `id` ASC");
				while($warranty_option = $sql->fetch_array($warranty_options)){
					?>
					<tr>
						<td><?=$i?><input type="hidden" name="id[<?=$i?>]" value="<?=$warranty_option['id']?>"></td>
						<td><input type="text" name="length[<?=$i?>]" class="form-control" placeholder="Length" value="<?=$warranty_option['length']?>"></td>
						<td><input type="text" name="description[<?=$i?>]" class="form-control" placeholder="Description" value="<?=$warranty_option['description']?>"></td>
						<td>
							<label>
								<input type="checkbox" name="delete[<?=$i?>]">
								<span class="lbl"></span>
							</label>
						</td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td colspan="3"></td>
					<td><button type="submit" class="btn btn-default" name="update">Update</button></td>
				</tr>
			</form>
			<tr class="active">
				<td colspan="4">Add New Warranty Option</td>
			</tr>
			<form method="POST">
				<tr>
					<td class="text-center"><span class="glyphicon glyphicon-plus"></span></td>
					<td><input type="text" name="length" class="form-control" placeholder="Length"></td>
					<td><input type="text" name="description" class="form-control" placeholder="Description"></td>
					<td><button type="submit" class="btn btn-default" name="add">Add</button></td>
				</tr>
			</form>
		</tbody>
	</table>
	<?php
}
?>