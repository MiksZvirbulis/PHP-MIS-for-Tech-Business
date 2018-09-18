<?php
$page = new page("Manage Emails", 2);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}

if($action == "categories"){
	if(isset($pageInfo[3]) AND $pageInfo[3] == "add"){
		if(isset($_POST['add_cat'])){
			$errors = array();
			if(empty($_POST['description'])){
				$errors[] = "You forgot to enter the description for the category!";
			}
			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$description = $sql->smart($_POST['description']);
				$sql->query("INSERT INTO `email_categories` (`description`) VALUES ($description)");
				page::alert("Category added successfully!", "success");
			}
		}
		?>
		<form method="POST">
			<input type="text" class="form-control" name="description" placeholder="Category description">
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="add_cat">Add category</button>
			</div>
		</form>
		<?php
	}else{
		?>
		<table class="table">
			<thead>
				<th>#</th>
				<th>Description</th>
				<th>Delete</th>
			</thead>
			<tbody>
				<form method="POST">
					<?php $categories = $sql->query("SELECT * FROM `email_categories` ORDER BY `description` ASC"); ?>
					<?php $i = 1; ?>
					<?php
					if(isset($_POST['update'])){
						$errors = array();
						$options = array();
						if(isset($_POST['cat_id']) AND isset($_POST['description'])){
							$cat_id = $_POST['cat_id'];
							$descriptions = $_POST['description'];
							$delete = isset($_POST['delete']) ? $_POST['delete'] : array();
							foreach($cat_id as $key => $option_id){
								if(empty($descriptions[$key])){
									$errors[] = "You forgot to enter the description for the category!";
									$description = false;
								}else{
									$description = $descriptions[$key];
								}
								if(isset($delete[$key])){
									$delete_option = true;
								}else{
									$delete_option = false;
								}
								if($description != false){
									$options[] = array("cat_id" => $option_id, "description" => $description, "delete" => $delete_option);
								}
							}
						}

						if(count($errors) > 0){
							foreach($errors as $error){
								page::alert($error, "danger");
							}
						}else{
							foreach($options as $option){
								$cat_id = $sql->smart($option['cat_id']);
								$description = $sql->smart($option['description']);
								if($option['delete']){
									$sql->query("DELETE FROM `email_categories` WHERE `id` = $cat_id");
								}else{
									$sql->query("UPDATE `email_categories` SET `description` = $description WHERE `id` = $cat_id");
								}
							}
							$categories = $sql->query("SELECT * FROM `email_categories` ORDER BY `description` ASC");
							page::alert("Email categories successfully updated!", "success");
						}
					}
					?>
					<?php while($category = $sql->fetch_array($categories)): ?>
						<tr>
							<td><?php echo $i; ?><input type="hidden" name="cat_id[<?php echo $i; ?>]" value="<?php echo $category['id']; ?>"></td>
							<td><input type="text" class="form-control" name="description[<?php echo $i; ?>]" value="<?php echo $category['description']; ?>" placeholder="Category description"></td>
							<td>
								<input type="checkbox" name="delete[<?php echo $i; ?>]">
								<span class="lbl"></span>
							</td>
						</tr>
						<?php $i++; ?>
					<?php endwhile; ?>
					<tr>
						<td colspan="2"></td>
						<td><button type="submit" class="btn btn-default" name="update">Update</button></td>
					</tr>
				</form>
			</tbody>
		</table>
		<?php
	}
}elseif($action == "add"){
	if(isset($_POST['add_template'])){
		$errors = array();
		if(empty($_POST['cat_id'])){
			$errors[] = "You forgot to select the templates category!";
		}
		if(empty($_POST['type'])){
			$errors[] = "You forgot to select the templates type!";
		}
		if(empty($_POST['description'])){
			$errors[] = "You forgot to enter the templates description!";
		}
		if(empty($_POST['subject'])){
			$errors[] = "You forgot to enter the emails subject!";
		}
		if(empty($_POST['content'])){
			$errors[] = "You forgot to enter the emails content!";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$cat_id = $sql->smart($_POST['cat_id']);
			$type = $sql->smart($_POST['type']);
			$description = $sql->smart($_POST['description']);
			$subject = $sql->smart($_POST['subject']);
			$content = $sql->smart($_POST['content']);
			$sql->query("INSERT INTO `email_templates` (`cat_id`, `type`, `description`, `subject`, `content`, `created_by_id`, `last_updated_by_id`) VALUES ($cat_id, $type, $description, $subject, $content, $global_user_id, $global_user_id)");
			page::alert("Email template added successfully!", "success");
		}
	}
	?>
	<form method="POST">
		<select class="form-control" name="cat_id">
			<option selected disabled>Select category</option>
			<?php $categories = $sql->query("SELECT * FROM `email_categories` ORDER BY `description` ASC"); ?>
			<?php while($category = $sql->fetch_array($categories)): ?>
				<option value="<?php echo $category['id']; ?>"><?php echo $category['description']; ?></option>
			<?php endwhile; ?>
		</select>
		<select class="form-control" name="type">
			<option selected disabled>Select type</option>
			<option value="order">Order</option>
			<option value="receipt">Receipt</option>
		</select>
		<input type="text" class="form-control" name="description" placeholder="Template description...">
		<input type="text" class="form-control" name="subject" placeholder="Email subject...">
		<textarea type="text" class="form-control" name="content" rows="20" placeholder="Email content..."></textarea>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm" name="add_template">Add template</button>
		</div>
	</form>
	<?php
}elseif($action == "edit"){
	if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
		$id = (int)$pageInfo[3];
		$find_email = $sql->query("SELECT * FROM `email_templates` WHERE `id` = $id");
		if($sql->num_rows($find_email) == 0){
			page::alert("No email template was found with the ID specified!", "danger");
		}else{
			if(isset($_POST['update_template'])){
				$errors = array();
				if(empty($_POST['cat_id'])){
					$errors[] = "You forgot to select the templates category!";
				}
				if(empty($_POST['type'])){
					$errors[] = "You forgot to select the templates type!";
				}
				if(empty($_POST['description'])){
					$errors[] = "You forgot to enter the templates description!";
				}
				if(empty($_POST['subject'])){
					$errors[] = "You forgot to enter the emails subject!";
				}
				if(empty($_POST['content'])){
					$errors[] = "You forgot to enter the emails content!";
				}
				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$cat_id = $sql->smart($_POST['cat_id']);
					$type = $sql->smart($_POST['type']);
					$description = $sql->smart($_POST['description']);
					$subject = $sql->smart($_POST['subject']);
					$content = $sql->smart($_POST['content']);
					$sql->query("UPDATE `email_templates` SET `cat_id` = $cat_id, `type` = $type, `description` = $description, `subject` = $subject, `content` = $content, `last_updated_by_id` = $global_user_id WHERE `id` = $id");
					page::alert("Email template updated successfully!", "success");
					$find_email = $sql->query("SELECT * FROM `email_templates` WHERE `id` = $id");
				}
			}
			$email = $sql->fetch_array($find_email);
			?>
			<form method="POST">
				<select class="form-control" name="cat_id">
					<option selected disabled>Select category</option>
					<?php $categories = $sql->query("SELECT * FROM `email_categories` ORDER BY `description` ASC"); ?>
					<?php while($category = $sql->fetch_array($categories)): ?>
						<?php
						$selected = ($category['id'] == $email['cat_id']) ? " selected" : "";
						?>
						<option value="<?php echo $category['id']; ?>"<?php echo $selected; ?>><?php echo $category['description']; ?></option>
					<?php endwhile; ?>
				</select>
				<select class="form-control" name="type">
					<?php
					$order = ($email['type'] == "order") ? " selected" : "";
					$receipt = ($email['type'] == "receipt") ? " selected" : "";
					?>
					<option disabled>Select type</option>
					<option value="order"<?php echo $order; ?>>Order</option>
					<option value="receipt"<?php echo $receipt; ?>>Receipt</option>
				</select>
				<input type="text" class="form-control" name="description" placeholder="Template description..." value="<?php echo $email['description']; ?>" readonly>
				<input type="text" class="form-control" name="subject" placeholder="Email subject..." value="<?php echo $email['subject']; ?>">
				<textarea type="text" class="form-control" name="content" rows="20" placeholder="Email content..."><?php echo $email['content']; ?></textarea>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="update_template">Update template</button>
				</div>
			</form>
			<?php
		}
	}else{
		page::alert("No ID was specified!", "danger");
	}
}else{
	?>
	<table class="table">
		<thead>
			<th>Category</th>
			<th>Description</th>
			<th>Subject</th>
			<th>Created by</th>
			<th>Actions</th>
		</thead>
		<tbody>
			<?php $emails = $sql->query("SELECT * FROM `email_templates` ORDER BY `id` ASC"); ?>
			<?php while($email = $sql->fetch_array($emails)): ?>
				<?php
				$cat_id = $email['cat_id'];
				$category = $sql->fetch_array($sql->query("SELECT `description` FROM `email_categories` WHERE `id` = $cat_id"));
				?>
				<tr>
					<td><strong><?php echo $category['description']; ?></strong></td>
					<td><?php echo $email['description']; ?></td>
					<td><?php echo $email['subject']; ?></td>
					<td><?=page::getUserInfo($email['created_by_id'], "name")?> <?=page::getUserInfo($email['created_by_id'], "surname")?></td>
					<td>
						<a href="<?=url?>/emails/edit/<?=$email['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Edit</button></a>
					</td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	<?php
}