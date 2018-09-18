<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['subcat_id']) AND isset($_POST['insert_element_id']) AND page::isLoggedIn()){
	$subcat_id = $_POST['subcat_id'];
	$items = $sql->query("SELECT * FROM `stock_items` WHERE `subcat_id` = $subcat_id");
	if($sql->num_rows($items) == 0){
		?>
		<select class="form-control">
			<option selected disabled>Category empty</option>
		</select>
		<?php
	}else{
		$element_id = $_POST['insert_element_id'];
		?>
		<select class="form-control" onChange="fillUPC(this, <?=$element_id?>)">
			<option selected disabled>Choose item</option>
			<?php
			while($item = $sql->fetch_array($items)){
				?>
				<option value="<?=$item['upc']?>"><?=$item['description']?></option>
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