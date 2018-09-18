<?php
$page = new page("Manage RMA", 3);
if(isset($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}
if($action == "add" AND page::moduleAccess(2)){
	$page->changeTitle("Add RMA");
	if(isset($_POST['add_rma'])){
		$errors = array();
		if(empty($_POST['date_created'])){
			$errors[] = "You forgot to select the RMA creation date!";
		}
		if(empty($_POST['supplier_id'])){
			$errors[] = "You forgot to select the supplier!";
		}
		if(empty($_POST['supplier_rma_no'])){
			$errors[] = "You forgot to enter the suppliers RMA number!";
		}
		if($_POST['sent_to_supplier'] == "1"){
			if(empty($_POST['date_sent'])){
				$errors[] = "You forgot to select the date sent!";
			}
		}
		if(!isset($_POST['upc'])){
			$errors[] = "No items have been selected!";
		}
		if(!isset($_POST['quantity'])){
			$errors[] = "No quantities have been set!";
		}
		if(!isset($_POST['rma_number'])){
			$errors[] = "No rma numbers have been set!";
		}
		if(!isset($_POST['reason'])){
			$errors[] = "No reasons selected!";
		}
		if(!isset($_POST['return_action'])){
			$errors[] = "No return action selected!";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$date_created = $sql->smart($_POST['date_created']);
			$supplier_id = $sql->smart($_POST['supplier_id']);
			$supplier_rma_no = $sql->smart($_POST['supplier_rma_no']);
			if($_POST['sent_to_supplier'] == 1){
				$sent_to_supplier = 1;
				$date_sent = $sql->smart($_POST['date_sent']);
			}else{
				$sent_to_supplier = 0;
				$date_sent = $sql->smart("");
			}
			if(isset($_POST['rma_completed'])){
				$rma_completed = 1;
			}else{
				$rma_completed = 0;
			}
			$created_by_id = $sql->smart($_COOKIE['user_id']);
			$note = $sql->smart($_POST['note']);
			$date = $sql->smart(time());
			$sql->query("INSERT INTO `rma` 
				(`date_created`, `supplier_id`, `supplier_rma_no`, `sent_to_supplier`, `date_sent`, `rma_completed`, `created_by_id`, `note`, `date`) 
				VALUES 
				($date_created, $supplier_id, $supplier_rma_no, $sent_to_supplier, $date_sent, $rma_completed, $created_by_id, $note, $date)
				") or die(mysql_error());
			$rma = $sql->fetch_array($sql->query("SELECT `id` FROM `rma` ORDER BY `id` DESC LIMIT 1"));
			$rma_id = $sql->smart($rma['id']);
			$upcs = $_POST['upc'];
			$quantitys = $_POST['quantity'];
			$rma_numbers = $_POST['rma_number'];
			$reasons = $_POST['reason'];
			$return_actions = $_POST['return_action'];
			$rejected_rcvd_backs = (isset($_POST['rejected_rcvd_back'])) ? $_POST['rejected_rcvd_back'] : array();
			$rejected_rcvd_back_dates = $_POST['rejected_rcvd_back_date'];
			$rejected_rcvd_notes = $_POST['rejected_rcvd_note'];
			$replaced_rcvd_backs = (isset($_POST['replaced_rcvd_back'])) ? $_POST['replaced_rcvd_back'] : array();
			$replaced_rcvd_back_dates = $_POST['replaced_rcvd_back_date'];
			$replaced_rcvd_notes = $_POST['replaced_rcvd_note'];
			$credit_note_ids = $_POST['credit_note_id'];
			$upgrade_reasons = $_POST['upgrade_reason'];
			$upgrade_new_upcs = $_POST['upgrade_new_upc'];
			$upgrade_paid_extras = (isset($_POST['upgrade_paid_extra'])) ? $_POST['upgrade_paid_extra'] : array();
			$upgrade_paid_amounts = $_POST['upgrade_paid_amount'];
			$upgrade_rcvd_backs = (isset($_POST['upgrade_rcvd_back'])) ? $_POST['upgrade_rcvd_back'] : array();
			$upgrade_rcvd_back_dates = $_POST['upgrade_rcvd_back_date'];
			$upgrade_rcvd_notes = $_POST['upgrade_rcvd_note'];
			foreach($upcs as $key => $upc){
				if(!empty($upc)){
					$key_for_check = $key + 1;
					$upc = $sql->smart($upc);
					$quantity = $quantitys[$key];
					$rma_number = $sql->smart($rma_numbers[$key]);
					$return_action = $sql->smart($return_actions[$key]);
					$reason = $sql->smart($reasons[$key]);
					if(isset($rejected_rcvd_backs[$key_for_check])){
						$rejected_rcvd_back = 1;
					}else{
						$rejected_rcvd_back = 0;
					}
					$rejected_rcvd_back_date = $sql->smart($rejected_rcvd_back_dates[$key]);
					$rejected_rcvd_note = $sql->smart($rejected_rcvd_notes[$key]);
					if(isset($replaced_rcvd_backs[$key_for_check])){
						$replaced_rcvd_back = 1;
					}else{
						$replaced_rcvd_back = 0;
					}
					$replaced_rcvd_back_date = $sql->smart($replaced_rcvd_back_dates[$key]);
					$replaced_rcvd_note = $sql->smart($replaced_rcvd_notes[$key]);
					$credit_note_id = $sql->smart($credit_note_ids[$key]);
					if($return_action == "upgraded"){
						$upgrade_reason = $sql->smart($upgrade_reasons[$key]);
					}else{
						$upgrade_reason = $sql->smart("");
					}
					$upgrade_new_upc = $sql->smart($upgrade_new_upcs[$key]);
					if(isset($upgrade_paid_extras[$key_for_check])){
						$upgrade_paid_extra = 1;
					}else{
						$upgrade_paid_extra = 0;
					}
					$upgrade_paid_amount = $sql->smart($upgrade_paid_amounts[$key]);
					if(isset($upgrade_rcvd_backs[$key_for_check])){
						$upgrade_rcvd_back = 1;
					}else{
						$upgrade_rcvd_back = 0;
					}
					$upgrade_rcvd_back_date = $sql->smart($upgrade_rcvd_back_dates[$key]);
					$upgrade_rcvd_note = $sql->smart($upgrade_rcvd_notes[$key]);
					$sql->query("INSERT INTO `rma_items`
						(`rma_id`, `upc`, `quantity`, `rma_number`, `reason`, `return_action`, `rejected_rcvd_back`, `rejected_rcvd_back_date`, `rejected_rcvd_note`, `replaced_rcvd_back`, `replaced_rcvd_back_date`, `replaced_rcvd_note`, `credit_note_id`, `upgrade_reason`, `upgrade_new_upc`, `upgrade_paid_extra`, `upgrade_paid_amount`, `upgrade_rcvd_back`, `upgrade_rcvd_back_date`, `upgrade_rcvd_note`)
						VALUES ($rma_id, $upc, $quantity, $rma_number, $reason, $return_action, $rejected_rcvd_back, $rejected_rcvd_back_date, $rejected_rcvd_note, $replaced_rcvd_back, $replaced_rcvd_back_date, $replaced_rcvd_note, $credit_note_id, $upgrade_reason, $upgrade_new_upc, $upgrade_paid_extra, $upgrade_paid_amount, $upgrade_rcvd_back, $upgrade_rcvd_back_date, $upgrade_rcvd_note)
						") or die(mysql_error());
				}
			}
			header("Location: " . url . "/rma/edit/" . str_replace("'", "", $rma_id));
			page::alert("RMA added successfully!", "success");
		}
	}
	?>
	<form method="POST" class="form-horizontal">
		<fieldset>
			<legend>RMA Information</legend>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="date_created" class="col-xs-4 control-label">RMA date created*</label>
					<div class="col-xs-8">
						<input type="text" class="form-control datepicker" name="date_created" placeholder="RMA date created">
					</div>
				</div>
				<div class="form-group">
					<label for="supplier_id" class="col-xs-4 control-label">Supplier*</label>
					<div class="col-xs-8">
						<select class="form-control" name="supplier_id">
							<option disabled selected>Select supplier</option>
							<?php
							$suppliers = $sql->query("SELECT `id`, `name` FROM `suppliers` ORDER BY `name` ASC");
							while($supplier = $sql->fetch_array($suppliers)){
								?>
								<option value="<?=$supplier['id']?>"><?=$supplier['name']?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="supplier_rma_no" class="col-xs-4 control-label">Supplier RMA number*</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="supplier_rma_no" placeholder="Supplier RMA number">
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
					<label for="invoice_number" class="col-xs-4 control-label">Sent to supplier</label>
					<div class="col-xs-8">
						<select class="form-control" onChange="rma_sent(this)" name="sent_to_supplier">
							<option value="1">Yes</option>
							<option value="0" selected>No</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="date_sent" class="col-xs-4 control-label">Date sent*</label>
					<div class="col-xs-8">
						<input type="text" class="form-control datepicker" id="date_sent" name="date_sent" placeholder="Date sent" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="vat" class="col-xs-4 control-label">RMA Completed</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="rma_completed">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
				<thead>
					<tr>
						<th>#</th>
						<th>Category</th>
						<th>Sub category</th>
						<th>Item</th>
						<th>UPC</th>
						<th>Quantity</th>
						<th>RMA number</th>
						<th>Reason</th>
						<th>Return action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for($i = 1; $i <= 5; $i++){
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
							<td><input class="form-control" type="text" name="quantity[]" placeholder="Quantity" value="1"></td>
							<td><input class="form-control" type="text" name="rma_number[]" placeholder="RMA number"></td>
							<td>
								<select class="form-control" name="reason[]" style="width: 200px">
									<option selected disabled>Choose reason</option>
									<?php
									$existing_reasons = $sql->query("SELECT `description` FROM `rma_item_reasons` ORDER BY `description` ASC");
									while($existing_reason = $sql->fetch_array($existing_reasons)){
										?>
										<option value="<?=$existing_reason['description']?>"><?=$existing_reason['description']?></option>
										<?php
									}
									?>
								</select>
							</td>
							<td style="width: 150px;">
								<select class="form-control" id="return_action_<?=$i?>" name="return_action[]" onChange="returnAction(<?=$i?>)">
									<option value="outstanding" selected>Outstanding</option>
									<option value="rejected">Rejected</option>
									<option value="replaced">Replaced</option>
									<option value="credited">Credited</option>
									<option value="upgraded">Upgraded</option>
								</select>
							</td>
						</tr>
						<tr id="rejected_<?=$i?>" style="display: none;">
							<td></td>
							<td colspan="2">Received back? <label><input type="checkbox" id="rejected_rcvd_back_<?=$i?>" name="rejected_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="receivedStatus(<?=$i?>, 'rejected')"><span class="lbl"></span></label></td>
							<td colspan="5" id="rejected_return_<?=$i?>"></td>
							<td class="rejected_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="rejected_rcvd_back_date[]" placeholder="Return date"></td>
							<td class="rejected_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="rejected_rcvd_note[]" placeholder="Additional details"></td>
						</tr>
						<tr id="replaced_<?=$i?>" style="display: none;">
							<td></td>
							<td colspan="2">Received back? <label><input type="checkbox" id="replaced_rcvd_back_<?=$i?>" name="replaced_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="receivedStatus(<?=$i?>, 'replaced')"><span class="lbl"></span></label></td>
							<td colspan="5" id="replaced_return_<?=$i?>"></td>
							<td class="replaced_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="replaced_rcvd_back_date[]" placeholder="Replacement date"></td>
							<td class="replaced_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="replaced_rcvd_note[]" placeholder="Additional details"></td>
						</tr>
						<tr id="credited_<?=$i?>" style="display: none;">
							<td></td>
							<td>Credit note ID</td>
							<td><input type="text" class="form-control" id="credit_note_id_<?=$i?>" name="credit_note_id[]" placeholder="Credit note ID" readonly></td>
							<td colspan="5"></td>
						</tr>
						<tr class="upgraded_<?=$i?>" style="display: none;">
							<td></td>
							<td>Reason for upgrade</td>
							<td colspan="2"><input type="text" class="form-control" id="upgrade_reason_<?=$i?>" name="upgrade_reason[]" placeholder="Reason for upgrade" value="Found faulty and upgraded"></td>
							<td colspan="2">UPC of new item</td>
							<td colspan="2"><input type="text" class="form-control" id="upgrade_new_upc_<?=$i?>" name="upgrade_new_upc[]" placeholder="UPC of new item"></td>
						</tr>
						<tr class="upgraded_<?=$i?>" style="display: none;">
							<td></td>
							<td colspan="2">Received back? <label><input type="checkbox" id="upgraded_rcvd_back_<?=$i?>" name="upgrade_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="receivedStatus(<?=$i?>, 'upgraded')"><span class="lbl"></span></label></td>
							<td colspan="5" id="upgraded_return_<?=$i?>"></td>
							<td class="upgraded_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="upgrade_rcvd_back_date[]" placeholder="Return date"></td>
							<td class="upgraded_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="upgrade_rcvd_note[]" placeholder="Additional details"></td>
						</tr>
						<tr class="upgraded_<?=$i?>" style="display: none;">
							<td></td>
							<td colspan="2">Extra amount paid? <label><input type="checkbox" id="paid_extra_<?=$i?>" name="upgrade_paid_extra[<?=$i?>]" style="margin-left: 20px;" onChange="upgradeExtra(<?=$i?>)"><span class="lbl"></span></label></td>
							<td colspan="5" id="extra_<?=$i?>"></td>
							<td class="extra_<?=$i?>" style="display: none;">Amount paid</td>
							<td class="extra_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" id="upgrade_paid_amount_<?=$i?>" name="upgrade_paid_amount[]" onclick="money(this)" onblur="money(this)" placeholder="Amount paid" value="0.00"></td>
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
				<button type="submit" class="btn btn-primary btn-sm" name="add_rma">Add RMA</button>
			</div>
		</form>
		<?php
	}elseif($action == "edit" AND page::moduleAccess(3)){
		$page->changeTitle("Edit RMA");
		if(isset($pageInfo[3])){
			$rma_id = (int)$pageInfo[3];
			$test_existance = $sql->num_rows($sql->query("SELECT `id` FROM `rma` WHERE `id` = $rma_id"));
			if($test_existance == 0){
				page::alert("No RMA was found with this ID!", "danger");
			}else{
				$rma = $sql->fetch_array($sql->query("SELECT * FROM `rma` WHERE `id` = $rma_id"));
				if(isset($_POST['edit_rma'])){
					if($rma['rma_completed'] == 1){
						if($page->hasLevel(1)){
							$proceed = true;
						}
					}else{
						$proceed = true;
					}
					if(isset($proceed) AND $proceed){
						$errors = array();
						if(empty($_POST['date_created'])){
							$errors[] = "You forgot to select the RMA creation date!";
						}
						if(empty($_POST['supplier_id'])){
							$errors[] = "You forgot to select the supplier!";
						}
						if(empty($_POST['supplier_rma_no'])){
							$errors[] = "You forgot to enter the suppliers RMA number!";
						}
						if($_POST['sent_to_supplier'] == "1"){
							if(empty($_POST['date_sent'])){
								$errors[] = "You forgot to select the date sent!";
							}
						}
						if(count($errors) > 0){
							foreach($errors as $error){
								page::alert($error, "danger");
							}
						}else{
							$rma_id = $sql->smart($rma_id);
							$date_created = $sql->smart($_POST['date_created']);
							$supplier_id = $sql->smart($_POST['supplier_id']);
							$supplier_rma_no = $sql->smart($_POST['supplier_rma_no']);
							if($_POST['sent_to_supplier'] == 1){
								$sent_to_supplier = 1;
								$date_sent = $sql->smart($_POST['date_sent']);
							}else{
								$sent_to_supplier = 0;
								$date_sent = $sql->smart("");
							}
							if(isset($_POST['rma_completed'])){
								$rma_completed = 1;
							}else{
								$rma_completed = 0;
							}
							$note = $sql->smart($_POST['note']);
							$date = $sql->smart(time());
							$sql->query("UPDATE `rma` SET 
								`date_created` = $date_created,
								`supplier_id` = $supplier_id,
								`supplier_rma_no` = $supplier_rma_no,
								`sent_to_supplier` = $sent_to_supplier,
								`date_sent` = $date_sent,
								`rma_completed` = $rma_completed,
								`note` = $note, 
								`date` = $date WHERE `id` = $rma_id") or die(mysql_error());
							$rma_id = $sql->smart($rma_id);
							$item_ids = $_POST['item_id'];
							$quantitys = $_POST['quantity'];
							$rma_numbers = $_POST['rma_number'];
							$reasons = $_POST['reason'];
							$return_actions = $_POST['return_action'];
							$rejected_rcvd_backs = (isset($_POST['rejected_rcvd_back'])) ? $_POST['rejected_rcvd_back'] : array();
							$rejected_rcvd_back_dates = $_POST['rejected_rcvd_back_date'];
							$rejected_rcvd_notes = $_POST['rejected_rcvd_note'];
							$replaced_rcvd_backs = (isset($_POST['replaced_rcvd_back'])) ? $_POST['replaced_rcvd_back'] : array();
							$replaced_rcvd_back_dates = $_POST['replaced_rcvd_back_date'];
							$replaced_rcvd_notes = $_POST['replaced_rcvd_note'];
							$credit_note_ids = $_POST['credit_note_id'];
							$upgrade_reasons = $_POST['upgrade_reason'];
							$upgrade_new_upcs = $_POST['upgrade_new_upc'];
							$upgrade_paid_extras = (isset($_POST['upgrade_paid_extra'])) ? $_POST['upgrade_paid_extra'] : array();
							$upgrade_paid_amounts = $_POST['upgrade_paid_amount'];
							$upgrade_rcvd_backs = (isset($_POST['upgrade_rcvd_back'])) ? $_POST['upgrade_rcvd_back'] : array();
							$upgrade_rcvd_back_dates = $_POST['upgrade_rcvd_back_date'];
							$upgrade_rcvd_notes = $_POST['upgrade_rcvd_note'];
							$delete = (isset($_POST['delete'])) ? $_POST['delete'] : array();
							foreach($item_ids as $key => $item_id){
								$key_for_check = $key + 1;
								$item_id = $sql->smart($item_id);
								$quantity = $quantitys[$key];
								$rma_number = $sql->smart($rma_numbers[$key]);
								$return_action = $sql->smart($return_actions[$key]);
								$reason = $sql->smart($reasons[$key]);
								if(isset($rejected_rcvd_backs[$key_for_check])){
									$rejected_rcvd_back = 1;
								}else{
									$rejected_rcvd_back = 0;
								}
								$rejected_rcvd_back_date = $sql->smart($rejected_rcvd_back_dates[$key]);
								$rejected_rcvd_note = $sql->smart($rejected_rcvd_notes[$key]);
								if(isset($replaced_rcvd_backs[$key_for_check])){
									$replaced_rcvd_back = 1;
								}else{
									$replaced_rcvd_back = 0;
								}
								$replaced_rcvd_back_date = $sql->smart($replaced_rcvd_back_dates[$key]);
								$replaced_rcvd_note = $sql->smart($replaced_rcvd_notes[$key]);
								$credit_note_id = $sql->smart($credit_note_ids[$key]);
								if($return_action == "upgraded"){
									$upgrade_reason = $sql->smart($upgrade_reasons[$key]);
								}else{
									$upgrade_reason = $sql->smart("");
								}
								$upgrade_new_upc = $sql->smart($upgrade_new_upcs[$key]);
								if(isset($upgrade_paid_extras[$key_for_check])){
									$upgrade_paid_extra = 1;
								}else{
									$upgrade_paid_extra = 0;
								}
								$upgrade_paid_amount = $sql->smart($upgrade_paid_amounts[$key]);
								if(isset($upgrade_rcvd_backs[$key_for_check])){
									$upgrade_rcvd_back = 1;
								}else{
									$upgrade_rcvd_back = 0;
								}
								$upgrade_rcvd_back_date = $sql->smart($upgrade_rcvd_back_dates[$key]);
								$upgrade_rcvd_note = $sql->smart($upgrade_rcvd_notes[$key]);
								if(isset($delete[$key_for_check])){
									$sql->query("DELETE FROM `rma_items` WHERE `id` = $item_id");
								}else{
									$sql->query("UPDATE `rma_items` SET `quantity` = $quantity,
										`rma_number` = $rma_number,
										`reason` = $reason,
										`return_action` = $return_action,
										`rejected_rcvd_back` = $rejected_rcvd_back,
										`rejected_rcvd_back_date` = $rejected_rcvd_back_date,
										`rejected_rcvd_note` = $rejected_rcvd_note,
										`replaced_rcvd_back` = $replaced_rcvd_back,
										`replaced_rcvd_back_date` = $replaced_rcvd_back_date,
										`replaced_rcvd_note` = $replaced_rcvd_note,
										`credit_note_id` = $credit_note_id,
										`upgrade_reason` = $upgrade_reason,
										`upgrade_new_upc` = $upgrade_new_upc,
										`upgrade_paid_extra` = $upgrade_paid_extra,
										`upgrade_paid_amount` = $upgrade_paid_amount,
										`upgrade_rcvd_back` = $upgrade_rcvd_back,
										`upgrade_rcvd_back_date` = $upgrade_rcvd_back_date,
										`upgrade_rcvd_note` = $upgrade_rcvd_note WHERE `id` = $item_id") or die(mysql_error());
									$find_credit_note = $sql->num_rows($sql->query("SELECT `id` FROM `credit_notes` WHERE `id` = $credit_note_id"));
									if($find_credit_note == 1){
										$sql->query("UPDATE `credit_notes` SET `rma_id` = $pageInfo[3] WHERE `id` = $credit_note_id");
									}
								}
							}
							$rma_id = $pageInfo[3];
							page::alert("RMA edited successfully!", "success");
						}
					}
				}
				if(isset($_POST['add_items'])){
					if($rma['rma_completed'] == 1){
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
						if(!isset($_POST['quantity'])){
							$errors[] = "No quantities have been set!";
						}
						if(!isset($_POST['rma_number'])){
							$errors[] = "No rma numbers have been set!";
						}
						if(!isset($_POST['reason'])){
							$errors[] = "No reasons selected!";
						}
						if(!isset($_POST['return_action'])){
							$errors[] = "No return action selected!";
						}
						if(count($errors) > 0){
							foreach($errors as $error){
								page::alert($error, "danger");
							}
						}else{
							$rma_id = $sql->smart($rma_id);
							$upcs = $_POST['upc'];
							$quantitys = $_POST['quantity'];
							$rma_numbers = $_POST['rma_number'];
							$reasons = $_POST['reason'];
							$return_actions = $_POST['return_action'];
							$rejected_rcvd_backs = (isset($_POST['rejected_rcvd_back'])) ? $_POST['rejected_rcvd_back'] : array();
							$rejected_rcvd_back_dates = $_POST['rejected_rcvd_back_date'];
							$rejected_rcvd_notes = $_POST['rejected_rcvd_note'];
							$replaced_rcvd_backs = (isset($_POST['replaced_rcvd_back'])) ? $_POST['replaced_rcvd_back'] : array();
							$replaced_rcvd_back_dates = $_POST['replaced_rcvd_back_date'];
							$replaced_rcvd_notes = $_POST['replaced_rcvd_note'];
							$credit_note_ids = $_POST['credit_note_id'];
							$upgrade_reasons = $_POST['upgrade_reason'];
							$upgrade_new_upcs = $_POST['upgrade_new_upc'];
							$upgrade_paid_extras = (isset($_POST['upgrade_paid_extra'])) ? $_POST['upgrade_paid_extra'] : array();
							$upgrade_paid_amounts = $_POST['upgrade_paid_amount'];
							$upgrade_rcvd_backs = (isset($_POST['upgrade_rcvd_back'])) ? $_POST['upgrade_rcvd_back'] : array();
							$upgrade_rcvd_back_dates = $_POST['upgrade_rcvd_back_date'];
							$upgrade_rcvd_notes = $_POST['upgrade_rcvd_note'];
							foreach($upcs as $key => $upc){
								if(!empty($upc)){
									$key_for_check = $key + 1;
									$upc = $sql->smart($upc);
									$quantity = $quantitys[$key];
									$rma_number = $sql->smart($rma_numbers[$key]);
									$return_action = $sql->smart($return_actions[$key]);
									$reason = $sql->smart($reasons[$key]);
									if(isset($rejected_rcvd_backs[$key_for_check])){
										$rejected_rcvd_back = 1;
									}else{
										$rejected_rcvd_back = 0;
									}
									$rejected_rcvd_back_date = $sql->smart($rejected_rcvd_back_dates[$key]);
									$rejected_rcvd_note = $sql->smart($rejected_rcvd_notes[$key]);
									if(isset($replaced_rcvd_backs[$key_for_check])){
										$replaced_rcvd_back = 1;
									}else{
										$replaced_rcvd_back = 0;
									}
									$replaced_rcvd_back_date = $sql->smart($replaced_rcvd_back_dates[$key]);
									$replaced_rcvd_note = $sql->smart($replaced_rcvd_notes[$key]);
									$credit_note_id = $sql->smart($credit_note_ids[$key]);
									if($return_action == "upgraded"){
										$upgrade_reason = $sql->smart($upgrade_reasons[$key]);
									}else{
										$upgrade_reason = $sql->smart("");
									}
									$upgrade_new_upc = $sql->smart($upgrade_new_upcs[$key]);
									if(isset($upgrade_paid_extras[$key_for_check])){
										$upgrade_paid_extra = 1;
									}else{
										$upgrade_paid_extra = 0;
									}
									$upgrade_paid_amount = $sql->smart($upgrade_paid_amounts[$key]);
									if(isset($upgrade_rcvd_backs[$key_for_check])){
										$upgrade_rcvd_back = 1;
									}else{
										$upgrade_rcvd_back = 0;
									}
									$upgrade_rcvd_back_date = $sql->smart($upgrade_rcvd_back_dates[$key]);
									$upgrade_rcvd_note = $sql->smart($upgrade_rcvd_notes[$key]);
									$sql->query("INSERT INTO `rma_items`
										(`rma_id`, `upc`, `quantity`, `rma_number`, `reason`, `return_action`, `rejected_rcvd_back`, `rejected_rcvd_back_date`, `rejected_rcvd_note`, `replaced_rcvd_back`, `replaced_rcvd_back_date`, `replaced_rcvd_note`, `credit_note_id`, `upgrade_reason`, `upgrade_new_upc`, `upgrade_paid_extra`, `upgrade_paid_amount`, `upgrade_rcvd_back`, `upgrade_rcvd_back_date`, `upgrade_rcvd_note`)
										VALUES ($rma_id, $upc, $quantity, $rma_number, $reason, $return_action, $rejected_rcvd_back, $rejected_rcvd_back_date, $rejected_rcvd_note, $replaced_rcvd_back, $replaced_rcvd_back_date, $replaced_rcvd_note, $credit_note_id, $upgrade_reason, $upgrade_new_upc, $upgrade_paid_extra, $upgrade_paid_amount, $upgrade_rcvd_back, $upgrade_rcvd_back_date, $upgrade_rcvd_note)
										") or die(mysql_error());
								}
							}
							page::alert("RMA items added successfully!", "success");
						}
					}
				}
				$rma = $sql->fetch_array($sql->query("SELECT * FROM `rma` WHERE `id` = $rma_id"));
				?>
				<form method="POST" class="form-horizontal">
					<fieldset>
						<legend>RMA Information</legend>
						<div class="col-xs-6">
							<div class="form-group">
								<label for="date_created" class="col-xs-4 control-label">RMA date created*</label>
								<div class="col-xs-8">
									<input type="text" class="form-control datepicker" name="date_created" placeholder="RMA date created" value="<?=$rma['date_created']?>">
								</div>
							</div>
							<div class="form-group">
								<label for="supplier_id" class="col-xs-4 control-label">Supplier*</label>
								<div class="col-xs-8">
									<select class="form-control" name="supplier_id">
										<option disabled selected>Select supplier</option>
										<?php
										$suppliers = $sql->query("SELECT `id`, `name` FROM `suppliers` ORDER BY `name` ASC");
										while($supplier = $sql->fetch_array($suppliers)){
											$selected = ($rma['supplier_id'] == $supplier['id']) ? " selected" : "";
											?>
											<option value="<?=$supplier['id']?>"<?=$selected?>><?=$supplier['name']?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="supplier_rma_no" class="col-xs-4 control-label">Supplier RMA number*</label>
								<div class="col-xs-8">
									<input type="text" class="form-control" name="supplier_rma_no" placeholder="Supplier RMA number" value="<?=$rma['supplier_rma_no']?>">
								</div>
							</div>
							<div class="form-group">
								<label for="note" class="col-xs-4 control-label">Note</label>
								<div class="col-xs-8">
									<textarea class="form-control" name="note" placeholder="Note"><?=$rma['note']?></textarea>
								</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<label for="invoice_number" class="col-xs-4 control-label">Sent to supplier</label>
								<div class="col-xs-8">
									<?php
									$sent_to_supplier_yes = ($rma['sent_to_supplier'] == "1") ? " selected" : "";
									$sent_to_supplier_no = ($rma['sent_to_supplier'] == "0") ? " selected" : "";
									?>
									<select class="form-control" onChange="rma_sent(this)" name="sent_to_supplier">
										<option value="1"<?=$sent_to_supplier_yes?>>Yes</option>
										<option value="0"<?=$sent_to_supplier_no?>>No</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="date_sent" class="col-xs-4 control-label">Date sent*</label>
								<div class="col-xs-8">
									<?php
									$date_sent = ($rma['sent_to_supplier'] == "1") ? "" : " disabled";
									?>
									<input type="text" class="form-control datepicker" id="date_sent" name="date_sent" placeholder="Date sent" value="<?=$rma['date_sent']?>"<?=$date_sent?>>
								</div>
							</div>
							<div class="form-group">
								<label for="vat" class="col-xs-4 control-label">RMA Completed</label>
								<div class="col-xs-8">
									<div class="checkbox">
										<label>
											<?php
											$rma_completed = ($rma['rma_completed'] == "1") ? " checked" : "";
											?>
											<input type="checkbox" name="rma_completed"<?=$rma_completed?>>
											<span class="lbl"></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
							<thead>
								<tr>
									<th>#</th>
									<th>UPC</th>
									<th>Item</th>
									<th>Quantity</th>
									<th>RMA number</th>
									<th>Reason</th>
									<th>Return action</th>
									<th>Delete</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$rma_items = $sql->query("SELECT * FROM `rma_items` WHERE `rma_id` = $rma_id");
								$i = 1;
								while($rma_item = $sql->fetch_array($rma_items)){
									$upc = $rma_item['upc'];
									?>
									<tr id="rma_item_count">
										<td id="item_count"><input type="hidden" name="item_id[]" value="<?=$rma_item['id']?>"><?=$i?></td>
										<td><?=$rma_item['upc']?></td>
										<td><?=page::getItemInfo($rma_item['upc'], "description")?></td>
										<td><input class="form-control" type="text" name="quantity[]" placeholder="Quantity" value="<?=$rma_item['quantity']?>"></td>
										<td><input class="form-control" type="text" name="rma_number[]" placeholder="RMA number" value="<?=$rma_item['rma_number']?>"></td>
										<td>
											<select class="form-control" name="reason[]" style="width: 200px">
												<option selected disabled>Choose reason</option>
												<?php
												$existing_reasons = $sql->query("SELECT `description` FROM `rma_item_reasons` ORDER BY `description` ASC");
												while($existing_reason = $sql->fetch_array($existing_reasons)){
													$selected = ($rma_item['reason'] == $existing_reason['description']) ? " selected" : "";
													?>
													<option value="<?=$existing_reason['description']?>"<?=$selected?>><?=$existing_reason['description']?></option>
													<?php
												}
												?>
											</select>
										</td>
										<td>
											<select class="form-control" id="return_action_<?=$i?>" name="return_action[]" onChange="editReturnAction(<?=$i?>)">
												<option value="outstanding"<?=($rma_item['return_action'] == "outstanding") ? " selected" : ""?>>Outstanding</option>
												<option value="rejected"<?=($rma_item['return_action'] == "rejected") ? " selected" : ""?>>Rejected</option>
												<option value="replaced"<?=($rma_item['return_action'] == "replaced") ? " selected" : ""?>>Replaced</option>
												<option value="credited"<?=($rma_item['return_action'] == "credited") ? " selected" : ""?>>Credited</option>
												<option value="upgraded"<?=($rma_item['return_action'] == "upgraded") ? " selected" : ""?>>Upgraded</option>
											</select>
										</td>
										<td>
											<label>
												<input type="checkbox" name="delete[<?=$i?>]">
												<span class="lbl"></span>
											</label>
										</td>
									</tr>
									<tr id="rejected_<?=$i?>" style="display: none;">
										<?php
										$rejected_rcvd_back = ($rma_item['rejected_rcvd_back'] == 1) ? " checked" : "";
										$replaced_rcvd_back = ($rma_item['replaced_rcvd_back'] == 1) ? " checked" : "";
										$upgrade_rcvd_back = ($rma_item['upgrade_rcvd_back'] == 1) ? " checked" : "";
										$upgrade_paid_extra = ($rma_item['upgrade_paid_extra'] == 1) ? " checked" : "";
										?>
										<td></td>
										<td colspan="2"><label>Received back? <input type="checkbox" id="rejected_rcvd_back_<?=$i?>" name="rejected_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="receivedStatus(<?=$i?>, 'rejected')"<?=$rejected_rcvd_back?>><span class="lbl"></span></label></td>
										<td colspan="5" id="rejected_return_<?=$i?>"></td>
										<td class="rejected_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="rejected_rcvd_back_date[]" placeholder="Return date" value="<?=$rma_item['rejected_rcvd_back_date']?>"></td>
										<td class="rejected_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="rejected_rcvd_note[]" placeholder="Additional details" value="<?=$rma_item['rejected_rcvd_note']?>"></td>
									</tr>
									<tr id="replaced_<?=$i?>" style="display: none;">
										<td></td>
										<td colspan="2"><label>Received back? <input type="checkbox" id="replaced_rcvd_back_<?=$i?>" name="replaced_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="receivedStatus(<?=$i?>, 'replaced')"<?=$replaced_rcvd_back?>><span class="lbl"></span></label></td>
										<td colspan="5" id="replaced_return_<?=$i?>"></td>
										<td class="replaced_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="replaced_rcvd_back_date[]" placeholder="Replacement date" value="<?=$rma_item['replaced_rcvd_back_date']?>"></td>
										<td class="replaced_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="replaced_rcvd_note[]" placeholder="Additional details" value="<?=$rma_item['replaced_rcvd_note']?>"></td>
									</tr>
									<tr id="credited_<?=$i?>" style="display: none;">
										<td></td>
										<td>Credit note ID</td>
										<td><input type="text" class="form-control" id="credit_note_id_<?=$i?>" name="credit_note_id[]" placeholder="Credit note ID" value="<?=$rma_item['credit_note_id']?>" readonly></td>
										<td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#credit_note_<?=$i?>">Select Credit Note</button></td>
										<td colspan="4"></td>
									</tr>
									<div class="modal fade" id="credit_note_<?=$i?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
													<h4 class="modal-title" id="myModalLabel">Select Credit Note for <?=page::getItemInfo($rma_item['upc'], "description")?></h4>
												</div>
												<div class="modal-body">
													<div class="list-group">
														<?php $find_credit_notes = $sql->query("SELECT `credit_note_id` FROM `credit_note_items` WHERE `upc` = $upc"); ?>
														<?php if($sql->num_rows($find_credit_notes) == 0): ?>
															<a class="list-group-item list-group-item-danger">
																No credit notes were found for this item
															</a>
														<?php else: ?>
															<?php while($credit_note = $sql->fetch_array($find_credit_notes)): ?>
																<a class="list-group-item pointer" onClick="fillCreditID(<?=$i?>, '<?=$credit_note['credit_note_id']?>')">
																	<?=page::getSupplierInfo(page::getPurchaseInfo(page::getCreditNoteInfo($credit_note['credit_note_id'], "purchase_id"), "supplier_id"), "name")?> - <?=date("d/m/Y", page::getCreditNoteInfo($credit_note['credit_note_id'], "date"))?>
																	<span class="badge"><?=$credit_note['credit_note_id']?></span>
																</a>
															<?php endwhile; ?>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<tr class="upgraded_<?=$i?>" style="display: none;">
										<td></td>
										<td>Reason for upgrade</td>
										<td colspan="2"><input type="text" class="form-control" id="upgrade_reason_<?=$i?>" name="upgrade_reason[]" placeholder="Reason for upgrade" value="<?=$rma_item['upgrade_reason']?>"></td>
										<td colspan="2">UPC of new item</td>
										<td colspan="2"><input type="text" class="form-control" id="upgrade_new_upc_<?=$i?>" name="upgrade_new_upc[]" placeholder="UPC of new item" value="<?=$rma_item['upgrade_new_upc']?>"></td>
									</tr>
									<tr class="upgraded_<?=$i?>" style="display: none;">
										<td></td>
										<td colspan="2"><label>Received back? <input type="checkbox" id="upgraded_rcvd_back_<?=$i?>" name="upgrade_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="receivedStatus(<?=$i?>, 'upgraded')"<?=$upgrade_rcvd_back?>><span class="lbl"></span></label></td>
										<td colspan="5" id="upgraded_return_<?=$i?>"></td>
										<td class="upgraded_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="upgrade_rcvd_back_date[]" placeholder="Return date" value="<?=$rma_item['upgrade_rcvd_back_date']?>"></td>
										<td class="upgraded_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="upgrade_rcvd_note[]" placeholder="Additional details" value="<?=$rma_item['upgrade_rcvd_note']?>"></td>
									</tr>
									<tr class="upgraded_<?=$i?>" style="display: none;">
										<td></td>
										<td colspan="2"><label>Extra amount paid? <input type="checkbox" id="paid_extra_<?=$i?>" name="upgrade_paid_extra[<?=$i?>]" style="margin-left: 20px;" onChange="upgradeExtra(<?=$i?>)"<?=$upgrade_paid_extra?>><span class="lbl"></span></label></td>
										<td colspan="5" id="extra_<?=$i?>"></td>
										<td class="extra_<?=$i?>" style="display: none;">Amount paid</td>
										<td class="extra_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" id="upgrade_paid_amount_<?=$i?>" name="upgrade_paid_amount[]" onclick="money(this)" onblur="money(this)" placeholder="Amount paid" value="<?=$rma_item['upgrade_paid_amount']?>"></td>
									</tr>
									<?php
									$i++;
								}
								?>
							</tbody>
						</table>
						<?php
						if($rma['rma_completed'] == 1){
							if($page->hasLevel(1)){
								?>
								<div class="form-button">
									<button type="submit" class="btn btn-primary btn-sm" name="edit_rma">Edit RMA</button>
								</div>
								<?php
							}
						}else{
							?>
							<div class="form-button">
								<button type="submit" class="btn btn-primary btn-sm" name="edit_rma">Edit RMA</button>
							</div>
							<?php
						}
						?>
					</form>
					<form method="POST">
						<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Category</th>
									<th>Sub category</th>
									<th>Item</th>
									<th>UPC</th>
									<th>Quantity</th>
									<th>RMA number</th>
									<th>Reason</th>
									<th>Return action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								for($i = 1; $i <= 5; $i++){
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
										<td><input class="form-control" type="text" name="quantity[]" placeholder="Quantity" value="1"></td>
										<td><input class="form-control" type="text" name="rma_number[]" placeholder="RMA number"></td>
										<td>
											<select class="form-control" name="reason[]" style="width: 200px">
												<option selected disabled>Choose reason</option>
												<?php
												$existing_reasons = $sql->query("SELECT `description` FROM `rma_item_reasons` ORDER BY `description` ASC");
												while($existing_reason = $sql->fetch_array($existing_reasons)){
													?>
													<option value="<?=$existing_reason['description']?>"><?=$existing_reason['description']?></option>
													<?php
												}
												?>
											</select>
										</td>
										<td>
											<select class="form-control" id="new_return_action_<?=$i?>" name="return_action[]" onChange="newReturnAction(<?=$i?>)">
												<option value="outstanding" selected>Outstanding</option>
												<option value="rejected">Rejected</option>
												<option value="replaced">Replaced</option>
												<option value="credited">Credited</option>
												<option value="upgraded">Upgraded</option>
											</select>
										</td>
									</tr>
									<tr id="new_rejected_<?=$i?>" style="display: none;">
										<td></td>
										<td colspan="2"><label>Received back? <input type="checkbox" id="new_rejected_rcvd_back_<?=$i?>" name="rejected_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="newReceivedStatus(<?=$i?>, 'rejected')"><span class="lbl"></span></label></td>
										<td colspan="5" id="new_rejected_return_<?=$i?>"></td>
										<td class="new_rejected_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="rejected_rcvd_back_date[]" placeholder="Return date"></td>
										<td class="new_rejected_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="rejected_rcvd_note[]" placeholder="Additional details"></td>
									</tr>
									<tr id="new_replaced_<?=$i?>" style="display: none;">
										<td></td>
										<td colspan="2"><label>Received back? <input type="checkbox" id="new_replaced_rcvd_back_<?=$i?>" name="replaced_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="newReceivedStatus(<?=$i?>, 'replaced')"><span class="lbl"></span></label></td>
										<td colspan="5" id="new_replaced_return_<?=$i?>"></td>
										<td class="new_replaced_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="replaced_rcvd_back_date[]" placeholder="Replacement date"></td>
										<td class="new_replaced_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="replaced_rcvd_note[]" placeholder="Additional details"></td>
									</tr>
									<tr id="new_credited_<?=$i?>" style="display: none;">
										<td></td>
										<td>Credit note ID</td>
										<td><input type="text" class="form-control" id="new_credit_note_id_<?=$i?>" name="credit_note_id[]" placeholder="Credit note ID" readonly></td>
										<td colspan="5"></td>
									</tr>
									<tr class="new_upgraded_<?=$i?>" style="display: none;">
										<td></td>
										<td>Reason for upgrade</td>
										<td colspan="2"><input type="text" class="form-control" id="new_upgrade_reason_<?=$i?>" name="upgrade_reason[]" placeholder="Reason for upgrade" value="Found faulty and upgraded"></td>
										<td colspan="2">UPC of new item</td>
										<td colspan="2"><input type="text" class="form-control" id="new_upgrade_new_upc_<?=$i?>" name="upgrade_new_upc[]" placeholder="UPC of new item"></td>
									</tr>
									<tr class="new_upgraded_<?=$i?>" style="display: none;">
										<td></td>
										<td colspan="2"><label>Received back? <input type="checkbox" id="new_upgraded_rcvd_back_<?=$i?>" name="upgrade_rcvd_back[<?=$i?>]" style="margin-left: 20px;" onChange="newReceivedStatus(<?=$i?>, 'upgraded')"><span class="lbl"></span></label></td>
										<td colspan="5" id="new_upgraded_return_<?=$i?>"></td>
										<td class="new_upgraded_return_<?=$i?>" style="display: none;"><input type="text" class="form-control datepicker" name="upgrade_rcvd_back_date[]" placeholder="Return date"></td>
										<td class="new_upgraded_return_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" name="upgrade_rcvd_note[]" placeholder="Additional details"></td>
									</tr>
									<tr class="new_upgraded_<?=$i?>" style="display: none;">
										<td></td>
										<td colspan="2"><label>Extra amount paid? <input type="checkbox" id="new_paid_extra_<?=$i?>" name="upgrade_paid_extra[<?=$i?>]" style="margin-left: 20px;" onChange="newUpgradeExtra(<?=$i?>)"><span class="lbl"></span></label></td>
										<td colspan="5" id="new_extra_<?=$i?>"></td>
										<td class="new_extra_<?=$i?>" style="display: none;">Amount paid</td>
										<td class="new_extra_<?=$i?>" style="display: none;" colspan="4"><input type="text" class="form-control" id="new_upgrade_paid_amount_<?=$i?>" name="upgrade_paid_amount[]" onclick="money(this)" onblur="money(this)" placeholder="Amount paid" value="0.00"></td>
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
						if($rma['rma_completed'] == 1){
							if($page->hasLevel(1)){
								?>
								<div class="form-button">
									<button type="submit" class="btn btn-primary btn-sm" name="add_items">Add Items</button>
								</div>
								<?php
							}
						}else{
							?>
							<div class="form-button">
								<button type="submit" class="btn btn-primary btn-sm" name="add_items">Add Items</button>
							</div>
							<?php
						}
						?>
					</form>
					<?php
				}
			}else{
				page::alert("No RMA ID was specified!", "danger");
			}
		}elseif($action == "search" AND isset($pageInfo[3]) AND !empty($pageInfo[3])){
			$page->changeTitle("Search item");
			$upc = $pageInfo[3];
			$find_item = $sql->num_rows($sql->query("SELECT `id` FROM `rma_items` WHERE `upc` = $upc"));
			if($find_item == 0){
				page::alert("No purchases of this item were found!", "danger");
			}else{
				$find_rma = $sql->fetch_array($sql->query("SELECT `rma_id` FROM `rma_items` WHERE `upc` = $upc"));
				foreach($find_rma as $rma_id){
					$rma = $sql->fetch_array($sql->query("SELECT * FROM `rma` WHERE `id` = $rma_id"));
					$quantity = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `rma_items` WHERE `upc` = $upc"));
					$status = $sql->fetch_array($sql->query("SELECT `rma_completed` FROM `rma` WHERE `id` = $rma_id"));
					?>
					<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Supplier name</th>
								<th>RMA Number</th>
								<th>Date Sent</th>
								<th>Quantity</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<td>RM-<a href="<?=url?>/rma/edit/<?=$rma_id?>"><?=$rma['id']?></td>
							<td><?=page::getSupplierInfo($rma['supplier_id'], "name")?></td>
							<td><?=$rma['supplier_rma_no']?></td>
							<td><?=$rma['date_sent']?></td>
							<td><?=$quantity['total']?></td>
							<td><?=($status['rma_completed'] == "1") ? "Completed" : "Outstanding"?></td>
						</tbody>
					</table>

					<?php
				}
			}
		}else{
			?>
			<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Supplier name</th>
						<th>Supplier RMA number</th>
						<th>Date sent</th>
						<th>Created by</th>
						<th>Completed</th>
						<th>Edit</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$rmas = $sql->query("SELECT * FROM `rma` ORDER BY `id` DESC");
					while($rma = $sql->fetch_array($rmas)){
						$date_sent = ($rma['date_sent'] == "") ? "Not sent" : $rma['date_sent'];
						$rma_completed = ($rma['rma_completed'] == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '';
						?>
						<tr>
							<td>RM-<?=$rma['id']?></td>
							<td><?=page::getSupplierInfo($rma['supplier_id'], "name")?></td>
							<td><?=$rma['supplier_rma_no']?></td>
							<td><?=$date_sent?></td>
							<td><?=page::getUserInfo($rma['created_by_id'], "name")?> <?=page::getUserInfo($rma['created_by_id'], "surname")?></td>
							<td><?=$rma_completed?></td>
							<td>
								<a href="<?=url?>/rma/edit/<?=$rma['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Edit</button></a>
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