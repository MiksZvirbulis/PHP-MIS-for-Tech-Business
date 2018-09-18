<?php
$page = new page("Purchases", 3);
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}
if($action == "add" AND page::moduleAccess(2)){
	if(isset($_POST['add_purchase'])){
		$errors = array();
		if(empty($_POST['supplier_id'])){
			$errors[] = "You forgot to select the supplier!";
		}
		if(empty($_POST['invoice_date'])){
			$errors[] = "You forgot to select the invoice date!";
		}
		if(empty($_POST['batch_number'])){
			$errors[] = "You forgot to enter the batch number!";
		}
		if(empty($_POST['shipping_charges'])){
			$errors[] = "You forgot to enter the shipping charges!";
		}
		if(empty($_POST['other_charges'])){
			$errors[] = "You forgot to enter the other charges!";
		}
		if(empty($_POST['amount_paid'])){
			$errors[] = "You forgot to enter the paid amount!";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$supplier_id = $sql->smart($_POST['supplier_id']);
			$invoice_number = $sql->smart($_POST['invoice_number']);
			$invoice_date = $sql->smart(strtotime(str_replace("/", "-", $_POST['invoice_date'])));
			$delivery_date = $sql->smart($_POST['delivery_date']);
			$batch_number = $sql->smart($_POST['batch_number']);
			if(isset($_POST['vat'])){
				$vat = 1;
			}else{
				$vat = 0;
			}
			$shipping_charges = $sql->smart($_POST['shipping_charges']);
			$other_charges = $sql->smart($_POST['other_charges']);
			$amount_paid = $sql->smart($_POST['amount_paid']);
			$note = $sql->smart($_POST['note']);
			if(isset($_POST['short_shipment'])){
				$short_shipment = 1;
			}else{
				$short_shipment = 0;
			}
			if(isset($_POST['flag'])){
				$flag = 1;
			}else{
				$flag = 0;
			}
			if(isset($_POST['highlight'])){
				$highlight = 1;
			}else{
				$highlight = 0;
			}
			if(isset($_POST['query'])){
				$query = 1;
			}else{
				$query = 0;
			}
			if(isset($_POST['invoice_matched'])){
				$invoice_matched = 1;
			}else{
				$invoice_matched = 0;
			}
			if(isset($_POST['delivery_method'])){
				$delivery_method = $sql->smart($_POST['delivery_method']);
			}else{
				$delivery_method = $sql->smart("");
			}
			$created_by_id = $_COOKIE['user_id'];
			$date_created = $sql->smart(time());
			$sql->query("INSERT INTO `purchases` 
				(`batch_number`, `supplier_id`, `invoice_number`, `invoice_date`, `delivery_date`, `vat`, `shipping_charges`, `other_charges`, `amount_paid`, `note`, `short_shipment`, `flag`, `highlight`, `query`, `invoice_matched`, `delivery_method`, `created_by_id`, `date_created`)
				VALUES
				($batch_number, $supplier_id, $invoice_number, $invoice_date, $delivery_date, $vat, $shipping_charges, $other_charges, $amount_paid, $note, $short_shipment, $flag, $highlight, $query, $invoice_matched, $delivery_method, $created_by_id, $date_created)");
			$purchases = $sql->fetch_array($sql->query("SELECT `purchase_id` FROM `purchases` ORDER BY `purchase_id` DESC LIMIT 1"));
			$purchase_id = $sql->smart($purchases['purchase_id']);
			if(isset($_POST['upc']) AND isset($_POST['cost_ex_vat']) AND isset($_POST['quantity'])){
				$upcs = $_POST['upc'];
				$cost_ex_vat = $_POST['cost_ex_vat'];
				$quantity = $_POST['quantity'];
				foreach($upcs as $key => $upc){
					if(!empty($upc)){
						$upc = $sql->smart($upc);
						$cost = $sql->smart($cost_ex_vat[$key]);
						$item_quantity = $sql->smart($quantity[$key]);
						$sql->query("INSERT INTO `purchase_items` (`upc`, `price_exc_vat`, `quantity`, `purchase_id`) VALUES ($upc, $cost, $item_quantity, $purchase_id)");
					}
				}
			}
			header("Location: " . url . "/purchases/edit/" . str_replace("'", "", $purchase_id));
		}
	}
	?>
	<form method="POST" class="form-horizontal">
		<fieldset>
			<legend>Purchase Information</legend>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="supplier_id" class="col-xs-4 control-label">Supplier*</label>
					<div class="col-xs-8">
						<select class="form-control" name="supplier_id">
							<option disabled selected>Select supplier</option>
							<?php
							$suppliers = $sql->query("SELECT `id`, `name` FROM `suppliers` WHERE `active` = 1 ORDER BY `name` ASC");
							while($supplier = $sql->fetch_array($suppliers)){
								?>
								<option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="invoice_date" class="col-xs-4 control-label">Invoice date*</label>
					<div class="col-xs-8">
						<input type="text" class="form-control datepicker" name="invoice_date" placeholder="Invoice date" value="<?=date("d/m/Y")?>">
					</div>
				</div>
				<div class="form-group">
					<label for="delivery_date" class="col-xs-4 control-label">Delivery date</label>
					<div class="col-xs-8">
						<input type="text" class="form-control datepicker" name="delivery_date" placeholder="Delivery date">
					</div>
				</div>
				<div class="form-group">
					<label for="note" class="col-xs-4 control-label">Note</label>
					<div class="col-xs-8">
						<textarea class="form-control" name="note" placeholder="Note"></textarea>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="batch_number" class="col-xs-4 control-label">Batch number*</label>
					<div class="col-xs-8">
						<?php
						$latest_batch = $sql->fetch_array($sql->query("SELECT `batch_number` FROM `purchases` ORDER BY `batch_number` DESC LIMIT 1"));
						?>
						<div class="input-group">
							<span class="input-group-addon">TP-</span>
							<input type="text" class="form-control" name="batch_number" placeholder="Batch number" value="<?=$latest_batch['batch_number'] + 1?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="invoice_number" class="col-xs-4 control-label">Invoice number</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="invoice_number" placeholder="Invoice number">
					</div>
				</div>
				<div class="form-group">
					<label for="vat" class="col-xs-4 control-label">VAT purchase?</label>
					<div class="col-xs-8">
						<label>
							<input type="checkbox" name="vat" onchange="update_total()" id="vat" checked>
							<span class="lbl"></span>
						</label>
					</div>
				</div>
			</div>
			<legend>Shipping and other charges</legend>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="shipping_charges" class="col-xs-4 control-label">Shipping charges*</label>
					<div class="col-xs-8">
						<input type="text" id="shipping_charges" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="shipping_charges" placeholder="Shipping charges" value="0.00">
					</div>
				</div>
				<div class="form-group">
					<label for="other_charges" class="col-xs-4 control-label">Other charges*</label>
					<div class="col-xs-8">
						<input type="text" id="other_charges" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="other_charges" placeholder="Other charges" value="0.00">
					</div>
				</div>
			</div>
			<legend>Purchase Summary</legend>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="short_shipment" class="col-xs-4 control-label">Short Shipment</label>
					<div class="col-xs-8">
						<label>
							<input type="checkbox" name="short_shipment">
							<span class="lbl"></span>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="flag" class="col-xs-4 control-label">Flag</label>
					<div class="col-xs-8">
						<label>
							<input type="checkbox" name="flag">
							<span class="lbl"></span>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="highlight" class="col-xs-4 control-label">Highlight</label>
					<div class="col-xs-8">
						<label>
							<input type="checkbox" name="highlight">
							<span class="lbl"></span>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="query" class="col-xs-4 control-label">Query</label>
					<div class="col-xs-8">
						<label>
							<input type="checkbox" name="query">
							<span class="lbl"></span>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="invoice_matched" class="col-xs-4 control-label">Invoice matched</label>
					<div class="col-xs-8">
						<label>
							<input type="checkbox" name="invoice_matched">
							<span class="lbl"></span>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="delivery_method" class="col-xs-4 control-label">Delivery method</label>
					<div class="col-xs-8">
						<select class="form-control" name="delivery_method">
							<option disabled selected>Select delivery method</option>
							<?php
							$delivery_methods = $sql->query("SELECT * FROM `delivery_methods` WHERE `purchases` = 1 ORDER BY `delivery_name` ASC");
							while($delivery_method = $sql->fetch_array($delivery_methods)){
								?>
								<option value="<?=$delivery_method['delivery_key']?>"><?=$delivery_method['delivery_name']?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="amount_paid" class="col-xs-4 control-label">Amount paid*</label>
					<div class="col-xs-8 form-inline">
						<div class="form-group col-xs-6">
							<input type="text" id="amount_paid" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="amount_paid" placeholder="Amount paid" value="0.00">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-4 control-label">Total EXC VAT</label>
					<div class="col-xs-8">
						<input type="text" id="total_exc_vat" class="form-control" value="0.00" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-4 control-label">Total INC VAT</label>
					<div class="col-xs-8">
						<input type="text" id="total_inc_vat" class="form-control" value="0.00" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-4 control-label">Total VAT</label>
					<div class="col-xs-8">
						<input type="text" id="total_vat" class="form-control" value="0.00" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-4 control-label">Payment due</label>
					<div class="col-xs-8">
						<input type="text" id="payment_due" class="form-control" value="0.00" readonly>
					</div>
				</div>
			</div>
		</fieldset>
		<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Category</th>
					<th>Sub category</th>
					<th>Item</th>
					<th>UPC</th>
					<th>Cost exc. VAT</th>
					<th>Quantity</th>
					<th>Total exc. VAT</th>
				</tr>
			</thead>
			<tbody>
				<?php
				for($i = 1; $i <= 10; $i++){
					?>
					<tr>
						<td id="item_count"><?=$i?></td>
						<td>
							<select class="form-control" onchange="get_subcat(<?=$i?>)" id="select_cat_<?=$i?>">
								<option selected disabled>Choose category</option>
								<?php
								$existing_cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
								while($existing_cat = $sql->fetch_array($existing_cats)){
									?>
									<option value="<?=$existing_cat['cat_id']?>"><?=$existing_cat['description']?></option>
									<?php
								}
								?>
							</select>
						</td>
						<td>
							<span id="subcat_<?=$i?>" onchange="get_catitems_list(<?=$i?>)">
								<select class="form-control">
									<option disabled selected>Select category to load sub categories</option>
								</select>
							</span>
						</td>
						<td>
							<span id="catitems_list_<?=$i?>">
								<select class="form-control">
									<option disabled selected>Select subcategory to load items</option>
								</select>
							</span>
						</td>
						<td style="width: 100px;"><input type="text" class="form-control input-sm" id="upc_<?=$i?>" name="upc[]" placeholder="UPC"></td>
						<td><input class="form-control" type="text" id="item_cost_<?=$i?>" onclick="money(this)" onblur="money(this), item_total(<?=$i?>), update_total()" name="cost_ex_vat[]" type="text" placeholder="Cost exc. VAT" value="0.00"></td>
						<td><input class="form-control" type="text" id="item_quantity_<?=$i?>" onblur="item_total(<?=$i?>), update_total()" name="quantity[]" placeholder="Quantity" value="0"></td>
						<td><input class="form-control item_total" type="text" id="item_total_<?=$i?>" value="0.00" readonly></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<div id="item_search">
			<input type="text" class="form-control" id="search_item" onkeyup="list_item_upc()" placeholder="Search for Item...">
			<div id="loader"></div>
			<div id="items"></div>
		</div>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm" name="add_purchase">Add purchase</button>
		</div>
	</form>
	<?php
}elseif($action == "edit" AND page::moduleAccess(2)){
	if(isset($pageInfo[3])){
		$purchase_id = $pageInfo[3];
		$update_total_existance = $sql->num_rows($sql->query("SELECT `batch_number` FROM `purchases` WHERE `purchase_id` = $purchase_id"));
		if($update_total_existance == 0){
			page::alert("A purchase with this ID was not found!", "danger");
		}else{
			$purchase = $sql->fetch_array($sql->query("SELECT * FROM `purchases` WHERE `purchase_id` = $purchase_id"));
			if(isset($_POST['edit_purchase'])){
				if($purchase['invoice_matched'] == 1){
					if($page->hasLevel(1)){
						$proceed = true;
					}
				}else{
					$proceed = true;
				}
				if(isset($proceed) AND $proceed){
					$errors = array();
					if(empty($_POST['supplier_id'])){
						$errors[] = "You forgot to select the supplier!";
					}
					if(empty($_POST['invoice_date'])){
						$errors[] = "You forgot to select the invoice date!";
					}
					if(empty($_POST['batch_number'])){
						$errors[] = "You forgot to enter the batch number!";
					}
					if(empty($_POST['shipping_charges'])){
						$errors[] = "You forgot to enter the shipping charges!";
					}
					if(empty($_POST['other_charges'])){
						$errors[] = "You forgot to enter the other charges!";
					}
					if(empty($_POST['amount_paid'])){
						$errors[] = "You forgot to enter the paid amount!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$supplier_id = $sql->smart($_POST['supplier_id']);
						$invoice_number = $sql->smart($_POST['invoice_number']);
						$invoice_date = $sql->smart(strtotime(str_replace("/", "-", $_POST['invoice_date'])));
						$delivery_date = $sql->smart($_POST['delivery_date']);
						$batch_number = $sql->smart($_POST['batch_number']);
						if(isset($_POST['vat'])){
							$vat = 1;
						}else{
							$vat = 0;
						}
						$shipping_charges = $sql->smart($_POST['shipping_charges']);
						$other_charges = $sql->smart($_POST['other_charges']);
						$amount_paid = $sql->smart($_POST['amount_paid']);
						$note = $sql->smart($_POST['note']);
						if(isset($_POST['short_shipment'])){
							$short_shipment = 1;
						}else{
							$short_shipment = 0;
						}
						if(isset($_POST['flag'])){
							$flag = 1;
						}else{
							$flag = 0;
						}
						if(isset($_POST['highlight'])){
							$highlight = 1;
						}else{
							$highlight = 0;
						}
						if(isset($_POST['query'])){
							$query = 1;
						}else{
							$query = 0;
						}
						if(isset($_POST['invoice_matched'])){
							$invoice_matched = 1;
						}else{
							$invoice_matched = 0;
						}
						if(isset($_POST['delivery_method'])){
							$delivery_method = $sql->smart($_POST['delivery_method']);
						}else{
							$delivery_method = $sql->smart("");
						}
						$sql->query("UPDATE `purchases` SET
							`batch_number` = $batch_number,
							`supplier_id` = $supplier_id,
							`invoice_number` = $invoice_number,
							`invoice_date` = $invoice_date,
							`delivery_date` = $delivery_date,
							`vat` = $vat,
							`shipping_charges` = $shipping_charges,
							`other_charges` = $other_charges,
							`amount_paid` = $amount_paid,
							`note` = $note,
							`short_shipment` = $short_shipment,
							`flag` = $flag,
							`highlight` = $highlight,
							`query` = $query,
							`invoice_matched` = $invoice_matched,
							`delivery_method` = $delivery_method
							WHERE `purchase_id` = $purchase_id
							");
						$purchase = $sql->fetch_array($sql->query("SELECT * FROM `purchases` WHERE `purchase_id` = $purchase_id"));
						if(isset($_POST['item_id']) AND isset($_POST['cost_ex_vat']) AND isset($_POST['quantity'])){
							$item_ids = $_POST['item_id'];
							$cost_ex_vat = $_POST['cost_ex_vat'];
							$quantity = $_POST['quantity'];
							$delete = (isset($_POST['delete'])) ? $_POST['delete'] : array();
							foreach($item_ids as $key => $item_id){
								$key_for_check = $key + 1;
								$item_id = $sql->smart($item_id);
								$cost = $sql->smart($cost_ex_vat[$key]);
								$item_quantity = $sql->smart($quantity[$key]);
								if(isset($delete[$key_for_check])){
									$sql->query("DELETE FROM `purchase_items` WHERE `id` = $item_id");
									$purchase_items = $sql->query("SELECT * FROM `purchase_items` WHERE `purchase_id` = $purchase_id");
								}else{
									$sql->query("UPDATE `purchase_items` SET `price_exc_vat` = $cost, `quantity` = $item_quantity WHERE `id` = $item_id");
									$purchase_items = $sql->query("SELECT * FROM `purchase_items` WHERE `purchase_id` = $purchase_id");
								}
							}
						}
						page::alert("Purchase edited successfully!", "success");
					}
				}
			}
			if(isset($_POST['add_items'])){
				if($purchase['invoice_matched'] == 1){
					if($page->hasLevel(1)){
						$proceed = true;
					}
				}else{
					$proceed = true;
				}
				if(isset($proceed) AND $proceed){
					$errors = array();
					if(!isset($_POST['upc'])){
						$errors[] = "No items have been selected!";
					}
					if(!isset($_POST['cost_ex_vat'])){
						$errors[] = "No prices have been defined!";
					}
					if(!isset($_POST['quantity'])){
						$errors[] = "No quantities have been set!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$upcs = $_POST['upc'];
						$cost_ex_vat = $_POST['cost_ex_vat'];
						$quantity = $_POST['quantity'];					
						foreach($upcs as $key => $upc){
							if(!empty($upc)){
								$upc = $sql->smart($upc);
								$cost = $sql->smart($cost_ex_vat[$key]);
								$item_quantity = $sql->smart($quantity[$key]);
								$sql->query("INSERT INTO `purchase_items` (`upc`, `price_exc_vat`, `quantity`, `purchase_id`) VALUES ($upc, $cost, $item_quantity, $purchase_id)");
							}
						}
						$purchase_items = $sql->query("SELECT * FROM `purchase_items` WHERE `purchase_id` = $purchase_id");
						page::alert("Items added successfully!", "success");
					}
				}
			}
			$purchase = $sql->fetch_array($sql->query("SELECT * FROM `purchases` WHERE `purchase_id` = $purchase_id"));
			?>
			<form method="POST" class="form-horizontal">
				<fieldset>
					<legend>Purchase Information</legend>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="supplier_id" class="col-xs-4 control-label">Supplier*</label>
							<div class="col-xs-8">
								<select class="form-control" name="supplier_id">
									<option disabled selected>Select supplier</option>
									<?php
									$suppliers = $sql->query("SELECT `id`, `name` FROM `suppliers` WHERE `active` = 1 ORDER BY `name` ASC");
									while($supplier = $sql->fetch_array($suppliers)){
										$selected = ($supplier['id'] == $purchase['supplier_id']) ? " selected" : "";
										?>
										<option value="<?=$supplier['id']?>"<?=$selected?>><?=$supplier['name']?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="invoice_date" class="col-xs-4 control-label">Invoice date*</label>
							<div class="col-xs-8">
								<input type="text" class="form-control datepicker" name="invoice_date" placeholder="Invoice date" value="<?=date('d/m/Y', $purchase['invoice_date'])?>">
							</div>
						</div>
						<div class="form-group">
							<label for="delivery_date" class="col-xs-4 control-label">Delivery date</label>
							<div class="col-xs-8">
								<input type="text" class="form-control datepicker" name="delivery_date" placeholder="Delivery date" value="<?=$purchase['delivery_date']?>">
							</div>
						</div>
						<div class="form-group">
							<label for="note" class="col-xs-4 control-label">Note</label>
							<div class="col-xs-8">
								<textarea class="form-control" name="note" placeholder="Note"><?=$purchase['note']?></textarea>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="batch_number" class="col-xs-4 control-label">Batch number*</label>
							<div class="col-xs-8">
								<div class="input-group">
									<span class="input-group-addon">TP-</span>
									<input type="text" class="form-control" name="batch_number" placeholder="Batch number" value="<?=$purchase['batch_number']?>">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="invoice_number" class="col-xs-4 control-label">Invoice number</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="invoice_number" placeholder="Invoice number" value="<?=$purchase['invoice_number']?>">
							</div>
						</div>
						<?php
						$vat_checked = ($purchase['vat'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="vat" class="col-xs-4 control-label">VAT purchase?</label>
							<div class="col-xs-8">
								<label>
									<input type="checkbox" name="vat" onchange="update_total()" id="vat"<?=$vat_checked?>>
									<span class="lbl"></span>
								</label>
							</div>
						</div>
					</div>
					<legend>Shipping and other charges</legend>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="shipping_charges" class="col-xs-4 control-label">Shipping charges*</label>
							<div class="col-xs-8">
								<input type="text" id="shipping_charges" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="shipping_charges" placeholder="Shipping charges" value="<?=$purchase['shipping_charges']?>">
							</div>
						</div>
						<div class="form-group">
							<label for="other_charges" class="col-xs-4 control-label">Other charges*</label>
							<div class="col-xs-8">
								<input type="text" id="other_charges" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="other_charges" placeholder="Other charges" value="<?=$purchase['other_charges']?>">
							</div>
						</div>
					</div>
					<legend>Purchase Summary</legend>
					<div class="col-xs-6">
						<?php
						$ss_checked = ($purchase['short_shipment'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="short_shipment" class="col-xs-4 control-label">Short Shipment</label>
							<div class="col-xs-8">
								<label>
									<input type="checkbox" name="short_shipment"<?=$ss_checked?>>
									<span class="lbl"></span>
								</label>
							</div>
						</div>
						<?php
						$flag_checked = ($purchase['flag'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="flag" class="col-xs-4 control-label">Flag</label>
							<div class="col-xs-8">
								<label>
									<input type="checkbox" name="flag"<?=$flag_checked?>>
									<span class="lbl"></span>
								</label>
							</div>
						</div>
						<?php
						$highlight_checked = ($purchase['highlight'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="highlight" class="col-xs-4 control-label">Highlight</label>
							<div class="col-xs-8">
								<label>
									<input type="checkbox" name="highlight"<?=$highlight_checked?>>
									<span class="lbl"></span>
								</label>
							</div>
						</div>
						<?php
						$query_checked = ($purchase['query'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="query" class="col-xs-4 control-label">Query</label>
							<div class="col-xs-8">
								<label>
									<input type="checkbox" name="query"<?=$query_checked?>>
									<span class="lbl"></span>
								</label>
							</div>
						</div>
						<?php
						$im_checked = ($purchase['invoice_matched'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="invoice_matched" class="col-xs-4 control-label">Invoice matched</label>
							<div class="col-xs-8">
								<label>
									<input type="checkbox" name="invoice_matched"<?=$im_checked?>>
									<span class="lbl"></span>
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="delivery_method" class="col-xs-4 control-label">Delivery method</label>
							<div class="col-xs-8">
								<select class="form-control" name="delivery_method">
									<option disabled selected>Select delivery method</option>
									<?php
									$delivery_methods = $sql->query("SELECT * FROM `delivery_methods` WHERE `purchases` = 1 ORDER BY `delivery_name` ASC");
									while($delivery_method = $sql->fetch_array($delivery_methods)){
										$selected = ($delivery_method['delivery_key'] == $purchase['delivery_method']) ? " selected" : "";
										?>
										<option value="<?=$delivery_method['delivery_key']?>"<?=$selected?>><?=$delivery_method['delivery_name']?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="amount_paid" class="col-xs-4 control-label">Amount paid*</label>
							<div class="col-xs-8 form-inline">
								<div class="form-group col-xs-6">
									<input type="text" id="amount_paid" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="amount_paid" placeholder="Amount paid" value="<?=$purchase['amount_paid']?>">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">Total EXC VAT</label>
							<div class="col-xs-8">
								<input type="text" id="total_exc_vat" class="form-control" value="0.00" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">Total INC VAT</label>
							<div class="col-xs-8">
								<input type="text" id="total_inc_vat" class="form-control" value="0.00" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">Total VAT</label>
							<div class="col-xs-8">
								<input type="text" id="total_vat" class="form-control" value="0.00" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">Payment due</label>
							<div class="col-xs-8">
								<input type="text" id="payment_due" class="form-control" readonly>
							</div>
						</div>
					</div>
				</fieldset>
				<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
					<thead>
						<tr>
							<th>#</th>
							<th>UPC</th>
							<th>Item</th>
							<th>Cost exc. VAT</th>
							<th>Quantity</th>
							<th>Total exc. VAT</th>
							<th>Delete</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$purchase_items = $sql->query("SELECT * FROM `purchase_items` WHERE `purchase_id` = $purchase_id");
						$i = 1;
						while($purchase_item = $sql->fetch_array($purchase_items)){
							?>
							<tr id="item_count">
								<td><?=$i?><input type="hidden" name="item_id[]" value="<?=$purchase_item['id']?>"></td>
								<td><?=$purchase_item['upc']?></td>
								<td><?=page::getItemInfo($purchase_item['upc'], "description")?></td>
								<td><input class="form-control" type="text" id="item_cost_<?=$i?>" onclick="money(this)" onblur="money(this), item_total(<?=$i?>), update_total()" name="cost_ex_vat[]" type="text" placeholder="Cost exc. VAT" value="<?=$purchase_item['price_exc_vat']?>"></td>
								<td><input class="form-control" type="text" id="item_quantity_<?=$i?>" onblur="item_total(<?=$i?>), update_total()" name="quantity[]" placeholder="Quantity" value="<?=$purchase_item['quantity']?>"></td>
								<td><input class="form-control item_total" type="text" id="item_total_<?=$i?>" value="0.00" readonly></td>
								<td>
									<label>
										<input type="checkbox" name="delete[<?=$i?>]">
										<span class="lbl"></span>
									</label>
								</td>
							</tr>
							<?php
							$i++;
						}
						?>
					</tbody>
				</table>
				<?php
				if($purchase['invoice_matched'] == 1){
					if($page->hasLevel(1)){
						?>
						<div class="form-button">
							<button type="submit" class="btn btn-primary btn-sm" name="edit_purchase">Edit purchase</button>
						</div>
						<?php
					}
				}else{
					?>
					<div class="form-button">
						<button type="submit" class="btn btn-primary btn-sm" name="edit_purchase">Edit purchase</button>
					</div>
					<?php
				}
				?>
			</form>
			<form method="POST">
				<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Category</th>
							<th>Sub category</th>
							<th>Item</th>
							<th>UPC</th>
							<th>Cost exc. VAT</th>
							<th>Quantity</th>
							<th>Total exc. VAT</th>
						</tr>
					</thead>
					<tbody>
						<?php
						for($i = 1; $i <= 10; $i++){
							?>
							<tr>
								<td id="item_count"><?=$i?></td>
								<td>
									<select class="form-control" onchange="get_subcat(<?=$i?>)" id="select_cat_<?=$i?>">
										<option selected disabled>Choose category</option>
										<?php
										$existing_cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
										while($existing_cat = $sql->fetch_array($existing_cats)){
											?>
											<option value="<?=$existing_cat['cat_id']?>"><?=$existing_cat['description']?></option>
											<?php
										}
										?>
									</select>
								</td>
								<td>
									<span id="subcat_<?=$i?>" onchange="get_catitems_list(<?=$i?>)">
										<select class="form-control">
											<option disabled selected>Select category to load sub categories</option>
										</select>
									</span>
								</td>
								<td>
									<span id="catitems_list_<?=$i?>">
										<select class="form-control">
											<option disabled selected>Select sub category to load items</option>
										</select>
									</span>
								</td>
								<td style="width: 100px;"><input type="text" class="form-control input-sm" id="upc_<?=$i?>" name="upc[]" placeholder="UPC"></td>
								<td><input class="form-control" type="text" id="new_item_cost_<?=$i?>" onclick="money(this)" onblur="money(this), new_item_total(<?=$i?>), update_total()" name="cost_ex_vat[]" type="text" placeholder="Cost exc. VAT" value="0.00"></td>
								<td><input class="form-control" type="text" id="new_item_quantity_<?=$i?>" onblur="new_item_total(<?=$i?>), update_total()" name="quantity[]" placeholder="Quantity" value="0"></td>
								<td><input class="form-control new_item_total" type="text" id="new_item_total_<?=$i?>" value="0.00" readonly></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<div id="item_search">
					<input type="text" class="form-control" id="search_item" onkeyup="list_item_upc()" placeholder="Search for Item...">
					<div id="loader"></div>
					<div id="items"></div>
				</div>
				<?php
				if($purchase['invoice_matched'] == 1){
					if($page->hasLevel(1)){
						?>
						<div class="form-button">
							<button type="submit" class="btn btn-primary btn-sm" name="add_items">Add items</button>
						</div>
						<?php
					}
				}else{
					?>
					<div class="form-button">
						<button type="submit" class="btn btn-primary btn-sm" name="add_items">Add items</button>
					</div>
					<?php
				}
				?>
			</form>
			<?php
		}
	}else{
		page::alert("No purchase ID was specified!", "danger");
	}
}elseif($action == "search" AND isset($pageInfo[3]) AND !empty($pageInfo[3])){
	$upc = $pageInfo[3];
	$find_item = $sql->num_rows($sql->query("SELECT `id` FROM `purchase_items` WHERE `upc` = $upc"));
	if($find_item == 0){
		page::alert("No purchases of this item were found!", "danger");
	}else{
		?>
		<legend>Purchases</legend>
		<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Batch Number</th>
					<th>Supplier name</th>
					<th>Invoice number</th>
					<th>Invoice date</th>
					<th>Quantity</th>
					<th>Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$find_purchases = $sql->query("SELECT `purchase_id`, `id` FROM `purchase_items` WHERE `upc` = $upc ORDER BY `purchase_id` DESC");
				while($find_purchase = $sql->fetch_array($find_purchases)){
					$purchase_id = $find_purchase['purchase_id'];
					$purchase = $sql->fetch_array($sql->query("SELECT * FROM `purchases` WHERE `purchase_id` = $purchase_id"));
					$purchase_item_id = $find_purchase['id'];
					$quantity = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `purchase_items` WHERE `id` = $purchase_item_id"));
					$price = $sql->fetch_array($sql->query("SELECT `price_exc_vat` FROM `purchase_items` WHERE `id` = $purchase_item_id AND `upc` = $upc LIMIT 1"));
					?>
					<tr>
						<td width="80px"><a href="<?=url?>/purchases/edit/<?=$purchase_id?>">TP-<b><?=$purchase['batch_number']?></b></a></td>
						<td><?=page::getSupplierInfo($purchase['supplier_id'], "name")?></td>
						<td><?=$purchase['invoice_number']?></td>
						<td><?=date("d/m/Y", $purchase['invoice_date'])?></td>
						<td><?=$quantity['total']?></td>
						<td><?=$price['price_exc_vat']?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<legend>Credit Notes</legend>
		<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Credit Note ID</th>
					<th>Date</th>
					<th>Credit Note Number</th>
					<th>Supplier name</th>
					<th>Batch Number</th>
					<th>Quantity</th>
					<th>Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$find_credit_notes = $sql->query("SELECT * FROM `credit_note_items` WHERE `upc` = $upc ORDER BY `credit_note_id` DESC");
				while($find_credit_note = $sql->fetch_array($find_credit_notes)){
					$credit_note_id = $find_credit_note['credit_note_id'];
					$credit_note = $sql->fetch_array($sql->query("SELECT * FROM `credit_notes` WHERE `id` = $credit_note_id"));
					$purchase_id = $credit_note['purchase_id'];
					$purchase = $sql->fetch_array($sql->query("SELECT * FROM `purchases` WHERE `purchase_id` = $purchase_id"));
					$quantity = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `credit_note_items` WHERE `credit_note_id` = $credit_note_id"));
					$price = $sql->fetch_array($sql->query("SELECT `price_exc_vat` FROM `credit_note_items` WHERE `credit_note_id` = $credit_note_id LIMIT 1"));
					?>
					<tr>
						<td width="80px"><a href="<?=url?>/creditnotes/edit/<?=$purchase_id?>"><?=$credit_note_id?></a></td>
						<td><?=date("d/m/Y", $credit_note['date'])?></td>
						<td><?=$credit_note['credit_note_number']?></td>
						<td><?=page::getSupplierInfo($purchase['supplier_id'], "name")?></td>
						<td width="80px"><a href="<?=url?>/purchases/edit/<?=$purchase_id?>">TP-<b><?=$purchase['batch_number']?></b></a></td>
						<td><?=$quantity['total']?></td>
						<td><?=$price['price_exc_vat']?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}
}else{
	$today = date("d/m/Y");
	$last_month = date("d/m/Y", strtotime("- 1 month", time()));
	$spec_supplier = "";
	$vat = "";
	if(isset($_POST['view'])){
		if(!empty($_POST['from']) AND !empty($_POST['to'])){
			$to = $sql->smart(strtotime(str_replace("/", "-", $_POST['to'])));
			$from = $sql->smart(strtotime(str_replace("/", "-", $_POST['from'])));
			if($_POST['supplier'] != "all"){
				$spec_supplier = "AND `supplier_id` = " . $_POST['supplier'];
			}
			if(isset($_POST['vat'])){
				$vat = "AND `vat` = 1";
			}else{
				$vat = "AND `vat` >= 0";
			}
		}
	}else{
		$to = $sql->smart(strtotime(str_replace("/", "-", $today)));
		$from = $sql->smart(strtotime(str_replace("/", "-", $last_month)));
	}
	if(isset($_POST['show']) AND isset($_POST['batch_number'])){
		$batch_number = $sql->smart($_POST['batch_number']);
		$purchases = $sql->query("SELECT * FROM `purchases` WHERE `batch_number` = $batch_number ORDER BY `batch_number` DESC");
	}else{
		$purchases = $sql->query("SELECT * FROM `purchases` WHERE `invoice_date` <= $to AND `invoice_date` >= $from $vat $spec_supplier ORDER BY `batch_number` DESC");
	}
	$from_d = (isset($_POST['from'])) ? $_POST['from'] : $last_month;
	$to_d = (isset($_POST['to'])) ? $_POST['to'] : $today;
	$vat_d = (isset($_POST['vat'])) ? " checked" : "";
	$batch_number_d = (isset($_POST['batch_number'])) ? $_POST['batch_number'] : "";
	?>
	<form class="form-inline bottom-space pull-left" role="form" method="POST">
		<div class="form-group">
			<select class="form-control no-space" name="supplier">
				<option value="all">All Suppliers</option>
				<?php
				$suppliers = $sql->query("SELECT `id`, `name` FROM `suppliers` ORDER BY `name` ASC");
				while($supplier = $sql->fetch_array($suppliers)){
					if(isset($_POST['supplier'])){
						$supplier_d = ($_POST['supplier'] == $supplier['id']) ? " selected" : "";
					}else{
						$supplier_d = "";
					}
					?>
					<option value="<?=$supplier['id']?>"<?=$supplier_d?>><?=$supplier['name']?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label class="sr-only" for="from">Date From</label>
			<input type="text" name="from" class="form-control datepicker no-space" placeholder="Date From..." value="<?=$from_d?>">
		</div>
		<div class="form-group">
			<label class="sr-only" for="to">Date To</label>
			<input type="text" name="to" class="form-control datepicker no-space" placeholder="Date To..." value="<?=$to_d?>">
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="vat" <?=$vat_d?>>
				<span class="lbl"> VAT Only</span>
			</label>
		</div>
		<button type="submit" class="btn btn-default" name="view" style="margin-top: 1px;">View</button>
	</form>
	<form class="form-inline bottom-space pull-right" role="form" method="POST">
		<div class="form-group">
			<label class="sr-only" for="from">Batch Number</label>
			<input type="text" name="batch_number" class="form-control no-space" placeholder="Batch Number..." value="<?=$batch_number_d?>">
		</div>
		<button type="submit" class="btn btn-default" name="show" style="margin-top: 1px;">Show</button>
	</form>
	<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Batch Number</th>
				<th>Supplier name</th>
				<th>Invoice number</th>
				<th>Invoice date</th>
				<th>Delivery date</th>
				<th>VAT</th>
				<th>Amount</th>
				<th>Amount Paid</th>
				<th>SS</th>
				<th>FL</th>
				<th>QY</th>
				<th>IM</th>
				<th>Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$purchase_data = array();
			while($purchase = $sql->fetch_array($purchases)){
				$purchase_id = $purchase['purchase_id'];
					# Calculating total
				$shipping_charges = $purchase['shipping_charges'];
				$other_charges = $purchase['other_charges'];
				$item_sum = $sql->fetch_array($sql->query("SELECT SUM(`price_exc_vat` * `quantity`) AS `total_sum` FROM `purchase_items` WHERE `purchase_id` = $purchase_id"));
				$total_sum = $shipping_charges + $other_charges + $item_sum['total_sum'];
				if($purchase['vat'] == 1){
					$total = $total_sum * 1.2;
				}else{
					$total = $total_sum;
				}
				$total = number_format((float)$total, 2, ".", "");
				$amount_paid = $purchase['amount_paid'];
				if($total > $amount_paid){
					$payment_status = "danger";
				}elseif($total < $amount_paid){
					$payment_status = "warning";
				}else{
					$payment_status = "success";
				}
					# Calculating total
				$vat = ($purchase['vat'] == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '';
				$highlight = ($purchase['highlight'] == 1) ? ' class="success"' : '';
				$short_shipment = ($purchase['short_shipment'] == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '';
				$flag = ($purchase['flag'] == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '';
				$query = ($purchase['query'] == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '';
				$invoice_matched = ($purchase['invoice_matched'] == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '';
				?>
				<tr<?=$highlight?>>
				<td width="80px">TP-<b><?=$purchase['batch_number']?></b></td>
				<td><?=page::getSupplierInfo($purchase['supplier_id'], "name")?></td>
				<td><?=$purchase['invoice_number']?></td>
				<td><?=date("d/m/Y", $purchase['invoice_date'])?></td>
				<td><?=$purchase['delivery_date']?></td>
				<td class="text-center"><?=$vat?></td>
				<td>£<?=$total?></td>
				<td class="<?=$payment_status?>">£<?=$amount_paid?></td>
				<td class="text-center"><?=$short_shipment?></td>
				<td class="text-center"><?=$flag?></td>
				<td class="text-center"><?=$query?></td>
				<td class="text-center"><?=$invoice_matched?></td>
				<td>
					<a href="<?=url?>/purchases/edit/<?=$purchase['purchase_id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
				</td>
			</tr>
			<?php
			$purchase_data[] = array("purchase_id" => $purchase_id, "shipping_charges" => $shipping_charges, "other_charges" => $other_charges, "vat" => $purchase['vat']);
		}
		$vat_total = 0;
		$exc_vat_total = 0;
		$total_amount_paid = 0;
		foreach($purchase_data as $key => $purchase){
			$purchase_id = $purchase['purchase_id'];
			$shipping_charges = $purchase['shipping_charges'];
			$other_charges = $purchase['other_charges'];
			$vat = $purchase['vat'];
			$item_sum = $sql->fetch_array($sql->query("SELECT SUM(`price_exc_vat` * `quantity`) AS `total_sum` FROM `purchase_items` WHERE `purchase_id` = $purchase_id"));
			$total_sum = ($shipping_charges + $other_charges + $item_sum['total_sum']);
			if($vat == 1){
				$vat_total += $total_sum * 1.2;
			}else{
				$exc_vat_total += $total_sum;
			}
			$amount_paid = $sql->fetch_array($sql->query("SELECT SUM(`amount_paid`) AS `total_sum` FROM `purchases` WHERE `purchase_id` = $purchase_id"));
			$total_amount_paid += $amount_paid['total_sum'];
		}
		?>
		<tr>
			<td colspan="13"></td>
		</tr>
		<?php
		if($sql->num_rows($purchases) > 0){
			?>
			<tr>
				<td class="danger"></td>
				<td>Amount paid is smaller than the actual amount</td>
				<td colspan="4"></td>
				<td><b>Total</b></td>
				<td>£<?=number_format((float)$vat_total + $exc_vat_total, 2, ".", "")?></td>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td class="success"></td>
				<td>Amount paid fully</td>
				<td colspan="4"></td>
				<td><b>INC VAT total</b></td>
				<td>£<?=number_format((float)$vat_total, 2, ".", "")?></td>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td class="warning"></td>
				<td>Amount paid exceeds the actual amount</td>
				<td colspan="4"></td>
				<td><b>EXC VAT total</b></td>
				<td>£<?=number_format((float)$exc_vat_total, 2, ".", "")?></td>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td colspan="6"></td>
				<td><b>Total VAT paid</b></td>
				<td>£<?=number_format((float)$vat_total - ($vat_total / 1.2), 2, ".", "")?></td>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td colspan="6"></td>
				<td><b>Total paid</b></td>
				<td>£<?=number_format((float)$total_amount_paid, 2, ".", "")?></td>
				<td colspan="5"></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?php
}
?>