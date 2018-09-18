<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['category_id']) AND page::isLoggedIn()){
	$category_id = $_POST['category_id'];
	$type = $sql->smart($_POST['type']);
	$templates = $sql->query("SELECT * FROM `email_templates` WHERE `cat_id` = $category_id AND `type` = $type");
	if($sql->num_rows($templates) == 0){
		?>
		<div class="form-group">
			<select class="form-control">
				<option selected disabled>Category empty</option>
			</select>
		</div>
		<?php
	}else{
		?>
		<div class="form-group">
			<select class="form-control" id="template">
				<option selected disabled>Choose template</option>
				<?php
				while($template = $sql->fetch_array($templates)){
					?>
					<option value="<?=$template['id']?>"><?=$template['description']?></option>
					<?php
				}
				?>
			</select>
		</div>
		<?php
	}
}else{
	exit;
}
?>