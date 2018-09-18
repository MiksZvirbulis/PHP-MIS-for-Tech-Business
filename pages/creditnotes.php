<?php
$page = new page("Manage Credit Notes", 1);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}

if($action == "add"){
	if(isset($_GET['purchase_id']) AND !empty($_GET['purchase_id'])){
		$purchase_id = (int)$_GET['purchase_id'];
		$find_purchase = $sql->num_rows($sql->query("SELECT `purchase_id` FROM `purchases` WHERE `purchase_id` = $purchase_id"));
		if($find_purchase == 0){
			page::alert("The Purchase ID was not found!", "danger");
			$form = true;
		}else{
			if(isset($_POST['add_creditnote'])){
				$errors = array();
				if(empty($_POST['date'])){
					$errors[] = "You forgot to select the credit note date!";
				}
				if(empty($_POST['purchase_id'])){
					$errors[] = "The Purchase ID is missing!";
				}
				if(empty($_POST['shipping_charges'])){
					$errors[] = "You forgot to enter the shipping charges!";
				}
				if(empty($_POST['other_charges'])){
					$errors[] = "You forgot to enter the other charges!";
				}
				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$credit_note_number = $sql->smart($_POST['credit_note_number']);
					$date = $sql->smart(strtotime(str_replace("/", "-", $_POST['date'])));
					$new_purchase_id = $sql->smart($_POST['purchase_id']);
					if(isset($_POST['vat'])){
						$vat = 1;
					}else{
						$vat = 0;
					}
					$shipping_charges = $sql->smart($_POST['shipping_charges']);
					$other_charges = $sql->smart($_POST['other_charges']);
					$note = $sql->smart($_POST['note']);
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
					if(isset($_POST['credit_note_matched'])){
						$credit_note_matched = 1;
					}else{
						$credit_note_matched = 0;
					}
					$date_created = $sql->smart(time());
					$created_by_id = $_COOKIE['user_id'];
					$sql->query("INSERT INTO `credit_notes` (`purchase_id`, `credit_note_number`, `date`, `vat`, `shipping_charges`, `other_charges`, `note`, `flag`, `highlight`, `query`, `credit_note_matched`, `date_created`, `created_by_id`)
						VALUES
						($new_purchase_id, $credit_note_number, $date, $vat, $shipping_charges, $other_charges, $note, $flag, $highlight, $query, $credit_note_matched, $date_created, $created_by_id)");

					$creditnotes = $sql->fetch_array($sql->query("SELECT `id` FROM `credit_notes` ORDER BY `id` DESC LIMIT 1"));
					$creditnote_id = $sql->smart($creditnotes['id']);
					if(isset($_POST['upc']) AND isset($_POST['cost_ex_vat']) AND isset($_POST['quantity'])){
						$upcs = $_POST['upc'];
						$cost_ex_vat = $_POST['cost_ex_vat'];
						$quantity = $_POST['quantity'];
						foreach($upcs as $key => $upc){
							if(!empty($upc)){
								$upc = $sql->smart($upc);
								$cost = $sql->smart($cost_ex_vat[$key]);
								$item_quantity = $sql->smart($quantity[$key]);
								$sql->query("INSERT INTO `credit_note_items` (`upc`, `quantity`, `price_exc_vat`, `credit_note_id`) VALUES ($upc, $item_quantity, $cost, $creditnote_id)");
							}
						}
					}
					header("Location: " . url . "/creditnotes/edit/" . str_replace("'", "", $creditnote_id));
				}
			}
			$purchase = $sql->fetch_array($sql->query("SELECT `supplier_id` FROM `purchases` WHERE `purchase_id` = $purchase_id"));
			$supplier_id = $purchase['supplier_id'];
			?>
			<form method="POST" class="form-horizontal">
				<fieldset>
					<legend>Credit Note Information</legend>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="col-xs-4 control-label">Supplier*</label>
							<div class="col-xs-8">
								<div class="input-group">
									<?php
									$supplier = $sql->fetch_array($sql->query("SELECT `name` FROM `suppliers` WHERE `id` = $supplier_id"));
									?>
									<input type="text" class="form-control" style="width: 330px" value="<?=$supplier['name']?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="date" class="col-xs-4 control-label">Credit note date*</label>
							<div class="col-xs-8">
								<input type="text" class="form-control datepicker" name="date" placeholder="Credit note date" value="<?=date("d/m/Y")?>">
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
							<label for="purchase_id" class="col-xs-4 control-label">Purchase ID*</label>
							<div class="col-xs-8">
								<div class="input-group">
									<input type="text" class="form-control" name="purchase_id" style="width: 330px" value="<?=$purchase_id?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="credit_note_number" class="col-xs-4 control-label">Credit Note number</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="credit_note_number" placeholder="Credit Note number">
							</div>
						</div>
						<div class="form-group">
							<label for="vat" class="col-xs-4 control-label">VAT credit note?</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="vat" onchange="update_total()" id="vat" checked>
										<span class="lbl"></span>
									</label>
								</div>
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
					<legend>Credit Note Summary</legend>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="flag" class="col-xs-4 control-label">Flag</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="flag">
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="highlight" class="col-xs-4 control-label">Highlight</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="highlight">
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="query" class="col-xs-4 control-label">Query</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="query">
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="credit_note_matched" class="col-xs-4 control-label">Credit note matched</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="credit_note_matched">
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
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
					<button type="submit" class="btn btn-primary btn-sm" name="add_creditnote">Add credit note</button>
				</div>
			</form>
			<?php
		}
	}elseif(isset($_GET['purchase_id']) AND empty($_GET['purchase_id'])){
		page::alert("The Purchase ID seems to be empty!", "danger");
		$form = true;
	}else{
		$form = true;
	}
	if(isset($form) AND $form === true){
		?>
		<form method="GET" action="<?=url?>/creditnotes/add/" class="form-horizontal">
			<div style="width: 300px; margin: 0 auto;">
				Enter Existing Purchase ID:<br />
				<input type="text" class="form-control" name="purchase_id" placeholder="Enter Purchase ID" maxlength="5">
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm">Proceed</button>
				</div>
			</div>
		</form>
		<?php
	}
}elseif($action == "edit"){
	if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
		$credit_note_id = (int)$pageInfo[3];
		$find_credit_note = $sql->num_rows($sql->query("SELECT `purchase_id` FROM `credit_notes` WHERE `id` = $credit_note_id"));
		if($find_credit_note == 0){
			page::alert("No credit notes were found with the ID specified!", "danger");
		}else{
			$credit_note = $sql->fetch_array($sql->query("SELECT * FROM `credit_notes` WHERE `id` = $credit_note_id"));
			$creditnote_id = $credit_note['id'];
			if(isset($_POST['edit_creditnote'])){
				if($credit_note['credit_note_matched'] == 1){
					if($page->hasLevel(1)){
						$proceed = true;
					}
				}else{
					$proceed = true;
				}
				if(isset($proceed) AND $proceed){
					$errors = array();
					if(empty($_POST['date'])){
						$errors[] = "You forgot to select the credit note date!";
					}
					if(empty($_POST['shipping_charges'])){
						$errors[] = "You forgot to enter the shipping charges!";
					}
					if(empty($_POST['other_charges'])){
						$errors[] = "You forgot to enter the other charges!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$credit_note_number = $sql->smart($_POST['credit_note_number']);
						$date = $sql->smart(strtotime(str_replace("/", "-", $_POST['date'])));
						if(isset($_POST['vat'])){
							$vat = 1;
						}else{
							$vat = 0;
						}
						$shipping_charges = $sql->smart($_POST['shipping_charges']);
						$other_charges = $sql->smart($_POST['other_charges']);
						$note = $sql->smart($_POST['note']);
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
						if(isset($_POST['credit_note_matched'])){
							$credit_note_matched = 1;
						}else{
							$credit_note_matched = 0;
						}
						$sql->query("UPDATE `credit_notes` SET
							`credit_note_number` = $credit_note_number,
							`date` = $date,
							`vat` = $vat,
							`shipping_charges` = $shipping_charges,
							`other_charges` = $other_charges,
							`note` = $note,
							`flag` = $flag,
							`highlight` = $highlight,
							`query` = $query,
							`credit_note_matched` = $credit_note_matched
							WHERE `id` = $credit_note_id
							") or die(mysql_error());
						$credit_note = $sql->fetch_array($sql->query("SELECT * FROM `credit_notes` WHERE `id` = $credit_note_id"));
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
									$sql->query("DELETE FROM `credit_note_items` WHERE `id` = $item_id");
									$creditnote_items = $sql->query("SELECT * FROM `credit_note_items` WHERE `credit_note_id` = $creditnote_id");
								}else{
									$sql->query("UPDATE `credit_note_items` SET `price_exc_vat` = $cost, `quantity` = $item_quantity WHERE `id` = $item_id");
									$creditnote_items = $sql->query("SELECT * FROM `credit_note_items` WHERE `credit_note_id` = $creditnote_id");
								}
							}
						}
						page::alert("Purchase edited successfully!", "success");
					}
				}
			}
			if(isset($_POST['add_items'])){
				if($credit_note['credit_note_matched'] == 1){
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
								$purchase_id = $credit_note['purchase_id'];
								$find_item = $sql->num_rows($sql->query("SELECT `id` FROM `purchase_items` WHERE `upc` = $upc AND `purchase_id` = $purchase_id"));
								if($find_item > 0){
									$upc = $sql->smart($upc);
									$cost = $sql->smart($cost_ex_vat[$key]);
									$item_quantity = $sql->smart($quantity[$key]);
									$sql->query("INSERT INTO `credit_note_items` (`upc`, `quantity`, `price_exc_vat`, `credit_note_id`) VALUES ($upc, $item_quantity, $cost, $creditnote_id)");
								}
								if($find_item == 0){
									$not_found = true;
								}else{
									$not_found = false;
								}
							}else{
								$not_found = 1;
							}
						}
						if($not_found === true){
							page::alert("Some items may have not been added as they were not found in this purchase!", "warning");
						}elseif($not_found == 1){

						}else{
							page::alert("Items added successfully!", "success");
						}
						$creditnote_items = $sql->query("SELECT * FROM `credit_note_items` WHERE `credit_note_id` = $creditnote_id");
					}
				}
			}
			$credit_note = $sql->fetch_array($sql->query("SELECT * FROM `credit_notes` WHERE `id` = $credit_note_id"));
			$purchase_id = $credit_note['purchase_id'];
			$purchase = $sql->fetch_array($sql->query("SELECT `supplier_id` FROM `purchases` WHERE `purchase_id` = $purchase_id"));
			$supplier_id = $purchase['supplier_id'];
			?>
			<form method="POST" class="form-horizontal">
				<fieldset>
					<legend>Credit Note Information</legend>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="col-xs-4 control-label">Supplier*</label>
							<div class="col-xs-8">
								<div class="input-group">
									<?php
									$supplier = $sql->fetch_array($sql->query("SELECT `name` FROM `suppliers` WHERE `id` = $supplier_id"));
									?>
									<input type="text" class="form-control" style="width: 330px" value="<?=$supplier['name']?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="date" class="col-xs-4 control-label">Credit note date*</label>
							<div class="col-xs-8">
								<input type="text" class="form-control datepicker" name="date" placeholder="Credit note date" value="<?=date('d/m/Y', $credit_note['date'])?>">
							</div>
						</div>
						<div class="form-group">
							<label for="note" class="col-xs-4 control-label">Note</label>
							<div class="col-xs-8">
								<textarea class="form-control" name="note" placeholder="Note"><?=$credit_note['note']?></textarea>
							</div>
						</div>
						<?php if($credit_note['rma_id'] != "0000000"): ?>
							<div class="form-group">
								<label for="rma_id" class="col-xs-4 control-label">RMA ID</label>
								<div class="col-xs-8">
									<input type="text" class="form-control" name="rma_id" value="<?=$credit_note['rma_id']?>" readonly>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="purchase_id" class="col-xs-4 control-label">Purchase ID*</label>
							<div class="col-xs-8">
								<div class="input-group">
									<input type="text" class="form-control" name="purchase_id" style="width: 330px" value="<?=$credit_note['purchase_id']?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="credit_note_number" class="col-xs-4 control-label">Credit Note number</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="credit_note_number" placeholder="Credit Note number" value="<?=$credit_note['credit_note_number']?>">
							</div>
						</div>
						<?php
						$vat_checked = ($credit_note['vat'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="vat" class="col-xs-4 control-label">VAT credit note?</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="vat" onchange="update_total()" id="vat"<?=$vat_checked?>>
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<legend>Shipping and other charges</legend>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="shipping_charges" class="col-xs-4 control-label">Shipping charges*</label>
							<div class="col-xs-8">
								<input type="text" id="shipping_charges" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="shipping_charges" placeholder="Shipping charges" value="<?=$credit_note['shipping_charges']?>">
							</div>
						</div>
						<div class="form-group">
							<label for="other_charges" class="col-xs-4 control-label">Other charges*</label>
							<div class="col-xs-8">
								<input type="text" id="other_charges" class="form-control" onclick="money(this)" onblur="money(this), update_total()" name="other_charges" placeholder="Other charges" value="<?=$credit_note['other_charges']?>">
							</div>
						</div>
					</div>
					<legend>Credit Note Summary</legend>
					<div class="col-xs-6">
						<?php
						$flag_checked = ($credit_note['flag'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="flag" class="col-xs-4 control-label">Flag</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="flag"<?=$flag_checked?>>
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
						<?php
						$highlight_checked = ($credit_note['highlight'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="highlight" class="col-xs-4 control-label">Highlight</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="highlight"<?=$highlight_checked?>>
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
						<?php
						$query_checked = ($credit_note['query'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="query" class="col-xs-4 control-label">Query</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="query"<?=$query_checked?>>
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
						<?php
						$cdm_checked = ($credit_note['credit_note_matched'] == 1) ? " checked" : "";
						?>
						<div class="form-group">
							<label for="credit_note_matched" class="col-xs-4 control-label">Credit note matched</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="credit_note_matched"<?=$cdm_checked?>>
										<span class="lbl"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6">
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
					</div>
				</fieldset>
				<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
					<thead>
						<tr>
							<th>#</th>
							<th>UPC</th>
							<th>Item</th>
							<th>History Price</th>
							<th>Cost exc. VAT</th>
							<th>Quantity</th>
							<th>Total exc. VAT</th>
							<th>Delete</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$creditnote_items = $sql->query("SELECT * FROM `credit_note_items` WHERE `credit_note_id` = $credit_note_id");
						$i = 1;
						while($creditnote_item = $sql->fetch_array($creditnote_items)){
							$upc = $creditnote_item['upc'];
							?>
							<tr id="item_count">
								<td><?=$i?><input type="hidden" name="item_id[]" value="<?=$creditnote_item['id']?>"></td>
								<td><?=$creditnote_item['upc']?></td>
								<td><?=page::getItemInfo($creditnote_item['upc'], "description")?></td>
								<?php
								$purchase_id = $credit_note['purchase_id'];
								$price = $sql->fetch_array($sql->query("SELECT `price_exc_vat` FROM `purchase_items` WHERE `upc` = $upc AND `purchase_id` = $purchase_id ORDER BY `id` DESC LIMIT 1"));
								?>
								<td>£<?=(empty($price['price_exc_vat'])) ? "0.00" : $price['price_exc_vat']?></td>
								<td><input class="form-control" type="text" id="item_cost_<?=$i?>" onclick="money(this)" onblur="money(this), item_total(<?=$i?>), update_total()" name="cost_ex_vat[]" type="text" placeholder="Cost exc. VAT" value="<?=$creditnote_item['price_exc_vat']?>"></td>
								<td><input class="form-control" type="text" id="item_quantity_<?=$i?>" onblur="item_total(<?=$i?>), update_total()" name="quantity[]" placeholder="Quantity" value="<?=$creditnote_item['quantity']?>"></td>
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
				if($credit_note['credit_note_matched'] == 1){
					if($page->hasLevel(1)){
						?>
						<div class="form-button">
							<button type="submit" class="btn btn-primary btn-sm" name="edit_creditnote">Edit credit note</button>
						</div>
						<?php
					}
				}else{
					?>
					<div class="form-button">
						<button type="submit" class="btn btn-primary btn-sm" name="edit_creditnote">Edit credit note</button>
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
				if($credit_note['credit_note_matched'] == 1){
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
		page::alert("The purchase ID specified was not found to have any credit note adjustments!", "danger");
	}
}else{
	?>
	<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Credit note ID</th>
				<th>Supplier name</th>
				<th>Credit note number</th>
				<th>Credit note date</th>
				<th>Amount</th>
				<th>Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$credit_notes = $sql->query("SELECT * FROM `credit_notes` ORDER BY `id` DESC");
			while($credit_note = $sql->fetch_array($credit_notes)){
				$credit_note_id = $credit_note['id'];
				$purchase_id = $credit_note['purchase_id'];
				$shipping_charges = $credit_note['shipping_charges'];
				$other_charges = $credit_note['other_charges'];
				$item_sum = $sql->fetch_array($sql->query("SELECT SUM(`price_exc_vat` * `quantity`) AS `total_sum` FROM `credit_note_items` WHERE `credit_note_id` = $credit_note_id"));
				$purchase = $sql->fetch_array($sql->query("SELECT `supplier_id`, `purchase_id` FROM `purchases` WHERE `purchase_id` = $purchase_id"));
				$total_sum = $shipping_charges + $other_charges + $item_sum['total_sum'];
				if($credit_note['vat'] == 1){
					$total = $total_sum * 1.2;
				}else{
					$total = $total_sum;
				}
				$total = number_format((float)$total, 2, ".", "");
				$highlight = ($credit_note['highlight'] == 1) ? ' class="success"' : '';
				?>
				<tr<?=$highlight?>>
				<td width="120px"><b><?=$credit_note_id?></b></td>
				<td><?=page::getSupplierInfo($purchase['supplier_id'], "name")?></td>
				<td><?=$credit_note['credit_note_number']?></td>
				<td><?=date("d/m/Y", $credit_note['date'])?></td>
				<td>£<?=$total?></td>
				<td>
					<a href="<?=url?>/creditnotes/edit/<?=$credit_note_id?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?php
}
?>