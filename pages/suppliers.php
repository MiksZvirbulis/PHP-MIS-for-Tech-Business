<?php
$page = new page("Manage Suppliers", 1);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2])){
	$action = $pageInfo[2];
	if($action == "add"){
		$page->changeTitle("Add supplier");
		if(isset($_POST['add_supplier'])){
			$errors = array();
			if(empty($_POST['name'])){
				$errors[] = "You forgot to enter the name of the supplier!";
			}else{
				$nameTest = $sql->smart($_POST['name']);
				$test_name = $sql->num_rows($sql->query("SELECT `id` FROM `suppliers` WHERE `name` = $nameTest"));
				if($test_name == 1){
					$errors[] = "A supplier with this name already exists!";
				}
			}
			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$name = $sql->smart($_POST['name']);
				$contact_name = $sql->smart($_POST['contact_name']);
				$contact_number = $sql->smart($_POST['contact_number']);
				$contact_email = $sql->smart($_POST['contact_email']);
				$web_account_username = $sql->smart($_POST['web_account_username']);
				$web_account_password = $sql->smart($_POST['web_account_password']);
				$website = $sql->smart($_POST['website']);
				if(isset($_POST['active'])){
					$active = 1;
				}else{
					$active = 0;
				}
				$sql->query("INSERT INTO `suppliers` (`name`, `contact_name`, `contact_number`, `contact_email`, `web_account_username`, `web_account_password`, `website`, `active`, `created_by_id`) VALUES ($name, $contact_name, $contact_number, $contact_email, $web_account_username, $web_account_password, $website, $active, $global_user_id)");
				page::alert("Supplier has been added successfully!", "success");
			}
		}
		?>
		<form method="POST">
			<input type="text" class="form-control" name="name" placeholder="Name of the supplier*">
			<input type="text" class="form-control" name="contact_name" placeholder="Contact person">
			<input type="text" class="form-control" name="contact_number" placeholder="Contact number">
			<input type="text" class="form-control" name="contact_email" placeholder="Contact email">
			<input type="text" class="form-control" name="web_account_username" placeholder="Web account username">
			<input type="text" class="form-control" name="web_account_password" placeholder="Web account password">
			<input type="text" class="form-control" name="website" placeholder="Website">
			<div class="checkbox">
				<label>
					<input type="checkbox" name="active" checked>
					<span class="lbl"> Active</span>
				</label>
			</div>
			* Required
			<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
				<button type="submit" class="btn btn-primary btn-sm" name="add_supplier">Add supplier</button>
			</div>
		</form>
		<?php
	}elseif($action == "del"){
		$page->changeTitle("Delete supplier");
		if(isset($pageInfo[3])){
			$supplier_id = (int)$pageInfo[3];
			$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `suppliers` WHERE `id` = $supplier_id"));
			if($check_existance == 0){
				page::alert("The supplier with that ID was not found!", "danger");
			}else{
				$sql->query("DELETE FROM `suppliers` WHERE `id` = $supplier_id");
				page::alert("Supplier successfully deleted!<br />Redirecting...", "success");
				header("refresh:2;url=".url."/suppliers");
			}
		}else{
			page::alert("No ID was specified!", "danger");
		}
	}elseif($action == "edit"){
		$page->changeTitle("Edit supplier");
		if(isset($pageInfo[3])){
			$supplier_id = (int)$pageInfo[3];
			$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `suppliers` WHERE `id` = $supplier_id"));
			if($check_existance == 0){
				page::alert("The supplier with that ID was not found!", "danger");
			}else{
				if(isset($_POST['edit_supplier'])){
					$errors = array();
					if(empty($_POST['name'])){
						$errors[] = "You forgot to enter the name of the supplier!";
					}else{
						$current_name = $sql->fetch_array($sql->query("SELECT `name` FROM `suppliers` WHERE `id` = $supplier_id"));
						if($current_name['name'] != $_POST['name']){
							$nameTest = $sql->smart($_POST['name']);
							$test_name = $sql->num_rows($sql->query("SELECT `id` FROM `suppliers` WHERE `name` = $nameTest"));
							if($test_name == 1){
								$errors[] = "A supplier with this name already exists!";
							}
						}
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$name = $sql->smart($_POST['name']);
						$contact_name = $sql->smart($_POST['contact_name']);
						$contact_number = $sql->smart($_POST['contact_number']);
						$contact_email = $sql->smart($_POST['contact_email']);
						$web_account_username = $sql->smart($_POST['web_account_username']);
						$web_account_password = $sql->smart($_POST['web_account_password']);
						$website = $sql->smart($_POST['website']);
						if(isset($_POST['active'])){
							$active = 1;
						}else{
							$active = 0;
						}
						$sql->query("UPDATE `suppliers` SET `name` = $name, `contact_name` = $contact_name, `contact_number` = $contact_number, `contact_email` = $contact_email, `web_account_username` = $web_account_username, `web_account_password` = $web_account_password, `website` = $website, `active` = $active WHERE `id` = $supplier_id");
						$supplier = $sql->fetch_array($sql->query("SELECT * FROM `suppliers` WHERE `id` = $supplier_id"));
						page::alert("Supplier has been edited successfully!", "success");
					}
				}
				$supplier = $sql->fetch_array($sql->query("SELECT * FROM `suppliers` WHERE `id` = $supplier_id"));
				$active = ($supplier['active'] == 1) ? " checked" : "";
				?>
				<form method="POST">
					<input type="text" class="form-control" name="name" placeholder="Name of the supplier*" value="<?=$supplier['name']?>">
					<input type="text" class="form-control" name="contact_name" placeholder="Contact person" value="<?=$supplier['contact_name']?>">
					<input type="text" class="form-control" name="contact_number" placeholder="Contact number" value="<?=$supplier['contact_number']?>">
					<input type="text" class="form-control" name="contact_email" placeholder="Contact email" value="<?=$supplier['contact_email']?>">
					<input type="text" class="form-control" name="web_account_username" placeholder="Web account username" value="<?=$supplier['web_account_username']?>">
					<input type="text" class="form-control" name="web_account_password" placeholder="Web account password" value="<?=$supplier['web_account_password']?>">
					<input type="text" class="form-control" name="website" placeholder="Website" value="<?=$supplier['website']?>">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="active"<?=$active?>>
							<span class="lbl"> Active</span>
						</label>
					</div>
					* Required
					<div style="text-align: right; margin-top: 10px; margin-bottom: 50px;">
						<button type="submit" class="btn btn-primary btn-sm" name="edit_supplier">Edit supplier</button>
					</div>
				</form>
				<?php
			}
		}else{
			page::alert("No ID was specified!", "danger");
		}
	}else{
		page::alert("This action could not be found!", "danger");
	}
}else{
	?>
	<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Name</th>
				<th>Contact person</th>
				<th>Contact number</th>
				<th>Contact email</th>
				<th>Web account username</th>
				<th>Web account password</th>
				<th>Website</th>
				<th>Active</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$suppliers = $sql->query("SELECT * FROM `suppliers` ORDER BY `name` ASC");
			while($supplier = $sql->fetch_array($suppliers)){
				?>
				<tr>
					<td><b><?=$supplier['name']?></b></td>
					<td><?=$supplier['contact_name']?></td>
					<td><?=$supplier['contact_number']?></td>
					<td><a href="mailto:<?=$supplier['contact_email']?>"><?=$supplier['contact_email']?></a></td>
					<td><?=$supplier['web_account_username']?></td>
					<td><?=$supplier['web_account_password']?></td>
					<td><a href="<?=$supplier['website']?>" target="_blank"><?=$supplier['website']?></a></td>
					<td class="text-center"><?=($supplier['active'] == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>'?></td>
					<td>
						<a class="confirm" href="<?=url?>/suppliers/del/<?=$supplier['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-remove" style="opacity:0.4;"></span> Delete</button></a>
						<a href="<?=url?>/suppliers/edit/<?=$supplier['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
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