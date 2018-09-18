<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['cat_id']) AND page::isLoggedIn()){
	$cat_id = $_POST['cat_id'];
	if($cat_id == "all"){
		?>
		<select class="form-control" name="subcat_id" id="select_subcat">
			<option selected disabled>Choose subcategory</option>
			<option value="all">All Items</option>
		</select>
		<?php
	}else{
		$subcats = $sql->query("SELECT * FROM `stock_subcat` WHERE `cat_id` = $cat_id");
		?>
		<select class="form-control" name="subcat_id" id="select_subcat">
			<option selected disabled>Choose subcategory</option>
			<?php if($_POST['all'] == true): ?>
				<option value="cat-<?=$cat_id?>">All</option>
			<?php endif; ?>
			<?php
			while($subcat = $sql->fetch_array($subcats)){
				?>
				<option value="<?=$subcat['subcat_id']?>"><?=$subcat['description']?></option>
				<?php
			}
			?>
		</select>
		<?php
	}
}else{
	exit;
}
?>