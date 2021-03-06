<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['item_name']) AND page::isLoggedIn()){
	$item_name = $_POST['item_name'];
	$search_name = $sql->smart("%$item_name%");
	$items = $sql->query("SELECT * FROM `stock_items` WHERE `description` LIKE $search_name OR `upc` LIKE $search_name");
	if($sql->num_rows($items) == 0){
		page::alert("No items were found!", "danger");
	}else{
		?>
		<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">UPC</th>
					<th>Description</th>
					<th>Price</th>
					<th class="text-center">Purchases</th>
					<th class="text-center">Op. Stock</th>
					<th class="text-center">RMA</th>
					<th class="text-center">Shipped</th>
					<th class="text-center">Physical</th>
					<th class="text-center">Committed</th>
					<th class="text-center">Available</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($sql->num_rows($items) == 0){
					?>
					<tr>
						<td class="danger" colspan="8">No items were found in this category!</td>
					</tr>
					<?php
				}else{
					$i = 1;
					while($item = $sql->fetch_array($items)){
						$upc = $item['upc'];
						?>
						<tr>
							<td class="text-center"><?=$i?></td>
							<td class="text-center"><?=$upc?></td>
							<td><?=$item['description']?></td>
							<?php
							$price = $sql->fetch_array($sql->query("SELECT `price_exc_vat` FROM `purchase_items` WHERE `upc` = $upc ORDER BY `id` DESC LIMIT 1"));
							?>
							<td>£<?=(empty($price['price_exc_vat'])) ? "0.00" : $price['price_exc_vat']?></td>
							<?php
							$purchases = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `purchase_items` WHERE `upc` = $upc"));
							$credit_notes = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `credit_note_items` WHERE `upc` = $upc"));
							$purchased = $purchases['total'] - $credit_notes['total'];
							if($purchased == 0){
								?>
								<td class="text-center">0</td>
								<?php
							}else{
								?>
								<td class="text-center"><a href="<?=url?>/purchases/search/<?=$upc?>" target="_blank"><?=$purchased?></a></td>
								<?php
							}
							$opening_stock = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `opening_stock` WHERE `upc` = $upc"));
							$opening_stock_total = ($opening_stock['total'] == 0) ? 0 : $opening_stock['total'];
							if($opening_stock_total != 0 AND page::hasLevel(1)){
								?>
								<td class="text-center"><a href="<?=url?>/stock/opening/search/<?=$upc?>" target="_blank"><?=$opening_stock_total?></a></td>
								<?php
							}else{
								?>
								<td class="text-center"><?=$opening_stock_total?></td>
								<?php
							}
							$rma = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `rma_items` WHERE `upc` = $upc AND `return_action` = 'outstanding'"));
							if($rma['total'] == 0){
								?>
								<td class="text-center">0</td>
								<?php
							}else{
								?>
								<td class="text-center"><a href="<?=url?>/rma/search/<?=$upc?>" target="_blank"><?=$rma['total']?></a></td>
								<?php
							}
							$shipped_total = page::returnShipped($upc);
							if($shipped_total == 0){
								?>
								<td class="text-center">0</td>
								<?php
							}else{
								?>
								<td class="text-center"><a href="<?=url?>/orders/search/<?=$upc?>" target="_blank"><?=$shipped_total?></a></td>
								<?php
							}
							$physical = ($purchased) - ((empty($rma['total'])) ? "0" : $rma['total']) + $opening_stock_total - $shipped_total;
							?>
							<td class="text-center"><?=$physical?></td>
							<?php
							$committed_total = page::returnCommitted($upc);
							if($committed_total == 0){
								?>
								<td class="text-center">0</td>
								<?php
							}else{
								?>
								<td class="text-center"><a href="<?=url?>/orders/search/<?=$upc?>" target="_blank"><?=$committed_total?></a></td>
								<?php
							}
							?>
							<td class="text-center"><?=$physical-$committed_total?></td>
						</tr>
						<?php
						$i++;
					}
				}
				?>
			</tbody>
		</table>
		<?php
	}
}else{
	exit;
}
?>