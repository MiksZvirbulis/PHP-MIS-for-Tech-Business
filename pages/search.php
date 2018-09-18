<?php
$page = new page("Search", 3);
if(isset($_POST['query']) AND isset($_POST['type'])){
	$search_query = $_POST['query'];
	if($_POST['type'] == "receipts"){
		header("Location: " . url . "/receipts/search/" . $search_query . "/");
	}else{
		header("Location: " . url . "/search/" . $search_query . "/");
	}
}
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$query = $pageInfo[2];
	$query = $sql->smart("%$query%");
	?>
	<legend>Orders</legend>
	<?php
	$invoices = $sql->query("SELECT `order_id` FROM `invoices` WHERE `invoice_number` LIKE $query LIMIT 1");
	if($sql->num_rows($invoices) > 0){
		$invoice = $sql->fetch_array($invoices);
		$order_id = $invoice['order_id'];
		$orders = $sql->query("SELECT * FROM `orders` WHERE `order_id` = $order_id");
	}else{
		$orders = $sql->query("SELECT * FROM `orders` WHERE `order_id` LIKE $query OR `first_name` LIKE $query OR `last_name` LIKE $query OR `company_name` LIKE $query OR `email` LIKE $query OR `mobile_number` LIKE $query OR `home_number` LIKE $query OR `work_number` LIKE $query OR `billing_line1` LIKE $query OR `billing_line2` LIKE $query OR `billing_line3` LIKE $query OR `billing_line4` LIKE $query OR `billing_postcode` LIKE $query OR `shipping_line1` LIKE $query OR `shipping_line2` LIKE $query OR `shipping_line3` LIKE $query OR `shipping_line4` LIKE $query OR `shipping_postcode` LIKE $query ORDER BY `order_id` DESC");
	}
	if($sql->num_rows($orders) == 0){
		page::alert("No orders were found!", "danger");
	}else{
		if(isset($_POST['update'])){
			if(isset($_POST['order_selected'])){
				$order_selected = $_POST['order_selected'];
				foreach($order_selected as $key => $selected){
					$get_status = $sql->fetch_array($sql->query("SELECT `order_status` FROM `orders` WHERE `order_id` = $selected"));
					if($get_status['order_status'] == "shipped"){
						if($page->hasLevel(1)){
							$proceed = true;
						}
					}else{
						$proceed = true;
					}
					if(isset($proceed) AND $proceed){
						$status = $sql->smart($_POST['status']);
						$sql->query("UPDATE `orders` SET `order_status` = $status WHERE `order_id` = $selected");
					}
				}
				if(count($order_selected) == 1){
					page::alert("The selected order was updated!", "success");
				}else{
					page::alert("The selected orders were updated!", "success");
				}
			}else{
				page::alert("No orders were selected!", "danger");
			}
		}
		$order_total = 0;
		$pc_total = 0;
		$parts_total = 0;
		$i = 1;
		?>
		<div class="pull-right">
			<button type="button" class="btn btn-default" style="margin-bottom: 11px;" onClick="duplicateEntry('order')">Duplicate Selected</button>
		</div>
		<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>
						<input type="checkbox" id="checkAll">
						<span class="lbl"></span>
					</th>
					<th>Order ID</th>
					<th>Invoice Number</th>
					<th>Name</th>
					<th>Reference</th>
					<th>Invoice date</th>
					<th>Shipping date</th>
					<th>Priority</th>
					<th>Status</th>
					<th>Total price</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				while($order = $sql->fetch_array($orders)){
					$order_id = $order['order_id'];
					$invoice = $sql->fetch_array($sql->query("SELECT `invoice_number` FROM `invoices` WHERE `order_id` = $order_id"));
					if(empty($invoice['invoice_number'])){
						$invoice_number = "Proforma";
					}else{
						$invoice_number = "TH-" . $invoice['invoice_number'];
					}
					$find_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id ORDER BY `type` DESC");
					$spec_total = 0;
					$item_total = 0;
					while($spec = $sql->fetch_array($find_specs)){
						$spec_total += number_format((float)($spec['quantity'] * $spec['cost_exc_vat']), 2, ".", "");
						$spec_id = $spec['spec_id'];
						$find_items = $sql->query("SELECT * FROM `order_items` WHERE `order_spec_id` = $spec_id ORDER BY `upc` ASC");
						while($item = $sql->fetch_array($find_items)){
							$item_total += number_format((float)($item['quantity'] * $item['cost_exc_vat']), 2, ".", "");
						}
						if($spec['type'] == "parts"){
							$parts_total++;
						}elseif($spec['type'] == "pc"){
							$pc_total++;
						}
					}
					$total = number_format((float)(($order['other_exc_vat'] + $order['shipping_exc_vat'] + $order['upgrades_exc_vat'] + $item_total + $spec_total) * page::multiplyVAT($order['vat'])), 2, ".", "");
					$payments = $sql->fetch_array($sql->query("SELECT SUM(`amount`) AS `amount_paid` FROM `payment_entries` WHERE `order_id` = $order_id"));
					$amount_paid = $payments['amount_paid'];
					if($total == $amount_paid){
						$payment_info = "Amount paid fully!";
						$payment_colour = "green";
						$payment_icon = "ok";
					}elseif($total > $amount_paid){
						$payment_info = "Amount due: " . number_format((float)$total - $amount_paid, 2, ".", "");
						$payment_colour = "red";
						$payment_icon = "exclamation-sign";
					}else{
						$payment_info = "Amount overpaid: " . number_format((float)abs($total - $amount_paid), 2, ".", "");
						$payment_colour = "#FFCC00";
						$payment_icon = "warning-sign";
					}
					?>
					<tr>
						<?php
						if($order['order_status'] == "completed"){
							if($page->hasLevel(1)){
								?>
								<td>
									<input type="checkbox" value="<?=$order['order_id']?>" name="order_selected[<?=$i?>]">
									<span class="lbl"></span>
								</td>
								<?php
							}else{
								?>
								<td></td>
								<?php
							}
						}else{
							?>
							<td>
								<input type="checkbox" value="<?=$order['order_id']?>" name="order_selected[<?=$i?>]">
								<span class="lbl"></span>
							</td>
							<?php
						}
						?>
						<td width="80px"><b><?=$order['order_id']?></b></td>
						<td><?=$invoice_number?></td>
						<td><?=$order['first_name']?> <?=$order['last_name']?></td>
						<td><?=$order['order_ref']?></td>
						<td><?=$order['invoice_date']?></td>
						<td><?=date("d/m/Y", $order['shipment_date'])?></td>
						<td><?=ucfirst($order['priority'])?></td>
						<td><strong><?=$page->order_status[$order['order_status']]?></strong></td>
						<td><a href="<?=url?>/invoice/<?=$order_id?>" target="_blank"><?=$total?></a></td>
						<td>
							<a href="<?=url?>/orders/edit/<?=$order['order_id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
						</td>
					</tr>
					<tr>
						<td colspan="4"><span class="glyphicon glyphicon-phone"></span> M - <?=$order['mobile_number']?> / H - <?=$order['home_number']?> / W - <?=$order['work_number']?></td>
						<td colspan="4"></td>
						<td style="color: <?=$payment_colour?>; font-weight: bold;" colspan="3"><span class="glyphicon glyphicon-<?=$payment_icon?>"></span> <?=$payment_info?></td>
					</tr>
					<?php
					$order_total += $total;
					$i++;
				}
				?>
				<tr>
					<td colspan="11"></td>
				</tr>
				<tr>
					<td colspan="7"></td>
					<td>Parts: <?php echo $parts_total; ?></td>
					<td>PCs: <?php echo $pc_total; ?></td>
					<td><strong>Total</strong></td>
					<td><strong>£<?php echo number_format((float)abs($order_total), 2, ".", ""); ?></strong></td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="form-group" style="margin-right: 20px;">
							<label class="sr-only no-space" for="status">Update Selected Receipts</label>
							<select class="form-control" name="status" id="status" style="width: 300px;">
								<?php
								foreach($page->order_status as $key => $name){
									$selected = ($_POST['status'] == $key) ? " selected" : "";
									?>
									<option value="<?=$key?>"<?=$selected?>><?=$name?></option>
									<?php
								}
								?>
							</select>
						</div>
						<button type="submit" class="btn btn-default" name="update" style="margin-bottom: 11px;">Update</button>
					</td>
					<td colspan="1">
						<div class="form-group">
							<select class="form-control" name="category" id="category" style="width: 300px;" onChange="getTemplateList('order')">
								<option disabled selected>Select Category</option>
								<?php
								$categories = $sql->query("SELECT * FROM `email_categories` ORDER BY `description` ASC");
								while($category = $sql->fetch_array($categories)){
									?>
									<option value="<?=$category['id']?>"><?=$category['description']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<span id="templates"></span>
						<button type="button" class="btn btn-default" style="margin-bottom: 11px;" onClick="sendEmail()">Send Email</button>
					</td>
					<td colspan="5">
						<div class="form-group">
							<select class="form-control" id="sms_template" style="width: 300px;">
								<option disabled selected>Select Template</option>
								<?php
								$templates = $sql->query("SELECT * FROM `sms_templates` WHERE `type` = 'order' ORDER BY `description` ASC");
								while($template = $sql->fetch_array($templates)){
									?>
									<option value="<?php echo $template['id']; ?>"><?php echo $template['description']; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<button type="button" class="btn btn-default" style="margin-bottom: 11px;" onClick="sendSMS()">Send SMS</button>
					</td>
					<td colspan="2">
						<button type="button" class="btn btn-default" style="margin-bottom: 11px;" onClick="duplicateEntry('order')">Duplicate Selected</button>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}else{
	page::alert("No search query was received!", "danger");
}