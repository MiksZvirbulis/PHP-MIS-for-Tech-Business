<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['insert_element_id']) AND isset($_POST['cat_id']) AND page::isLoggedIn()){
	$insert_element_id = $_POST['insert_element_id'];
	$cat_id = $_POST['cat_id'];
	$subcats = $sql->query("SELECT * FROM `stock_subcat` WHERE `cat_id` = $cat_id");
	?>
	<select class="form-control" name="subcat_id" id="select_subcat_<?=$insert_element_id?>">
		<option selected disabled>Choose subcategory</option>
		<?php
		while($subcat = $sql->fetch_array($subcats)){
			?>
			<option value="<?=$subcat['subcat_id']?>"><?=$subcat['description']?></option>
			<?php
		}
		?>
	</select>
	<?php
}else{
	exit;
}
?>