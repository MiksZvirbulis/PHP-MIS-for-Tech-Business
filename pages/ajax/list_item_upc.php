<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['item_name']) AND page::isLoggedIn()){
	$item_name = $_POST['item_name'];
	$search_name = $sql->smart("%$item_name%");
	$items = $sql->query("SELECT * FROM `stock_items` WHERE `description` LIKE '%" . $item_name . "%' OR `upc` LIKE $search_name");
	?>
	<div class="list-group">
		<a class="list-group-item disabled">
			Search Results For: <strong><?=$item_name?></strong>
		</a>
		<?php if($sql->num_rows($items) == 0): ?>
			<a class="list-group-item list-group-item-danger">
				No items were found
			</a>
		<?php else: ?>
			<?php while($item = $sql->fetch_array($items)): ?>
				<a class="list-group-item pointer" onClick="fillitemUPC('<?=$item['upc']?>')">
					<?=$item['description']?> - <?=page::getCatInfo($item['cat_id'], "description")?> - <?=page::getSubcatInfo($item['subcat_id'], "description")?>
					<span class="badge"><?=$item['upc']?></span>
				</a>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
	<?php
}else{
	exit;
}
?>