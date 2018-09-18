<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['order_id']) AND page::isLoggedIn()){
	$order_id = $_POST['order_id'];
	$order = $sql->fetch_array($sql->query("SELECT * FROM `orders` WHERE `order_id` = $order_id"))
	?>
	<form method="POST">
		<?php
		if(isset($_POST['update_items']) AND isset($_POST['item_id']) AND isset($_POST['quantity']) AND isset($_POST['item_cost_exc_vat']) AND isset($_POST['spec_id'])){
			if($order['order_status'] == "completed"){
				if(page::hasLevel(1)){
					$proceed = true;
				}
			}else{
				$proceed = true;
			}
			if(isset($proceed) AND $proceed){
				foreach($_POST['item_id'] as $key => $item_id){
					$quantity = $sql->smart($_POST['quantity'][$key]);
					$item_cost_exc_vat = $sql->smart($_POST['item_cost_exc_vat'][$key]);
					$sql->query("UPDATE `order_items` SET `quantity` = $quantity, `cost_exc_vat` = $item_cost_exc_vat WHERE `item_id` = $item_id");
				}
				foreach($_POST['spec_id'] as $key => $spec_id){
					if(isset($_POST['cost_exc_vat'][$key])){
						$cost_exc_vat = $sql->smart($_POST['cost_exc_vat'][$key]);
					}else{
						$cost_exc_vat = $sql->smart("");
					}
					if(isset($_POST['spec_quantity'][$key])){
						$spec_quantity = $sql->smart($_POST['spec_quantity'][$key]);
					}else{
						$spec_quantity = $sql->smart("");
					}
					$sql->query("UPDATE `order_specs` SET `cost_exc_vat` = $cost_exc_vat, `quantity` = $spec_quantity WHERE `spec_id` = $spec_id");
				}
				page::alert("Items successfully updated!", "success");
			}
		}
		$iii = 1;
		$find_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id ORDER BY `type` DESC");
		while($spec = $sql->fetch_array($find_specs)){
			$spec_id = $sql->smart($spec['spec_id']);
			$find_items = $sql->query("SELECT * FROM `order_items` WHERE `order_spec_id` = $spec_id");
			if($spec['type'] == "pc"){
				$type = "PC-" . $iii;
			}elseif($spec['type'] == "parts"){
				$type = "Parts";
			}else{
				$type = "Services";
			}
			?>
			<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th colspan="2"><?=$type?><input type="hidden" name="spec_id[]" value="<?=$spec['spec_id']?>"></th>
						<?php
						if($spec['type'] == "pc"){
							?>
							<th></th>
							<th style="width: 50px;"><input class="form-control input-sm" type="number" name="spec_quantity[]" placeholder="Quantity" min="1" value="<?=$spec['quantity']?>"></th>
							<th width="100px"><input type="text" class="form-control" name="cost_exc_vat[]" placeholder="Cost exc VAT" onclick="money(this)" onblur="money(this)" value="<?=$spec['cost_exc_vat']?>"></th>
							<?php
						}else{
							?>
							<th colspan="4"></th>
							<?php
						}
						?>
					</tr>
					<tr>
						<th>#</th>
						<?php
						if($spec['type'] == "aservices"){
							?>
							<th>Service</th>
							<?php
						}else{
							?>
							<th>UPC</th>
							<th>Item Name</th>
							<?php
						}
						?>
						<th>Quantity</th>
						<?php
						if($spec['type'] != "pc"){
							?>
							<th>Cost exc VAT</th>
							<?php
						}
						?>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$iiii = 1;
					while($item = $sql->fetch_array($find_items)){
						?>
						<tr>
							<td style="width: 50px;"><?=$iiii?><input type="hidden" name="item_id[]" value="<?=$item['item_id']?>"></td>
							<?php
							if($spec['type'] == "aservices"){
								?>
								<td><?=page::getServiceInfo($item['service_id'], "description")?></td>
								<?php
							}else{
								?>
								<td style="width: 50px;"><?=$item['upc']?></td>
								<td><?=page::getItemInfo($item['upc'], "description")?></td>
								<?php
							}
							?>
							<td style="width: 50px;"><input class="form-control input-sm" type="number" id="quantity_<?=$i?>" name="quantity[]" placeholder="Quantity" min="1" value="<?=$item['quantity']?>"></td>
							<?php
							if($spec['type'] != "pc"){
								?>
								<td style="width: 150px;"><input class="form-control input-sm" type="text" name="item_cost_exc_vat[]" placeholder="Cost exc VAT" onclick="money(this)" onblur="money(this)" value="<?=$item['cost_exc_vat']?>"></td>
								<?php
							}else{
								?>
								<input type="hidden" name="item_cost_exc_vat[]" value="">
								<?php
							}
							if($order['order_status'] == "completed"){
								if(page::hasLevel(1)){
									?>
									<td style="width: 50px;"><span class="pointer glyphicon glyphicon-remove" onClick="removeItem(<?=$item['item_id']?>, <?=$order_id?>)"></span></td>
									<?php
								}else{
									?>
									<td style="width: 50px;"></td>
									<?php
								}
							}else{
								?>
								<td style="width: 50px;"><span class="pointer glyphicon glyphicon-remove" onClick="removeItem(<?=$item['item_id']?>, <?=$order_id?>)"></span></td>
								<?php
							}
							?>
						</tr>
						<?php
						$iiii++;
					}
					?>
				</tbody>
			</table>
			<?php
			$iii++;
		}
		if($order['order_status'] == "completed"){
			if(page::hasLevel(1)){
				?>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="update_items">Update items</button>
				</div>
				<?php
			}
		}else{
			?>
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="update_items">Update items</button>
			</div>
			<?php
		}
		?>
	</form>
	<?php
}else{
	exit;
}
?>