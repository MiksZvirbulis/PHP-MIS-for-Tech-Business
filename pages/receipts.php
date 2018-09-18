<?php
$page = new page("Receipts", 3);
$global_user_id = $_COOKIE['user_id'];
$suppliers = $sql->query("SELECT * FROM `suppliers` ORDER BY `name` ASC");
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}
if($action == "add"){
	if(isset($_POST['add_receipt'])){
		$page->changeTitle("Add receipt");
		$errors = array();
		$services = array();
		if(empty($_POST['customer_name'])){
			$errors[] = "You forgot to enter the customer's name!";
		}
		if(empty($_POST['customer_number'])){
			$errors[] = "You forgot to enter the customer's number!";
		}else{
			if(strlen($_POST['customer_number']) < 11){
				$errors[] = "The number you entered is shorter than 11 digits!";
			}
		}
		if(!empty($_POST['alternative_number'])){
			if(strlen($_POST['alternative_number']) < 11){
				$errors[] = "The alternative number you entered is shorter than 11 digits!";
			}
		}
		if(empty($_POST['computer_information'])){
			$errors[] = "You forgot to enter the computer information!";
		}
		if(empty($_POST['amount_paid'])){
			$errors[] = "You forgot to enter the amount paid!";
		}else{
			if($_POST['amount_paid'] > 0){
				if(!isset($_POST['payment_type'])){
					$errors[] = "You forgot to choose the payment type!";
				}
			}
		}
		if(isset($_POST['service_id']) AND isset($_POST['price']) AND isset($_POST['computer_quantity'])){
			$service_ids = $_POST['service_id'];
			$prices = $_POST['price'];
			$computer_quantitys = $_POST['computer_quantity'];
			foreach($service_ids as $key => $service_id){
				if(empty($prices[$key])){
					$errors[] = "You forgot to enter the price for one of the services!";
					$price = false;
				}else{
					$price = $prices[$key];
				}
				if(empty($computer_quantitys[$key])){
					$errors[] = "You forgot to enter the computer quantity for one of the services!";
					$computer_quantity = false;
				}else{
					$computer_quantity = $computer_quantitys[$key];
				}
				if($price != false AND $computer_quantity != false){
					$services[] = array("service_id" => $service_id, "price" => $price, "computer_quantity" => $computer_quantity);
				}
			}
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$customer_name = $sql->smart($_POST['customer_name']);
			$customer_surname = $sql->smart($_POST['customer_surname']);
			$customer_email = $sql->smart($_POST['customer_email']);
			$customer_number = $sql->smart($_POST['customer_number']);
			$alternative_number = $sql->smart($_POST['alternative_number']);
			$computer_information = $sql->smart($_POST['computer_information']);
			if(isset($_POST['scratches_on_body'])){
				$scratches_on_body = 1;
			}else{
				$scratches_on_body = 0;
			}
			if(isset($_POST['scratches_on_screen'])){
				$scratches_on_screen = 1;
			}else{
				$scratches_on_screen = 0;
			}
			if(isset($_POST['screws_missing'])){
				$screws_missing = 1;
			}else{
				$screws_missing = 0;
			}
			if(isset($_POST['rubber_pad_missing'])){
				$rubber_pad_missing = 1;
			}else{
				$rubber_pad_missing = 0;
			}
			if(isset($_POST['keyboard_btns_missing'])){
				$keyboard_btns_missing = 1;
			}else{
				$keyboard_btns_missing = 0;
			}
			if(isset($_POST['charger_missing'])){
				$charger_missing = 1;
			}else{
				$charger_missing = 0;
			}
			if(isset($_POST['battery_missing'])){
				$battery_missing = 1;
			}else{
				$battery_missing = 0;
			}
			if(isset($_POST['checked'])){
				$checked = 1;
			}else{
				$checked = 0;
			}
			if(empty($_POST['other_charges'])){
				$other_charges = $sql->smart("0.00");
			}else{
				$other_charges = $sql->smart($_POST['other_charges']);
			}
			$discount = $sql->smart($_POST['discount']);
			$discount_reason = $sql->smart($_POST['discount_reason']);
			$customer_note = $sql->smart($_POST['customer_note']);
			$note = $sql->smart($_POST['note']);
			$amount_paid = $sql->smart($_POST['amount_paid']);
			if(isset($_POST['payment_type'])){
				$payment_type = $sql->smart($_POST['payment_type']);
			}else{
				$payment_type = $sql->smart("");
			}
			$estimated_collection = $sql->smart($_POST['estimated_collection']);
			$added = $sql->smart(time());
			$created_by_id = $sql->smart($global_user_id);
			$status = $sql->smart($_POST['status']);
			$sql->query("INSERT INTO `receipts` 
				(`customer_name`, `customer_surname`, `customer_email`, `customer_number`, `alternative_number`, `computer_information`, `scratches_on_body`, `scratches_on_screen`, `screws_missing`, `rubber_pad_missing`, `keyboard_btns_missing`, `charger_missing`, `battery_missing`, `checked`, `other_charges`, `discount`, `discount_reason`, `note`, `customer_note`, `amount_paid`, `payment_type`, `estimated_collection`, `added`, `created_by_id`, `status`)
				VALUES
				($customer_name, $customer_surname, $customer_email, $customer_number, $alternative_number, $computer_information, $scratches_on_body, $scratches_on_screen, $screws_missing, $rubber_pad_missing, $keyboard_btns_missing, $charger_missing, $battery_missing, $checked, $other_charges, $discount, $discount_reason, $note, $customer_note, $amount_paid, $payment_type, $estimated_collection, $added, $created_by_id, $status)
				");
			$receipts = $sql->fetch_array($sql->query("SELECT `id` FROM `receipts` ORDER BY `id` DESC LIMIT 1"));
			$receipt_id = $sql->smart($receipts['id']);
			$page->logAction("receipt", $receipt_id, false, "receipt_created");
			foreach($services as $service){
				$service_id = $sql->smart($service['service_id']);
				$price = $sql->smart($service['price']);
				$computer_quantity = $sql->smart($service['computer_quantity']);
				$sql->query("INSERT INTO `receipt_items` (`receipt_id`, `service_id`, `price`, `computer_quantity`) VALUES ($receipt_id, $service_id, $price, $computer_quantity)");
			}
			page::alert("Receipt successfully added!", "success");
			$receipt_win = url . "/receipt/" . str_replace("'", "", $receipt_id);
			$receipt_edit = url . "/receipts/edit/" . str_replace("'", "", $receipt_id);
			?>
			<script type="text/javascript">
				window.open("<?php echo $receipt_win; ?>", "_blank");

				function goEdit(){
					window.location = "<?php echo $receipt_edit; ?>";
				}
				setTimeout("goEdit()", 1000);
			</script>
			<?php
		}
	}
	if(isset($_GET['duplicate']) AND !empty($_GET['duplicate'])){
		$duplicate_receipt_id = (int)$_GET['duplicate'];
		$find_duplicate = $sql->num_rows($sql->query("SELECT `id` FROM `receipts` WHERE `id` = $duplicate_receipt_id"));
		if($find_duplicate == 0){
			$duplicate = false;
		}else{
			$duplicate = $sql->fetch_array($sql->query("SELECT * FROM `receipts` WHERE `id` = $duplicate_receipt_id"));
		}
	}else{
		$duplicate = false;
	}
	?>
	<form method="POST" class="form-horizontal">
		<?php echo page::alert("<center><strong>PLEASE MAKE SURE YOU ARE LOGGED IN BEFORE SUBMITTING A RECEIPT!</strong></center>", "danger"); ?>
		<fieldset>
			<legend>Customer</legend>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="customer_name" class="col-xs-4 control-label">Customer's name*</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="customer_name" placeholder="Name" value="<?php echo ($duplicate != false) ? $duplicate['customer_name'] : ''; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="customer_number" class="col-xs-4 control-label">Customer's number*</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="customer_number" placeholder="Number" value="<?php echo ($duplicate != false) ? $duplicate['customer_number'] : ''; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="alternative_number" class="col-xs-4 control-label">Alternative number</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="alternative_number" placeholder="Alternative number" value="<?php echo ($duplicate != false) ? $duplicate['alternative_number'] : ''; ?>">
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="customer_name" class="col-xs-4 control-label">Customer's surname</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="customer_surname" placeholder="Surname" value="<?php echo ($duplicate != false) ? $duplicate['customer_surname'] : ''; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="customer_email" class="col-xs-4 control-label">Customer's email</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="customer_email" placeholder="Email address" value="<?php echo ($duplicate != false) ? $duplicate['customer_email'] : ''; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="computer_information" class="col-xs-4 control-label">Computer information*</label>
					<div class="col-xs-8">
						<textarea type="text" class="form-control" name="computer_information" rows="5" placeholder="Computer information"><?php echo ($duplicate != false) ? $duplicate['computer_information'] : ''; ?></textarea>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="scratches_on_body" class="col-xs-4 control-label">Scratches on Body</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="scratches_on_body">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="scratches_on_screen" class="col-xs-4 control-label">Scratches on Screen</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="scratches_on_screen">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="screws_missing" class="col-xs-4 control-label">Screws Missing</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="screws_missing">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="charger_missing" class="col-xs-4 control-label">Battery Missing</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="battery_missing">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="rubber_pad_missing" class="col-xs-4 control-label">Rubber Pad Missing</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="rubber_pad_missing">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="keyboard_btns_missing" class="col-xs-4 control-label">Keyboard Buttons Missing</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="keyboard_btns_missing">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="charger_missing" class="col-xs-4 control-label">Charger Missing</label>
					<div class="col-xs-8">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="charger_missing">
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<legend>Information</legend>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="estimated_collection" class="col-xs-4 control-label">Estimated collection date</label>
					<div class="col-xs-8">
						<input type="text" class="form-control datepicker" name="estimated_collection" placeholder="Estimated collection date">
					</div>
				</div>
				<div class="form-group">
					<label for="other_charges" class="col-xs-4 control-label">Other charges</label>
					<div class="col-xs-8">
						<input type="text" id="other_charges" class="form-control" onclick="money(this)" onblur="money(this), update_receipt_total()" name="other_charges" placeholder="Other charges" value="0.00">
					</div>
				</div>
				<div class="form-group">
					<label for="discount" class="col-xs-4 control-label">Discount</label>
					<div class="col-xs-8">
						<input type="text" id="discount" class="form-control" onclick="money(this)" onblur="money(this), update_receipt_total(), receipt_discount_reason(this)" name="discount" placeholder="Discount" value="0.00">
					</div>
				</div>
				<div class="form-group" id="discount_reason" style="display: none;">
					<label for="estimated_collection" class="col-xs-4 control-label">Reason for Discount</label>
					<div class="col-xs-8">
						<input type="text" class="form-control" name="discount_reason" placeholder="Reason for Discount">
					</div>
				</div>
				<div class="form-group">
					<label for="customer_note" class="col-xs-4 control-label">Note for Customer</label>
					<div class="col-xs-8">
						<textarea type="text" class="form-control" name="customer_note" rows="5" placeholder="Note for Customer"></textarea>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="amount_paid" class="col-xs-4 control-label">Status</label>
					<div class="col-xs-8">
						<select class="form-control" name="status" id="status" onChange="update_receipt_total()">
							<?php
							foreach($page->receipt_status as $key => $name){
								?>
								<option value="<?=$key?>"><?=$name?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="amount_paid" class="col-xs-4 control-label">Amount paid</label>
					<div class="col-xs-8">
						<input type="text" id="amount_paid" class="form-control" onclick="money(this)" onblur="money(this), update_receipt_total(), receipt_payment_type(this)" name="amount_paid" placeholder="Amount paid" value="0.00">
					</div>
				</div>
				<div class="form-group" id="payment_type" style="display: none;">
					<label for="payment_type" class="col-xs-4 control-label">Payment type</label>
					<div class="col-xs-8">
						<select class="form-control" name="payment_type">
							<option selected disabled>Select Payment type</option>
							<option value="cash">Cash</option>
							<option value="card">Card</option>
							<option value="cheq">Cheque</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-4 control-label">Total</label>
					<div class="col-xs-8">
						<input type="text" id="total" class="form-control" value="0.00" readonly>
					</div>
				</div>
				<div class="form-group">
					<label for="payment_due" class="col-xs-4 control-label">Payment due</label>
					<div class="col-xs-8">
						<input type="text" id="payment_due" class="form-control" value="0.00" readonly>
					</div>
				</div>
			</div>
			<div class="col-xs-10">
				<div class="form-group">
					<label for="note" class="col-xs-4 control-label">Note</label>
					<div class="col-xs-8">
						<textarea type="text" class="form-control" name="note" rows="5" placeholder="Note"></textarea>
					</div>
				</div>
			</div>
		</fieldset>
		<?php page::alert("If a service will not be selected - the whole row will not be inserted!", "info"); ?>
		<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Service</th>
					<th>Price</th>
					<th>Computer quantity</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<?php
				for($i = 1; $i <= 5; $i++){
					?>
					<tr>
						<td id="item_count"><?=$i?></td>
						<td>
							<select class="form-control" onchange="get_service_price(<?=$i?>)" id="select_service_<?=$i?>" name="service_id[]">
								<option selected disabled>Choose service</option>
								<?php
								$services = $sql->query("SELECT * FROM `services` ORDER BY `title` ASC");
								while($service = $sql->fetch_array($services)){
									?>
									<option value="<?=$service['id']?>"><?=$service['title']?></option>
									<?php
								}
								?>
							</select>
						</td>
						<td><input class="form-control" type="text" id="service_cost_<?=$i?>" onclick="money(this)" onchange="update_receipt_total()" onblur="money(this), service_total(<?=$i?>), update_receipt_total()" name="price[]" type="text" placeholder="Price" value="0.00"></td>
						<td><input class="form-control" type="text" id="service_quantity_<?=$i?>" onblur="service_total(<?=$i?>), update_receipt_total()" name="computer_quantity[]" placeholder="Quantity" value="0"></td>
						<td><input class="form-control service_total" type="text" id="service_total_<?=$i?>" value="0.00" readonly></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm" name="add_receipt">Add receipt</button>
		</div>
	</form>
	<?php
}elseif($action == "del"){
	$page->changeTitle("Delete receipt");
	if(isset($pageInfo[3])){
		$receipt_id = (int)$pageInfo[3];
		$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `receipts` WHERE `id` = $receipt_id"));
		if($check_existance == 0){
			page::alert("No receipts were found with that ID!", "danger");
			header("refresh:2;url=".url."/receipts");
		}else{
			$sql->query("DELETE FROM `receipts` WHERE `id` = $receipt_id");
			$sql->query("DELETE FROM `receipt_items` WHERE `receipt_id` = $receipt_id");
			page::alert("Receipt successfully deleted!<br />Redirecting...", "success");
			header("refresh:2;url=".url."/receipts");
		}
	}else{
		page::alert("No ID was specified!", "danger");
	}
}elseif($action == "edit"){
	$page->changeTitle("Edit receipt");
	if(isset($pageInfo[3])){
		$receipt_id = (int)$pageInfo[3];
		$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `receipts` WHERE `id` = $receipt_id"));
		if($check_existance == 0){
			page::alert("No receipts were found with that ID!", "danger");
			header("refresh:2;url=".url."/receipts");
		}else{
			$receipt = $sql->fetch_array($sql->query("SELECT * FROM `receipts` WHERE `id` = $receipt_id"));
			if(isset($_POST['edit_receipt'])){
				if($receipt['status'] == "shipped"){
					if($page->hasLevel(1)){
						$proceed = true;
					}
				}else{
					$proceed = true;
				}
				if(isset($proceed) AND $proceed){
					$errors = array();
					$services = array();
					if(empty($_POST['customer_name'])){
						$errors[] = "You forgot to enter the customer's name!";
					}
					if(empty($_POST['customer_number'])){
						$errors[] = "You forgot to enter the customer's number!";
					}else{
						if(strlen($_POST['customer_number']) < 11){
							$errors[] = "The number you entered is shorter than 11 digits!";
						}
					}
					if(!empty($_POST['alternative_number'])){
						if(strlen($_POST['alternative_number']) < 11){
							$errors[] = "The alternative number you entered is shorter than 11 digits!";
						}
					}
					if(empty($_POST['computer_information'])){
						$errors[] = "You forgot to enter the computer information!";
					}
					if(empty($_POST['amount_paid'])){
						$errors[] = "You forgot to enter the amount paid!";
					}else{
						if($_POST['amount_paid'] > 0){
							if(!isset($_POST['payment_type'])){
								$errors[] = "You forgot to choose the payment type!";
							}
						}
					}
					if(isset($_POST['price']) AND isset($_POST['computer_quantity'])){
						$receipt_item_ids = $_POST['receipt_item_id'];
						$prices = $_POST['price'];
						$computer_quantitys = $_POST['computer_quantity'];
						$delete = isset($_POST['delete']) ? $_POST['delete'] : array();
						foreach($receipt_item_ids as $key => $receipt_item_id){
							$key_for_check = $key + 1;
							if(empty($prices[$key])){
								$errors[] = "You forgot to enter the price for one of the services!";
								$price = false;
							}else{
								$price = $prices[$key];
							}
							if(empty($computer_quantitys[$key])){
								$errors[] = "You forgot to enter the computer quantity for one of the services!";
								$computer_quantity = false;
							}else{
								$computer_quantity = $computer_quantitys[$key];
							}
							if(isset($delete[$key_for_check])){
								$delete_service = true;
							}else{
								$delete_service = false;
							}
							if($price != false AND $computer_quantity != false){
								$services[] = array("receipt_item_id" => $receipt_item_id, "price" => $price, "computer_quantity" => $computer_quantity, "delete" => $delete_service);
							}
						}
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$customer_name = $sql->smart($_POST['customer_name']);
						$customer_surname = $sql->smart($_POST['customer_surname']);
						$customer_email = $sql->smart($_POST['customer_email']);
						$customer_number = $sql->smart($_POST['customer_number']);
						$alternative_number = $sql->smart($_POST['alternative_number']);
						$computer_information = $sql->smart($_POST['computer_information']);
						if(isset($_POST['scratches_on_body'])){
							$scratches_on_body = 1;
						}else{
							$scratches_on_body = 0;
						}
						if(isset($_POST['scratches_on_screen'])){
							$scratches_on_screen = 1;
						}else{
							$scratches_on_screen = 0;
						}
						if(isset($_POST['screws_missing'])){
							$screws_missing = 1;
						}else{
							$screws_missing = 0;
						}
						if(isset($_POST['rubber_pad_missing'])){
							$rubber_pad_missing = 1;
						}else{
							$rubber_pad_missing = 0;
						}
						if(isset($_POST['keyboard_btns_missing'])){
							$keyboard_btns_missing = 1;
						}else{
							$keyboard_btns_missing = 0;
						}
						if(isset($_POST['charger_missing'])){
							$charger_missing = 1;
						}else{
							$charger_missing = 0;
						}
						if(isset($_POST['battery_missing'])){
							$battery_missing = 1;
						}else{
							$battery_missing = 0;
						}
						if(isset($_POST['checked']) OR $_POST['status'] == "shipped"){
							$checked = 1;
						}else{
							$checked = 0;
						}
						if(empty($_POST['other_charges'])){
							$other_charges = $sql->smart("0.00");
						}else{
							$other_charges = $sql->smart($_POST['other_charges']);
						}
						$discount = $sql->smart($_POST['discount']);
						$discount_reason = $sql->smart($_POST['discount_reason']);
						$note = $sql->smart($_POST['note']);
						$customer_note = $sql->smart($_POST['customer_note']);
						$amount_paid = $sql->smart($_POST['amount_paid']);
						if(isset($_POST['payment_type'])){
							$payment_type = $sql->smart($_POST['payment_type']);
						}else{
							$payment_type = $sql->smart("");
						}
						$estimated_collection = $sql->smart($_POST['estimated_collection']);
						$status = $sql->smart($_POST['status']);
						if($_POST['status'] != $receipt['status']){
							$new_status = "Status changed from <strong>" . $page->receipt_status[$receipt['status']] . "</strong> to <strong>" . $page->receipt_status[$_POST['status']] . "</strong>";
							$page->logAction("receipt", $receipt['id'], true, $new_status);
						}
						if($_POST['status'] == "shipped" AND empty($receipt['status'])){
							$date_collected = $sql->smart(date("d/m/Y"));
						}else{
							$date_collected = $sql->smart($_POST['date_collected']);
						}
						$sql->query("UPDATE `receipts` SET `customer_name` = $customer_name, `customer_surname` = $customer_surname, `customer_email` = $customer_email, `customer_number` = $customer_number, `alternative_number` = $alternative_number, `computer_information` = $computer_information, `scratches_on_body` = $scratches_on_body, `scratches_on_screen` = $scratches_on_screen, `screws_missing` = $screws_missing, `rubber_pad_missing` = $rubber_pad_missing, `keyboard_btns_missing` = $keyboard_btns_missing, `charger_missing` = $charger_missing, `battery_missing` = $battery_missing, `checked` = $checked, `other_charges` = $other_charges, `discount` = $discount, `discount_reason` = $discount_reason, `note` = $note, `customer_note` = $customer_note, `amount_paid` = $amount_paid, `payment_type` = $payment_type, `estimated_collection` = $estimated_collection, `date_collected` = $date_collected, `status` = $status WHERE `id` = $receipt_id");
						foreach($services as $service){
							$receipt_item_id = $service['receipt_item_id'];
							$price = $sql->smart($service['price']);
							$computer_quantity = $sql->smart($service['computer_quantity']);
							if($service['delete']){
								$sql->query("DELETE FROM `receipt_items` WHERE `id` = $receipt_item_id");
							}else{
								$sql->query("UPDATE `receipt_items` SET `price` = $price, `computer_quantity` = $computer_quantity WHERE `id` = $receipt_item_id");
							}
						}
						page::alert("Receipt edited successfully!", "success");
						$page->logAction("receipt", $receipt['id'], false, "receipt_updated");
						$receipt = $sql->fetch_array($sql->query("SELECT * FROM `receipts` WHERE `id` = $receipt_id"));
						$sendStatusUpdate = isset($_POST['sendStatusUpdate']) ? true : false;
						if($sendStatusUpdate === true){
							$email = url . "/email/2/" . $receipt['id'];
							?>
							<script type="text/javascript">
								window.open("<?php echo $email; ?>", "_blank");
							</script>
							<?php
						}
					}
				}
			}
			
			if(isset($_POST['add_items'])){
				if($receipt['status'] == "shipped"){
					if($page->hasLevel(1)){
						$proceed = true;
					}
				}else{
					$proceed = true;
				}
				if(isset($proceed) AND $proceed){
					$errors = array();
					$services = array();
					if(isset($_POST['service_id']) AND isset($_POST['price']) AND isset($_POST['computer_quantity'])){
						$service_ids = $_POST['service_id'];
						$prices = $_POST['price'];
						$computer_quantitys = $_POST['computer_quantity'];
						foreach($service_ids as $key => $service_id){
							if(empty($prices[$key])){
								$errors[] = "You forgot to enter the price for one of the services!";
								$price = false;
							}else{
								$price = $prices[$key];
							}
							if(empty($computer_quantitys[$key])){
								$errors[] = "You forgot to enter the computer quantity for one of the services!";
								$computer_quantity = false;
							}else{
								$computer_quantity = $computer_quantitys[$key];
							}
							if($price != false AND $computer_quantity != false){
								$services[] = array("service_id" => $service_id, "price" => $price, "computer_quantity" => $computer_quantity);
							}
						}
					}else{
						$errors[] = "No service information was set!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						foreach($services as $service){
							$service_id = $sql->smart($service['service_id']);
							$price = $sql->smart($service['price']);
							$computer_quantity = $sql->smart($service['computer_quantity']);
							$sql->query("INSERT INTO `receipt_items` (`receipt_id`, `service_id`, `price`, `computer_quantity`) VALUES ($receipt_id, $service_id, $price, $computer_quantity)");
						}
						page::alert("Receipt items added!", "success");
						$page->logAction("receipt", $receipt['id'], false, "new_services");
					}
				}
			}
			$receipt = $sql->fetch_array($sql->query("SELECT * FROM `receipts` WHERE `id` = $receipt_id"));
			$scratches_on_body = ($receipt['scratches_on_body'] == 1) ? " checked" : "";
			$scratches_on_screen = ($receipt['scratches_on_screen'] == 1) ? " checked" : "";
			$screws_missing = ($receipt['screws_missing'] == 1) ? " checked" : "";
			$rubber_pad_missing = ($receipt['rubber_pad_missing'] == 1) ? " checked" : "";
			$keyboard_btns_missing = ($receipt['keyboard_btns_missing'] == 1) ? " checked" : "";
			$charger_missing = ($receipt['charger_missing'] == 1) ? " checked" : "";
			$battery_missing = ($receipt['battery_missing'] == 1) ? " checked" : "";
			$checked = ($receipt['checked'] == 1) ? " checked" : "";
			?>
			<ul class="nav nav-tabs margin">
				<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
				<li><a href="#logs" data-toggle="tab">Logs</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="information">
					<form method="POST" class="form-horizontal">
						<fieldset>
							<legend>Customer</legend>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="customer_name" class="col-xs-4 control-label">Customer's name*</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="customer_name" placeholder="Name" value="<?=$receipt['customer_name']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="customer_number" class="col-xs-4 control-label">Customer's number*</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="customer_number" placeholder="Number" value="<?=$receipt['customer_number']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="alternative_number" class="col-xs-4 control-label">Alternative number</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="alternative_number" placeholder="Alternative number" value="<?=$receipt['alternative_number']?>">
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="customer_name" class="col-xs-4 control-label">Customer's surname</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="customer_surname" placeholder="Surname" value="<?=$receipt['customer_surname']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="customer_email" class="col-xs-4 control-label">Customer's email</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="customer_email" placeholder="Email address" value="<?=$receipt['customer_email']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="computer_information" class="col-xs-4 control-label">Computer information*</label>
									<div class="col-xs-8">
										<textarea type="text" class="form-control" name="computer_information" rows="5" placeholder="Computer information"><?=$receipt['computer_information']?></textarea>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="scratches_on_body" class="col-xs-4 control-label">Scratches on Body</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="scratches_on_body"<?=$scratches_on_body?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="scratches_on_screen" class="col-xs-4 control-label">Scratches on Screen</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="scratches_on_screen"<?=$scratches_on_screen?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="screws_missing" class="col-xs-4 control-label">Screws Missing</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="screws_missing"<?=$screws_missing?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="battery_missing" class="col-xs-4 control-label">Battery Missing</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="battery_missing"<?=$battery_missing?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="rubber_pad_missing" class="col-xs-4 control-label">Rubber Pad Missing</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="rubber_pad_missing"<?=$rubber_pad_missing?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="keyboard_btns_missing" class="col-xs-4 control-label">Keyboard Buttons Missing</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="keyboard_btns_missing"<?=$keyboard_btns_missing?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="charger_missing" class="col-xs-4 control-label">Charger Missing</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="charger_missing"<?=$charger_missing?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="checked" class="col-xs-4 control-label">Checked</label>
									<div class="col-xs-8">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="checked"<?=$checked?>>
												<span class="lbl"></span>
											</label>
										</div>
									</div>
								</div>
							</div>
							<legend>Information</legend>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="estimated_collection" class="col-xs-4 control-label">Estimated collection date</label>
									<div class="col-xs-8">
										<input type="text" class="form-control datepicker" name="estimated_collection" placeholder="Estimated collection date" value="<?=$receipt['estimated_collection']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="other_charges" class="col-xs-4 control-label">Other charges</label>
									<div class="col-xs-8">
										<input type="text" id="other_charges" class="form-control" onclick="money(this)" onblur="money(this), update_receipt_total()" name="other_charges" placeholder="Other charges" value="<?=$receipt['other_charges']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="discount" class="col-xs-4 control-label">Discount</label>
									<div class="col-xs-8">
										<input type="text" id="discount" class="form-control" onclick="money(this)" onblur="money(this), update_receipt_total(), receipt_discount_reason(this)" name="discount" placeholder="Discount" value="<?=$receipt['discount']?>">
									</div>
								</div>
								<?php
								if($receipt['discount'] > 0){
									$display_discount_reason = "block";
								}else{
									$display_discount_reason = "none";
								}
								?>
								<div class="form-group" id="discount_reason" style="display: <?=$display_discount_reason?>;">
									<label for="estimated_collection" class="col-xs-4 control-label">Reason for Discount</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="discount_reason" placeholder="Reason for Discount" value="<?=$receipt['discount_reason']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="computer_information" class="col-xs-4 control-label">Note for Customer</label>
									<div class="col-xs-8">
										<textarea type="text" class="form-control" name="customer_note" rows="5" placeholder="Note"><?=$receipt['customer_note']?></textarea>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="estimated_collection" class="col-xs-4 control-label">Date collected</label>
									<div class="col-xs-8">
										<input type="text" class="form-control datepicker" name="date_collected" placeholder="Date collected" value="<?=$receipt['date_collected']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="amount_paid" class="col-xs-4 control-label">Status</label>
									<div class="col-xs-8">
										<div class="input-group">
											<span class="input-group-addon" style="padding: 3px 12px;">
												<label>
													<input type="checkbox" name="sendStatusUpdate">
													<span class="lbl"></span>
												</label>
											</span>
											<select class="form-control" name="status" id="status" onChange="update_receipt_total()">
												<?php
												foreach($page->receipt_status as $key => $name){
													$selected = ($receipt['status'] == $key) ? " selected" : "";
													?>
													<option value="<?=$key?>"<?=$selected?>><?=$name?></option>
													<?php
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="amount_paid" class="col-xs-4 control-label">Amount paid</label>
									<div class="col-xs-8">
										<input type="text" id="amount_paid" class="form-control" onclick="money(this)" onblur="money(this), update_receipt_total(), receipt_payment_type(this)" name="amount_paid" placeholder="Amount paid" value="<?=$receipt['amount_paid']?>">
									</div>
								</div>
								<?php
								if($receipt['amount_paid'] > 0){
									$display_payment_type = "block";
								}else{
									$display_payment_type = "none";
								}
								$cash = ($receipt['payment_type'] == "cash") ? " selected" : "";
								$card = ($receipt['payment_type'] == "card") ? " selected" : "";
								$cheq = ($receipt['payment_type'] == "cheq") ? " selected" : "";
								?>
								<div class="form-group" id="payment_type" style="display: <?=$display_payment_type?>;">
									<label for="payment_type" class="col-xs-4 control-label">Payment type</label>
									<div class="col-xs-8">
										<select class="form-control" name="payment_type">
											<option selected disabled>Select Payment type</option>
											<option value="cash"<?=$cash?>>Cash</option>
											<option value="card"<?=$card?>>Card</option>
											<option value="cheq"<?=$cheq?>>Cheque</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label">Total</label>
									<div class="col-xs-8">
										<input type="text" id="total" class="form-control" value="0.00" readonly>
									</div>
								</div>
								<div class="form-group">
									<label for="payment_due" class="col-xs-4 control-label">Payment due</label>
									<div class="col-xs-8">
										<input type="text" id="payment_due" class="form-control" value="0.00" readonly>
									</div>
								</div>
							</div>
							<div class="col-xs-10">
								<div class="form-group">
									<label for="note" class="col-xs-4 control-label">Note</label>
									<div class="col-xs-8">
										<textarea type="text" class="form-control" name="note" rows="5" placeholder="Note"><?=$receipt['note']?></textarea>
									</div>
								</div>
							</div>
							<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
								<thead>
									<tr>
										<th>#</th>
										<th>Service</th>
										<th>Price</th>
										<th>Computer quantity</th>
										<th>Total</th>
										<th>Delete</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$receipt_items = $sql->query("SELECT * FROM `receipt_items` WHERE `receipt_id` = $receipt_id");
									$i = 1;
									while($receipt_item = $sql->fetch_array($receipt_items)){
										?>
										<tr id="service_count">
											<td id="item_count"><?=$i?><input type="hidden" name="receipt_item_id[]" value="<?=$receipt_item['id']?>"></td>
											<td><?=page::getServiceInfo($receipt_item['service_id'], "title")?></td>
											<td><input class="form-control" type="text" id="service_cost_<?=$i?>" onclick="money(this)" onchange="update_receipt_total()" onblur="money(this), service_total(<?=$i?>), update_receipt_total()" name="price[]" type="text" placeholder="Price" value="<?=$receipt_item['price']?>"></td>
											<td><input class="form-control" type="text" id="service_quantity_<?=$i?>" onblur="service_total(<?=$i?>), update_receipt_total()" name="computer_quantity[]" placeholder="Quantity" value="<?=$receipt_item['computer_quantity']?>"></td>
											<td><input class="form-control service_total" type="text" id="service_total_<?=$i?>" value="0.00" readonly></td>
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
						</fieldset>
						<?php
						if($receipt['status'] == "shipped"){
							if($page->hasLevel(1)){
								?>
								<div class="form-button">
									<button type="submit" class="btn btn-primary btn-sm" name="edit_receipt">Edit receipt</button>
								</div>
								<?php
							}
						}else{
							?>
							<div class="form-button">
								<button type="submit" class="btn btn-primary btn-sm" name="edit_receipt">Edit receipt</button>
							</div>
							<?php
						}
						?>
					</form>
					<form method="POST">
						<?php
						page::alert("If a service will not be selected - the whole row will not be inserted!", "info");
						?>
						<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Service</th>
									<th>Price</th>
									<th>Computer quantity</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<?php
								for($i = 1; $i <= 5; $i++){
									?>
									<tr>
										<td id="item_count"><?=$i?></td>
										<td>
											<select class="form-control" onchange="new_get_service_price(<?=$i?>)" id="new_select_service_<?=$i?>" name="service_id[]">
												<option selected disabled>Choose service</option>
												<?php
												$services = $sql->query("SELECT * FROM `services` ORDER BY `title` ASC");
												while($service = $sql->fetch_array($services)){
													?>
													<option value="<?=$service['id']?>"><?=$service['title']?></option>
													<?php
												}
												?>
											</select>
										</td>
										<td><input class="form-control" type="text" id="new_service_cost_<?=$i?>" onclick="money(this)" onchange="update_receipt_total()" onblur="money(this), new_service_total(<?=$i?>), update_receipt_total()" name="price[]" type="text" placeholder="Price" value="0.00"></td>
										<td><input class="form-control" type="text" id="new_service_quantity_<?=$i?>" onblur="new_service_total(<?=$i?>), update_receipt_total()" name="computer_quantity[]" placeholder="Quantity" value="0"></td>
										<td><input class="form-control new_service_total" type="text" id="new_service_total_<?=$i?>" value="0.00" readonly></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
						<?php
						if($receipt['status'] == "shipped"){
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
				</div>
				<div class="tab-pane fade" id="logs">
					<legend>Logs</legend>
					<table class="table table-bordered">
						<thead>
							<th>#</th>
							<th>Action</th>
							<th>Action time</th>
							<th>Action by</th>
						</thead>
						<tbody>
							<?php
							$log_i = 1;
							$logs = $sql->query("SELECT * FROM `logs` WHERE `parent_type` = 'receipt' AND `parent_id` = $receipt_id ORDER BY `id` ASC");
							while($log = $sql->fetch_array($logs)):
								if(empty($log['action'])){
									$action = $log['custom_action'];
								}else{
									$action = $page->actions[$log['action']];
								}
								?>
								<tr>
									<td><?php echo $log_i; ?></td>
									<td><?php echo $action; ?></td>
									<td><?php echo date("d/m/Y H:i", $log['time']); ?></td>
									<td><?php echo page::getUserInfo($log['user_id'], "name") . " " . page::getUserInfo($log['user_id'], "surname"); ?></td>
								</tr>
								<?php
								$log_i++;
								endwhile;
								?>
							</tbody>
						</table>
						<legend>Email Logs</legend>
						<table class="table table-bordered">
							<thead>
								<th>#</th>
								<th>Subject</th>
								<th>Sent to</th>
								<th>Sent on</th>
								<th>Sent by</th>
								<th>Sent</th>
							</thead>
							<tbody>
								<?php
								$elog_i = 1;
								$email_logs = $sql->query("SELECT * FROM `email_logs` WHERE `parent_type` = 'receipt' AND `parent_id` = $receipt_id ORDER BY `id` ASC");
								while($email_log = $sql->fetch_array($email_logs)):
									$sent = ($email_log['sent'] == 1) ? "ok" : "remove";
								?>
								<tr>
									<td><?php echo $elog_i; ?></td>
									<td><?php echo $email_log['subject']; ?></td>
									<td><?php echo $email_log['sent_to']; ?></td>
									<td><?php echo date("d/m/Y H:i", $email_log['time']); ?></td>
									<td><?php echo page::getUserInfo($email_log['sent_by_id'], "name") . " " . page::getUserInfo($email_log['sent_by_id'], "surname"); ?></td>
									<td><span class="glyphicon glyphicon-<?=$sent?>"></span></td>
								</tr>
								<?php
								$elog_i++;
								endwhile;
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?php
			}
		}else{
			page::alert("No ID was specified!", "danger");
		}
	}elseif($action == "invoice"){
		if(isset($pageInfo[3])){
			$receipt_id = (int)$pageInfo[3];
			$check_existance = $sql->num_rows($sql->query("SELECT `id` FROM `receipts` WHERE `id` = $receipt_id"));
			if($check_existance == 0){
				page::alert("No receipts were found with that ID!", "danger");
				header("refresh:2;url=" . url . "/receipts");
			}else{
				$receipt = $sql->fetch_array($sql->query("SELECT * FROM `receipts` WHERE `id` = $receipt_id"));
				$sql->query("UPDATE `receipts` SET `invoiced` = 1 WHERE `id` = $receipt_id");
				$page->logAction("receipt", $receipt_id, false, "order_created");
				$created = $sql->smart(time());
				$created_by_id = $sql->smart($global_user_id);
				$order_type = $sql->smart("parts");
				$first_name = $sql->smart($receipt['customer_name']);
				$last_name = $sql->smart($receipt['customer_surname']);
				$email = $sql->smart($receipt['customer_email']);
				$mobile_number = $sql->smart($receipt['customer_number']);
				$home_number = $sql->smart($receipt['alternative_number']);
				$priority = $sql->smart("normal");
				$invoice_date = $sql->smart(date("d/m/Y"));
				if(empty($receipt['estimated_collection'])){
					$shipment_date = date("d/m/Y");
				}else{
					$shipment_date = $receipt['estimated_collection'];
				}
				$shipment_date = $sql->smart(strtotime(str_replace("/", "-", $shipment_date)));
				$committed = 0;
				$order_status = $sql->smart("open_order");
				$delivery_method = $sql->smart("collection");
				$saturday = 0;
				$number_of_parcels = 0;
				$warranty = 8;
				$shipping_exc_vat = $sql->smart("0.00");
				$upgrades_exc_vat = $sql->smart("0.00");
				$other_exc_vat = $sql->smart(number_format((float)(($receipt['other_charges'] - $receipt['discount']) / 1.2), 2, ".", ""));
				$sql->query("INSERT INTO `orders` (`created`, `created_by_id`, `vat`, `order_type`, `first_name`, `last_name`, `email`, `mobile_number`, `home_number`, `priority`, `invoice_date`, `shipment_date`, `committed`, `order_status`, `build_service`, `delivery_method`, `saturday`, `number_of_parcels`, `warranty`, `shipping_exc_vat`, `upgrades_exc_vat`, `other_exc_vat`) VALUES ($created, $created_by_id, '20.00', $order_type, $first_name, $last_name, $email, $mobile_number, $home_number, $priority, $invoice_date, $shipment_date, $committed, $order_status, 'not_applicable', $delivery_method, $saturday, $number_of_parcels, $warranty, $shipping_exc_vat, $upgrades_exc_vat, $other_exc_vat)");
				$new_order = $sql->fetch_array($sql->query("SELECT `order_id` FROM `orders` ORDER BY `order_id` DESC LIMIT 1"));
				$new_order_id = $new_order['order_id'];
				$sql->query("INSERT INTO `order_specs` (`order_id`, `type`) VALUES ($new_order_id, 'aservices')");
				$new_spec = $sql->fetch_array($sql->query("SELECT `spec_id` FROM `order_specs` ORDER BY `spec_id` DESC LIMIT 1"));
				$new_spec_id = $sql->smart($new_spec['spec_id']);;
				$receipt_items = $sql->query("SELECT * FROM `receipt_items` WHERE `receipt_id` = $receipt_id");
				while($receipt_item = $sql->fetch_array($receipt_items)){
					$service_id = $sql->smart($receipt_item['service_id']);
					$quantity = $sql->smart($receipt_item['computer_quantity']);
					$cost_exc_vat = $sql->smart(number_format((float)($receipt_item['price'] / 1.2), 2, ".", ""));
					$sql->query("INSERT INTO `order_items` (`order_spec_id`, `service_id`, `quantity`, `order_id`, `cost_exc_vat`) VALUES ($new_spec_id, $service_id, $quantity, $new_order_id, $cost_exc_vat)") or die(mysql_error());
				}
				$note = $sql->smart($receipt['note']);
				$sql->query("INSERT INTO `order_notes` (`order_id`, `note`, `date`, `added_by_id`) VALUES ($new_order_id, $note, $created, $created_by_id)");
				$new_order = url . "/orders/edit/" . $new_order_id;
				$page->logAction("order", $new_order_id, false, "order_created");
				header("Location: $new_order");
			}
		}else{
			page::alert("No ID was specified!", "danger");
		}
	}else{
		if(isset($_POST['search']) AND isset($_POST['query']) AND !empty($_POST['query']) AND strlen($_POST['query']) > 1){
			header("Location: " . url . "/receipts/search/" . $_POST['query'] . "/");
		}
		if(isset($_POST['update'])){
			if(isset($_POST['receipt_selected']) AND isset($_POST['receipt_id'])){
				$receipt_selected = $_POST['receipt_selected'];
				$receipt_ids = $_POST['receipt_id'];
				foreach($receipt_selected as $key => $selected){
					$receipt_id = $receipt_ids[$key];
					$get_status = $sql->fetch_array($sql->query("SELECT `status` FROM `receipts` WHERE `id` = $receipt_id"));
					if($get_status['status'] == "shipped"){
						if($page->hasLevel(1)){
							$proceed = true;
						}
					}else{
						$proceed = true;
					}
					if(isset($proceed) AND $proceed){
						$status = $sql->smart($_POST['status']);
						$sql->query("UPDATE `receipts` SET `status` = $status WHERE `id` = $receipt_id");
					}
				}
				if(count($receipt_selected) == 1){
					page::alert("The selected receipt was updated!", "success");
				}else{
					page::alert("The selected receipts were updated!", "success");
				}
			}else{
				page::alert("No receipts were selected!", "danger");
			}
		}
		if(isset($pageInfo[2]) AND !empty($pageInfo[3]) AND $pageInfo[2] == "search"){
			$query_r = urldecode($pageInfo[3]);
		}else{
			$query_r = "";
		}
		?>
		<form class="form-inline bottom-space pull-left" role="form" method="POST">
			<div class="form-group">
				<label class="sr-only" for="from">Search receipts</label>
				<input type="text" name="query" class="form-control no-space" placeholder="Search receipts..." value="<?php echo $query_r; ?>">
			</div>
			<button type="submit" class="btn btn-default" name="search">Search</button>
		</form>
		<form class="form-inline bottom-space pull-right" role="form" method="POST">
			<div class="form-group">
				<select class="form-control no-space" name="view_status">
					<option value="all">All statuses</option>
					<?php
					foreach($page->receipt_status as $key => $name){
						$selected = (isset($_POST['view_status']) AND $_POST['view_status'] == $key) ? " selected" : "";
						?>
						<option value="<?=$key?>"<?=$selected?>><?=$name?></option>
						<?php
					}
					?>
				</select>
			</div>
			<button type="submit" class="btn btn-default no-space" name="view">View selected</button>
		</form>
		<form class="form-inline" role="form" method="POST">
			<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>
							<input type="checkbox" id="checkAll">
							<span class="lbl"></span>
						</th>
						<th>ID</th>
						<th width="150">Name</th>
						<th>Number</th>
						<th width="200">Computer</th>
						<th>Amount</th>
						<th>Amount paid</th>
						<th width="175">Status</th>
						<th>Created</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(isset($pageInfo[2]) AND !empty($pageInfo[3]) AND $pageInfo[2] == "search"){
						$query = $pageInfo[3];
						$query = $sql->smart("%$query%");
						$receipts_count = $sql->num_rows($sql->query("SELECT * FROM `receipts` WHERE `id` LIKE $query OR `customer_number` LIKE $query OR `alternative_number` LIKE $query OR `customer_name` LIKE $query OR `customer_surname` LIKE $query OR `computer_information` LIKE $query OR `customer_email` LIKE $query"));
						if($receipts_count > 0){
							list($pagertop, $pagerbottom, $limit) = page::pagination(10, $receipts_count, url . "/receipts/search/" . $pageInfo[3] . "/page/", 5);
							$receipts = $sql->query("SELECT * FROM `receipts` WHERE `id` LIKE $query OR `customer_number` LIKE $query OR `alternative_number` LIKE $query OR `customer_name` LIKE $query OR `customer_surname` LIKE $query OR `computer_information` LIKE $query OR `customer_email` LIKE $query ORDER BY `id` DESC $limit");
						}else{
							$receipts = "";
						}
					}elseif(isset($_POST['view']) AND isset($_POST['view_status'])){
						if($_POST['view_status'] == "all"){
							$receipts_count = $sql->num_rows($sql->query("SELECT * FROM `receipts`"));
							if($receipts_count > 0){
								$receipts = $sql->query("SELECT * FROM `receipts` ORDER BY `id` DESC");
							}else{
								$receipts = "";
							}
						}else{
							$status = $sql->smart($_POST['view_status']);
							$receipts_count = $sql->num_rows($sql->query("SELECT * FROM `receipts` WHERE `status` = $status"));
							if($receipts_count > 0){
								$receipts = $sql->query("SELECT * FROM `receipts` WHERE `status` = $status ORDER BY `id` DESC");
							}else{
								$receipts = "";
							}
						}
						$pager = false;
					}else{
						$receipts_count = $sql->num_rows($sql->query("SELECT `id` FROM `receipts`"));
						list($pagertop, $pagerbottom, $limit) = page::pagination(10, $receipts_count, url . "/receipts/page/", 3);
						$receipts = $sql->query("SELECT * FROM `receipts` ORDER BY `id` DESC $limit");
					}
					$i = 1;
					while($receipt = $sql->fetch_array($receipts)){
						$receipt_id = $receipt['id'];
						$service_sum = $sql->fetch_array($sql->query("SELECT SUM(`price` * `computer_quantity`) AS `total_sum` FROM `receipt_items` WHERE `receipt_id` = $receipt_id"));
						$total_sum = $receipt['other_charges'] + $service_sum['total_sum'] - $receipt['discount'];
						if($receipt['status'] == "cancelled"){
							$total = "0.00";
						}else{
							$total = number_format((float)$total_sum, 2, ".", "");
						}
						$amount_paid = $receipt['amount_paid'];
						if($total > $amount_paid){
							$payment_status = "danger";
						}elseif($total < $amount_paid){
							$payment_status = "warning";
						}else{
							$payment_status = "success";
						}
						$receipt_selected = (isset($_POST['receipt_selected'][$i])) ? " checked" : "";
						?>
						<tr>
							<?php
							if($receipt['status'] == "shipped"){
								if($page->hasLevel(1)){
									?>
									<td>
										<input type="checkbox" value="<?=$receipt['id']?>" name="receipt_selected[<?=$i?>]"<?=$receipt_selected?>>
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
									<input type="checkbox" value="<?=$receipt['id']?>" name="receipt_selected[<?=$i?>]"<?=$receipt_selected?>>
									<span class="lbl"></span>
								</td>
								<?php
							}
							?>
							<td><b><?=$receipt['id']?><input type="hidden" name="receipt_id[<?=$i?>]" value="<?=$receipt['id']?>"></b></td>
							<td><?=$receipt['customer_name']?> <?=$receipt['customer_surname']?></td>
							<td><?=$receipt['customer_number']?></td>
							<td><?=page::limit($receipt['computer_information'], 25)?></td>
							<td>£<?=$total?></td>
							<td class="<?=$payment_status?>">£<?=$receipt['amount_paid']?></td>
							<td><?=$page->receipt_status[$receipt['status']]?></td>
							<td><?=date("d/m/Y", $receipt['added'])?></td>
							<td>
								<a class="confirm" href="<?=url?>/receipts/del/<?=$receipt['id']?>"><button class="btn btn-danger btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-remove" style="opacity:0.4;"></span> Delete</button></a>
								<a href="<?=url?>/receipts/edit/<?=$receipt['id']?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
								<a href="<?=url?>/receipt/<?=$receipt['id']?>" target="_blank"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-file" style="opacity:0.4;"></span> View</button></a>
								<a class="confirm" href="<?=url?>/receipts/invoice/<?=$receipt['id']?>"><button class="btn btn-success btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-file" style="opacity:0.4;"></span> Invoice</button></a>
							</td>
						</tr>
						<?php
						$i++;
					}
					?>
					<tr>
						<td colspan="4">
							<div class="form-group" style="margin-right: 20px;">
								<label class="sr-only no-space" for="status">Update Selected Receipts</label>
								<select class="form-control" name="status" id="status" style="width: 250px;">
									<?php
									foreach($page->receipt_status as $key => $name){
										$selected = (isset($_POST['status']) AND $_POST['status'] == $key) ? " selected" : "";
										?>
										<option value="<?=$key?>"<?=$selected?>><?=$name?></option>
										<?php
									}
									?>
								</select>
							</div>
							<button type="submit" class="btn btn-default" name="update" style="margin-bottom: 11px;">Update</button>
						</td>
						<td colspan="3">
							<div class="form-group">
								<select class="form-control" id="category" style="width: 300px;" onChange="getTemplateList('receipt')">
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
						<td colspan="2">
							<button type="button" class="btn btn-default" style="margin-bottom: 11px;" onClick="duplicateEntry('receipt')">Duplicate Selected</button>
						</td>
						<td colspan="1">
							<div class="form-group">
								<select class="form-control" id="sms_template" style="width: 300px;">
									<option disabled selected>Select Template</option>
									<?php
									$templates = $sql->query("SELECT * FROM `sms_templates` WHERE `type` = 'receipt' ORDER BY `description` ASC");
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
					</tr>
					<tr>
						<td class="danger"></td>
						<td colspan="11">Amount paid is smaller than the actual amount</td>
					</tr>
					<tr>
						<td class="success"></td>
						<td colspan="11">Amount paid fully</td>
					</tr>
					<tr>
						<td class="warning"></td>
						<td colspan="11">Amount paid exceeds the actual amount</td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
		if($receipts_count > 0 AND !isset($pager)){
			echo $pagerbottom;
		}
	}
	?>