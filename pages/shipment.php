<?php
$page = new page("Manage Shipment", 3);
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}

if($action == "methods"){
	?>
	<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>#</th>
				<th>Delivery Key</th>
				<th>Delivery Name</th>
				<th>Visible in Purchases</th>
				<th>Visible in Orders</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			<form method="POST">
				<?php
				$i = 1;
				if(isset($_POST['update'])){
					$errors = array();
					$methods = array();
					if(isset($_POST['delivery_key']) AND isset($_POST['delivery_name'])){
						$delivery_keys = $_POST['delivery_key'];
						$delivery_names = $_POST['delivery_name'];
						$purchases = isset($_POST['purchases']) ? $_POST['purchases'] : array();
						$orders = isset($_POST['orders']) ? $_POST['orders'] : array();
						$delete = isset($_POST['delete']) ? $_POST['delete'] : array();
						foreach($delivery_keys as $key => $delivery_key){
							$key_for_check = $key + 1;
							if(empty($delivery_names[$key])){
								$errors[] = "You left the methods name empty!";
								$delivery_name = false;
							}else{
								$delivery_name = $delivery_names[$key];
							}
							if(isset($delete[$key])){
								$delete_method = true;
							}else{
								$delete_method = false;
							}
							if(isset($purchases[$key])){
								$purchase = true;
							}else{
								$purchase = false;
							}
							if(isset($orders[$key])){
								$order = true;
							}else{
								$order = false;
							}
							if($delivery_name != false){
								$methods[] = array("delivery_key" => $delivery_key, "delivery_name" => $delivery_name, "purchases" => $purchase, "orders" => $order, "delete" => $delete_method);
							}
						}
					}

					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						foreach($methods as $method){
							$delivery_key = $sql->smart($method['delivery_key']);
							$delivery_name = $sql->smart($method['delivery_name']);
							if($method['purchases']){
								$purchases = 1;
							}else{
								$purchases = 0;
							}
							if($method['orders']){
								$orders = 1;
							}else{
								$orders = 0;
							}
							if($method['delete']){
								$sql->query("DELETE FROM `delivery_methods` WHERE `delivery_key` = $delivery_key");
							}else{
								$sql->query("UPDATE `delivery_methods` SET `delivery_name` = $delivery_name, `purchases` = $purchases, `orders` = $orders WHERE `delivery_key` = $delivery_key");
							}
						}
						page::alert("Delivery methods successfully updated!", "success");
					}
				}
				if(isset($_POST['add'])){
					$errors = array();
					if(empty($_POST['delivery_key'])){
						$errors[] = "You forgot to enter the delivery key!";
					}
					if(empty($_POST['delivery_name'])){
						$errors[] = "You forgot to enter the delivery name!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$delivery_key = $sql->smart($_POST['delivery_key']);
						$delivery_name = $sql->smart($_POST['delivery_name']);
						if(isset($_POST['purchases'])){
							$purchases = 1;
						}else{
							$purchases = 0;
						}
						if(isset($_POST['orders'])){
							$orders = 1;
						}else{
							$orders = 0;
						}
						$sql->query("INSERT INTO `delivery_methods` (`delivery_key`, `delivery_name`, `purchases`, `orders`) VALUES ($delivery_key, $delivery_name, $purchases, $orders)") or die(mysql_error());
						page::alert("Delivery method successfully added!", "success");
					}
				}
				$delivery_methods = $sql->query("SELECT * FROM `delivery_methods` ORDER BY `delivery_key` ASC");
				while($delivery_method = $sql->fetch_array($delivery_methods)){
					$purchases = ($delivery_method['purchases'] == 1) ? " checked" : "";
					$orders = ($delivery_method['orders'] == 1) ? " checked" : "";
					?>
					<tr>
						<td><?=$i?></td>
						<td><input type="text" name="delivery_key[<?=$i?>]" class="form-control" placeholder="Delivery Key" value="<?=$delivery_method['delivery_key']?>" readonly></td>
						<td><input type="text" name="delivery_name[<?=$i?>]" class="form-control" placeholder="Delivery Name" value="<?=$delivery_method['delivery_name']?>"></td>
						<td><input type="checkbox" name="purchases[<?=$i?>]"<?=$purchases?>><span class="lbl"></span></td>
						<td><input type="checkbox" name="orders[<?=$i?>]"<?=$orders?>><span class="lbl"></span></td>
						<td><input type="checkbox" name="delete[<?=$i?>]"><span class="lbl"></span></td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td colspan="5"></td>
					<td><button type="submit" class="btn btn-default" name="update">Update</button></td>
				</tr>
			</form>
			<tr class="active">
				<td colspan="6">Add New Delivery Method</td>
			</tr>
			<form method="POST">
				<tr>
					<td class="text-center"><span class="glyphicon glyphicon-plus"></span></td>
					<td><input type="text" name="delivery_key" class="form-control" placeholder="Delivery Key"></td>
					<td><input type="text" name="delivery_name" class="form-control" placeholder="Delivery Name"></td>
					<td><input type="checkbox" name="purchases"><span class="lbl"></span></td>
					<td><input type="checkbox" name="orders"><span class="lbl"></span></td>
					<td><button type="submit" class="btn btn-default" name="add">Add</button></td>
				</tr>
			</form>
		</tbody>
	</table>
	<?php
}else{
	$today = date("d/m/Y");
	$last_week = date("d/m/Y", strtotime("- 1 week", time()));
	$next_week = date("d/m/Y", strtotime("+ 1 week", time()));
	$from_d = (isset($_POST['from'])) ? $_POST['from'] : $last_week;
	$to_d = (isset($_POST['to'])) ? $_POST['to'] : $next_week;
	if(isset($_POST['view'])){
		if(!empty($_POST['from']) AND !empty($_POST['to'])){
			$to = strtotime(str_replace("/", "-", $_POST['to']));
			$from = strtotime(str_replace("/", "-", $_POST['from']));
		}else{
			$to = strtotime(str_replace("/", "-", $next_week));
			$from = strtotime(str_replace("/", "-", $last_week));
		}
	}else{
		if(isset($pageInfo[2]) AND $pageInfo[2] == "today"){
			$to = strtotime(str_replace("/", "-", $today));
			$from = strtotime(str_replace("/", "-", $today));
			$from_d = $today;
			$to_d = $today;
		}else{
			$to = strtotime(str_replace("/", "-", $next_week));
			$from = strtotime(str_replace("/", "-", $last_week));
		}
	}

	?>
	<form class="form-inline bottom-space pull-left" role="form" method="POST">
		<div class="form-group">
			<label class="sr-only" for="from">Date From</label>
			<input type="text" name="from" class="form-control datepicker no-space" placeholder="Date From..." value="<?=$from_d?>">
		</div>
		<div class="form-group">
			<label class="sr-only" for="to">Date To</label>
			<input type="text" name="to" class="form-control datepicker no-space" placeholder="Date To..." value="<?=$to_d?>">
		</div>
		<button type="submit" class="btn btn-default" name="view" style="margin-top: 1px;">View</button>
	</form>
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td colspan="11">
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
						<?php
						$order_total = 0;
						$pc_total = 0;
						$parts_total = 0;
						for($i = $from; $i <= $to; $i = $i + 86400){
							if($i == 1){
								$collapse = "collapse in";
							}else{
								$collapse = "collapse";
							}
							$total_orders = $sql->num_rows($sql->query("SELECT `order_id` FROM `orders` WHERE `shipment_date` = $i ORDER BY `order_id` ASC"));
							if($total_orders > 0){
								?>
								<div class="panel panel-default">
									<div class="panel-heading" role="tab" id="heading_<?=$i?>">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?=$i?>" aria-controls="collapse_<?=$i?>">
												<?php echo date("d/m/Y", $i); ?>
											</a>
										</h4>
									</div>
									<div id="collapse_<?=$i?>" class="panel-collapse <?=$collapse?>" role="tabpanel" aria-labelledby="heading_<?=$i?>">
										<div class="panel-body">
											<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Order ID</th>
														<th>Invoice Number</th>
														<th>Name</th>
														<th>Reference</th>
														<th>Invoice date</th>
														<th>Shipping date</th>
														<th>Priority</th>
														<th>Status</th>
														<th>Delivery Method</th>
														<th>Total price</th>
														<th>Actions</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$orders = $sql->query("SELECT * FROM `orders` WHERE `shipment_date` = $i ORDER BY `order_id` ASC");
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
														$total = number_format((float)(($order['other_exc_vat'] + $order['shipping_exc_vat'] + $item_total + $spec_total) * 1.2), 2, ".", "");
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
														$delivery_key = $sql->smart($order['delivery_method']);
														$delivery_method = $sql->fetch_array($sql->query("SELECT `delivery_name` FROM `delivery_methods` WHERE `delivery_key` = $delivery_key"));
														?>
														<tr>
															<td width="80px"><b><?=$order['order_id']?></b></td>
															<td><?=$invoice_number?></td>
															<td><?=$order['first_name']?> <?=$order['last_name']?></td>
															<td><?=$order['order_ref']?></td>
															<td><?=$order['invoice_date']?></td>
															<td><b><?=date("d/m/Y", $order['shipment_date'])?></b></td>
															<td><?=ucfirst($order['priority'])?></td>
															<td><strong><?=$page->order_status[$order['order_status']]?></strong></td>
															<td><?=$delivery_method['delivery_name']?></td>
															<td><a href="<?=url?>/invoice/<?=$order_id?>" target="_blank"><?=$total?></a></td>
															<td>
																<a href="<?=url?>/orders/edit/<?=$order['order_id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
															</td>
														</tr>
														<tr>
															<td colspan="4"><?=empty($order['shipping_line1']) ? "" : $order['shipping_line1'] . ", "?><?=empty($order['shipping_line2']) ? "" : $order['shipping_line2'] . ", "?><?=empty($order['shipping_line3']) ? "" : $order['shipping_line3'] . ", "?><?=empty($order['shipping_line4']) ? "" : $order['shipping_line4'] . ", "?><?=$order['shipping_postcode']?></td>
															<td colspan="4"><span class="glyphicon glyphicon-phone"></span> M - <?=$order['mobile_number']?> / H - <?=$order['home_number']?> / W - <?=$order['work_number']?></td>
															<td style="color: <?=$payment_colour?>; font-weight: bold;" colspan="3"><span class="glyphicon glyphicon-<?=$payment_icon?>"></span> <?=$payment_info?></td>
														</tr>
														<?php
														$order_total += $total;
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="10" class="text-right">Parts: <?=$parts_total?>; PCs: <?=$pc_total?></td>
				<td colspan="1" width="200px"><strong>Total: £<?=$order_total?></strong></td>
			</tr>
		</tbody>
	</table>
	<?php
}
?>