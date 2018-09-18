<?php
$page = new page("Manage Orders", 3);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "list";
}

if($action == "add"){
	$page->changeTitle("Add order");
	if(isset($_GET['type']) OR (isset($_GET['duplicate']) AND !empty($_GET['duplicate']))){
		if(isset($_POST['add_order'])){
			$errors = array();
			if(empty($_POST['first_name'])){
				$errors[] = "You forgot to enter the first name!";
			}
			if(empty($_POST['last_name'])){
				$errors[] = "You forgot to enter the last name!";
			}
			if(empty($_POST['invoice_date'])){
				$errors[] = "You forgot to enter the invoice date!";
			}
			if(empty($_POST['shipment_date'])){
				$errors[] = "You forgot to enter the shipment date!";
			}
			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$created = $sql->smart(time());
				$created_by_id = $sql->smart($global_user_id);
				$vat = $sql->smart($_POST['vat']);
				$order_type = $sql->smart($_GET['type']);
				$first_name = $sql->smart($_POST['first_name']);
				$last_name = $sql->smart($_POST['last_name']);
				$company_name = $sql->smart($_POST['company_name']);
				$email = $sql->smart($_POST['email']);
				$mobile_number = $sql->smart($_POST['mobile_number']);
				$home_number = $sql->smart($_POST['home_number']);
				$work_number = $sql->smart($_POST['work_number']);
				$order_ref = $sql->smart($_POST['order_ref']);
				$billing_line1 = $sql->smart($_POST['billing_line1']);
				$billing_line2 = $sql->smart($_POST['billing_line2']);
				$billing_line3 = $sql->smart($_POST['billing_line3']);
				$billing_line4 = $sql->smart($_POST['billing_line4']);
				$billing_postcode = $sql->smart($_POST['billing_postcode']);
				$shipping_line1 = $sql->smart($_POST['shipping_line1']);
				$shipping_line2 = $sql->smart($_POST['shipping_line2']);
				$shipping_line3 = $sql->smart($_POST['shipping_line3']);
				$shipping_line4 = $sql->smart($_POST['shipping_line4']);
				$shipping_postcode = $sql->smart($_POST['shipping_postcode']);
				$priority = $sql->smart($_POST['priority']);
				$invoice_date = $sql->smart($_POST['invoice_date']);
				$shipment_date = $sql->smart(strtotime(str_replace("/", "-", $_POST['shipment_date'])));
				if(isset($_POST['committed'])){
					$committed = 1;
				}else{
					$committed = 0;
				}
				$order_status = $sql->smart($_POST['order_status']);
				if(isset($_POST['build_service'])){
					$build_service = $sql->smart($_POST['build_service']);
				}else{
					$build_service = $sql->smart("");
				}
				if(isset($_POST['delivery_method'])){
					$delivery_method = $sql->smart($_POST['delivery_method']);
				}else{
					$delivery_method = $sql->smart("");
				}
				$delivery_instructions = $sql->smart($_POST['delivery_instructions']);
				if(isset($_POST['saturday'])){
					$saturday = 1;
				}else{
					$saturday = 0;
				}
				$number_of_parcels = $sql->smart($_POST['number_of_parcels']);
				$warranty = $sql->smart($_POST['warranty']);
				$shipping_exc_vat = $sql->smart($_POST['shipping_exc_vat']);
				$upgrades_exc_vat = $sql->smart($_POST['upgrades_exc_vat']);
				$other_exc_vat = $sql->smart($_POST['other_exc_vat']);
				$sql->query("INSERT INTO `orders` (
					`created`,
					`created_by_id`,
					`vat`,
					`order_type`,
					`first_name`,
					`last_name`,
					`company_name`,
					`email`,
					`mobile_number`,
					`home_number`,
					`work_number`,
					`order_ref`,
					`billing_line1`,
					`billing_line2`,
					`billing_line3`,
					`billing_line4`,
					`billing_postcode`,
					`shipping_line1`,
					`shipping_line2`,
					`shipping_line3`,
					`shipping_line4`,
					`shipping_postcode`,
					`priority`,
					`invoice_date`,
					`shipment_date`,
					`committed`,
					`order_status`,
					`build_service`,
					`delivery_method`,
					`delivery_instructions`,
					`saturday`,
					`number_of_parcels`,
					`warranty`,
					`shipping_exc_vat`,
					`upgrades_exc_vat`,
					`other_exc_vat`
					) VALUES (
					$created,
					$created_by_id,
					$vat,
					$order_type,
					$first_name,
					$last_name,
					$company_name,
					$email,
					$mobile_number,
					$home_number,
					$work_number,
					$order_ref,
					$billing_line1,
					$billing_line2,
					$billing_line3,
					$billing_line4,
					$billing_postcode,
					$shipping_line1,
					$shipping_line2,
					$shipping_line3,
					$shipping_line4,
					$shipping_postcode,
					$priority,
					$invoice_date,
					$shipment_date,
					$committed,
					$order_status,
					$build_service,
					$delivery_method,
					$delivery_instructions,
					$saturday,
					$number_of_parcels,
					$warranty,
					$shipping_exc_vat,
					$upgrades_exc_vat,
					$other_exc_vat
					)");
$new_order = $sql->fetch_array($sql->query("SELECT `order_id` FROM `orders` ORDER BY `order_id` DESC LIMIT 1"));
$new_order_id = $new_order['order_id'];
if($_GET['type'] == "pc"){
	$sql->query("INSERT INTO `order_specs` (`order_id`, `type`, `cost_exc_vat`, `quantity`) VALUES ($new_order_id, 'pc', '0.00', '1')");
}
$new_order = url . "/orders/edit/" . $new_order_id;
$page->logAction("order", $new_order_id, false, "order_created");
header("Location: $new_order");
}
}
if(isset($_GET['duplicate']) AND !empty($_GET['duplicate'])){
	$duplicate_order_id = (int)$_GET['duplicate'];
	$find_duplicate = $sql->num_rows($sql->query("SELECT `order_id` FROM `orders` WHERE `order_id` = $duplicate_order_id"));
	if($find_duplicate == 0){
		$duplicate = false;
	}else{
		$duplicate = $sql->fetch_array($sql->query("SELECT * FROM `orders` WHERE `order_id` = $duplicate_order_id"));
	}
}else{
	$duplicate = false;
}
?>
<ul class="nav nav-tabs margin">
	<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane fade in active" id="information">
		<form method="POST" class="form-horizontal">
			<fieldset>
				<legend>Customer Information</legend>
				<div class="col-xs-6">
					<div class="form-group">
						<label for="first_name" class="col-xs-4 control-label">First name*</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="first_name" placeholder="First name" value="<?php echo ($duplicate != false) ? $duplicate['first_name'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="company_name" class="col-xs-4 control-label">Company name</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="company_name" placeholder="Company name" value="<?php echo ($duplicate != false) ? $duplicate['company_name'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Mobile number</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="mobile_number" placeholder="Mobile number" value="<?php echo ($duplicate != false) ? $duplicate['mobile_number'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Work number</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="work_number" placeholder="Work number" value="<?php echo ($duplicate != false) ? $duplicate['work_number'] : ''; ?>">
						</div>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="form-group">
						<label for="last_name" class="col-xs-4 control-label">Last name*</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="last_name" placeholder="Last name" value="<?php echo ($duplicate != false) ? $duplicate['last_name'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-xs-4 control-label">Email</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="email" placeholder="Email" value="<?php echo ($duplicate != false) ? $duplicate['email'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="home_number" class="col-xs-4 control-label">Home number</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="home_number" placeholder="Home number" value="<?php echo ($duplicate != false) ? $duplicate['home_number'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="order_ref" class="col-xs-4 control-label">Order reference</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="order_ref" placeholder="Order reference">
						</div>
					</div>
				</div>
				<legend>Addresses</legend>
				<div class="col-xs-6">
					<legend>Billing Address</legend>
					<div class="form-group">
						<label for="first_name" class="col-xs-4 control-label">Search Postcode</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" onBlur="findAddress(this, 'billing')" placeholder="Find Postcode">
							<select class="form-control" id="billing_addressSelection" onChange="retrieveAddress(this, 'billing')">
								<option selected disabled>Enter Postcode for Listing</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="first_name" class="col-xs-4 control-label">Line 1</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="billing_line1" id="billing_line1" placeholder="Line 1" value="<?php echo ($duplicate != false) ? $duplicate['billing_line1'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="company_name" class="col-xs-4 control-label">Line 2</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="billing_line2" id="billing_line2" placeholder="Line 2" value="<?php echo ($duplicate != false) ? $duplicate['billing_line2'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Line 3</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="billing_line3" id="billing_line3" placeholder="Line 3" value="<?php echo ($duplicate != false) ? $duplicate['billing_line3'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Line 4</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="billing_line4" id="billing_line4" placeholder="Line 4" value="<?php echo ($duplicate != false) ? $duplicate['billing_line4'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Postcode</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="billing_postcode" id="billing_postcode" placeholder="Postcode" value="<?php echo ($duplicate != false) ? $duplicate['billing_postcode'] : ''; ?>">
						</div>
					</div>
				</div>
				<div class="col-xs-6">
					<legend>Shipping Address</legend>
					<div class="form-group">
						<label for="first_name" class="col-xs-4 control-label">Search Postcode</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" onBlur="findAddress(this, 'shipping')" placeholder="Find Postcode">
							<select class="form-control" id="shipping_addressSelection" onChange="retrieveAddress(this, 'shipping')">
								<option selected disabled>Enter Postcode for Listing</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="first_name" class="col-xs-4 control-label">Line 1</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="shipping_line1" id="shipping_line1" placeholder="Line 1" value="<?php echo ($duplicate != false) ? $duplicate['shipping_line1'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="company_name" class="col-xs-4 control-label">Line 2</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="shipping_line2" id="shipping_line2" placeholder="Line 2" value="<?php echo ($duplicate != false) ? $duplicate['shipping_line2'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Line 3</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="shipping_line3" id="shipping_line3" placeholder="Line 3" value="<?php echo ($duplicate != false) ? $duplicate['shipping_line3'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Line 4</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="shipping_line4" id="shipping_line4" placeholder="Line 4" value="<?php echo ($duplicate != false) ? $duplicate['shipping_line4'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="mobile_number" class="col-xs-4 control-label">Postcode</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="shipping_postcode" id="shipping_postcode" placeholder="Postcode" value="<?php echo ($duplicate != false) ? $duplicate['shipping_postcode'] : ''; ?>">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<div class="control-group">
								<div class="controls">
									<label>
										<input type="checkbox" id="same_address" onChange="sameAddress()">
										<span class="lbl"> Same as Billing Address</span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<legend>Order & Delivery</legend>
				<div class="col-xs-6">
					<legend>Order Information</legend>
					<div class="form-group">
						<label for="priority" class="col-xs-4 control-label">Priority</label>
						<div class="col-xs-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary active">
									<input type="radio" name="priority" id="option1" autocomplete="off" value="normal" checked> Normal
								</label>
								<label class="btn btn-primary">
									<input type="radio" name="priority" id="option2" autocomplete="off" value="urgent"> Urgent
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="build_service" class="col-xs-4 control-label">Build Service</label>
						<div class="col-xs-8">
							<select class="form-control" name="build_service">
								<option disabled selected>Select build service</option>
								<?php
								foreach($page->build_service as $key => $name){
									?>
									<option value="<?=$key?>"><?=$name?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="committed" class="col-xs-4 control-label">Committed</label>
						<div class="col-xs-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									<input type="checkbox" name="committed" autocomplete="off"> Committed
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="invoice_date" class="col-xs-4 control-label">Invoice Date*</label>
						<div class="col-xs-8">
							<input type="text" class="form-control datepicker" name="invoice_date" placeholder="Invoice Date" value="<?=date("d/m/Y")?>">
						</div>
					</div>
					<div class="form-group">
						<label for="shipment_date" class="col-xs-4 control-label">Shipment Date</label>
						<div class="col-xs-8">
							<input type="text" class="form-control datepicker" id="shipment_date" name="shipment_date" placeholder="Shipment Date" value="<?=date("d/m/Y")?>">
						</div>
					</div>
					<div class="form-group">
						<label for="order_status" class="col-xs-4 control-label">Order Status</label>
						<div class="col-xs-8">
							<select class="form-control" name="order_status">
								<?php
								foreach($page->order_status as $key => $name){
									?>
									<option value="<?=$key?>"><?=$name?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-xs-6">
					<legend>Delivery Information</legend>
					<div class="form-group">
						<label for="delivery_method" class="col-xs-4 control-label">Delivery method</label>
						<div class="col-xs-8">
							<select class="form-control" name="delivery_method" id="delivery_method">
								<option disabled selected>Select delivery method</option>
								<?php
								$delivery_methods = $sql->query("SELECT * FROM `delivery_methods` WHERE `orders` = 1 ORDER BY `delivery_name` ASC");
								while($delivery_method = $sql->fetch_array($delivery_methods)){
									?>
									<option value="<?=$delivery_method['delivery_key']?>"><?=$delivery_method['delivery_name']?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="delivery_instructions" class="col-xs-4 control-label">Delivery Instructions</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="delivery_instructions" placeholder="Delivery Instructions">
						</div>
					</div>
					<div class="form-group">
						<label for="saturday" class="col-xs-4 control-label">Saturday</label>
						<div class="col-xs-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									<input type="checkbox" id="saturday" name="saturday" autocomplete="off"> Delivery on Saturday?
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Set Delivery</label>
						<div class="col-xs-8">
							<button type="button" class="btn btn-default" onClick="estDelDate()">Set</button>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Delivery Date</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" id="delivery_date" placeholder="Delivery Date" readonly>
						</div>
					</div>
					<div class="form-group">
						<label for="number_of_parcels" class="col-xs-4 control-label">Number of Parcels</label>
						<div class="col-xs-8">
							<input type="number" class="form-control" name="number_of_parcels" placeholder="Number of Parcels" value="0" min="0">
						</div>
					</div>
				</div>
				<legend>Warranty Information</legend>
				<div class="form-group">
					<label for="warranty" class="col-xs-2 control-label">Warranty Selection</label>
					<div class="col-xs-10">
						<select class="form-control" name="warranty">
							<?php $warranty_options = $sql->query("SELECT * FROM `warranty_options` ORDER BY `id` ASC"); ?>
							<?php while($warranty_option = $sql->fetch_array($warranty_options)): ?>
								<option value="<?=$warranty_option['id']?>" <?php echo ($warranty_option['length'] == "na") ? " selected" : ""; ?>><?=$warranty_option['description']?></option>
							<?php endwhile; ?>
						</select>
					</div>
				</div>
				<legend>Pricing Information</legend>
				<div class="form-group">
					<label for="vat" class="col-xs-2 control-label">VAT</label>
					<div class="col-xs-3">
						<select class="form-control" name="vat" id="vat" onChange="updateOrderTotal('exc')">
							<option value="20.00">20.00%</option>
							<option value="0.00">0.00%</option>
						</select>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="form-group">
						<label for="shipping_exc_vat" class="col-xs-4 control-label">Shipping exc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="shipping_exc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('exc')" name="shipping_exc_vat" placeholder="Shipping exc. VAT" value="0.00">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="upgrades_exc_vat" class="col-xs-4 control-label">Upgrades exc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="upgrades_exc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('exc')" name="upgrades_exc_vat" placeholder="Upgrades exc. VAT" value="0.00">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="surcharge_exc_vat" class="col-xs-4 control-label">Other Charges exc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="other_exc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('exc')" name="other_exc_vat" placeholder="Other Charges exc. VAT" value="0.00">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Total exc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="total_exc_vat" class="form-control" value="0.00" readonly>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="form-group">
						<label class="col-xs-4 control-label">Shipping inc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="shipping_inc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('inc')" placeholder="Shipping inc. VAT" value="0.00">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Upgrades inc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="upgrades_inc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('inc')" placeholder="Upgrades inc. VAT" value="0.00">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Other Charges inc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="other_inc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('inc')" placeholder="Other Charges inc. VAT" value="0.00">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Total inc. VAT</label>
						<div class="col-xs-8 form-inline">
							<div class="form-group col-xs-6">
								<input type="text" id="total_inc_vat" class="form-control" value="0.00" readonly>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="add_order">Add order</button>
			</div>
		</form>
	</div>
</div>
<?php }else{ ?>
<form method="GET" action="<?=url?>/orders/add/" class="form-horizontal">
	<div style="width: 300px; margin: 0 auto;">
		<div class="control-group">
			<label class="control-label">Select Order Type:</label>
			<div class="controls">
				<label class="radio">
					<input type="radio" name="type" value="pc" checked>
					<span class="lbl"> PC Sale</span>
				</label>
				<label class="radio">
					<input type="radio" name="type" value="parts">
					<span class="lbl"> Services & Parts Sale</span>
				</label>
			</div>
		</div>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm">Proceed</button>
		</div>
	</div>
</form>
<?php } ?>
<?php }elseif($action == "edit"){
	$page->changeTitle("Edit order");
	if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
		$order_id = (int)$pageInfo[3];
		$find_order = $sql->query("SELECT * FROM `orders` WHERE `order_id` = $order_id");
		if($sql->num_rows($find_order) == 0){
			page::alert("No order was found with this ID!", "danger");
		}else{
			$order = $sql->fetch_array($find_order);
			if(isset($_POST['edit_order'])){
				if($order['order_status'] == "completed"){
					if($page->hasLevel(1)){
						$proceed = true;
					}
				}else{
					$proceed = true;
				}
				if(isset($proceed) AND $proceed){
					$errors = array();
					if(empty($_POST['first_name'])){
						$errors[] = "You forgot to enter the first name!";
					}
					if(empty($_POST['last_name'])){
						$errors[] = "You forgot to enter the last name!";
					}
					if(empty($_POST['invoice_date'])){
						$errors[] = "You forgot to enter the invoice date!";
					}
					if(empty($_POST['shipment_date'])){
						$errors[] = "You forgot to enter the shipment date!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$vat = $sql->smart($_POST['vat']);
						$order_type = $sql->smart($_POST['type']);
						$first_name = $sql->smart($_POST['first_name']);
						$last_name = $sql->smart($_POST['last_name']);
						$company_name = $sql->smart($_POST['company_name']);
						$email = $sql->smart($_POST['email']);
						$mobile_number = $sql->smart($_POST['mobile_number']);
						$home_number = $sql->smart($_POST['home_number']);
						$work_number = $sql->smart($_POST['work_number']);
						$order_ref = $sql->smart($_POST['order_ref']);
						$billing_line1 = $sql->smart($_POST['billing_line1']);
						$billing_line2 = $sql->smart($_POST['billing_line2']);
						$billing_line3 = $sql->smart($_POST['billing_line3']);
						$billing_line4 = $sql->smart($_POST['billing_line4']);
						$billing_postcode = $sql->smart($_POST['billing_postcode']);
						$shipping_line1 = $sql->smart($_POST['shipping_line1']);
						$shipping_line2 = $sql->smart($_POST['shipping_line2']);
						$shipping_line3 = $sql->smart($_POST['shipping_line3']);
						$shipping_line4 = $sql->smart($_POST['shipping_line4']);
						$shipping_postcode = $sql->smart($_POST['shipping_postcode']);
						$priority = $sql->smart($_POST['priority']);
						$invoice_date = $sql->smart($_POST['invoice_date']);
						$shipment_date = $sql->smart(strtotime(str_replace("/", "-", $_POST['shipment_date'])));
						if(isset($_POST['committed'])){
							$committed = 1;
						}else{
							$committed = 0;
						}
						$order_status = $sql->smart($_POST['order_status']);
						if($_POST['order_status'] != $order['order_status']){
							$new_status = "Status changed from <strong>" . $page->order_status[$order['order_status']] . "</strong> to <strong>" . $page->order_status[$_POST['order_status']] . "</strong>";
							$page->logAction("order", $order['order_id'], true, $new_status);
						}
						if(isset($_POST['build_service'])){
							$build_service = $sql->smart($_POST['build_service']);
						}else{
							$build_service = $sql->smart("");
						}
						if(isset($_POST['delivery_method'])){
							$delivery_method = $sql->smart($_POST['delivery_method']);
						}else{
							$delivery_method = $sql->smart("");
						}
						$delivery_instructions = $sql->smart($_POST['delivery_instructions']);
						if(isset($_POST['saturday'])){
							$saturday = 1;
						}else{
							$saturday = 0;
						}
						$number_of_parcels = $sql->smart($_POST['number_of_parcels']);
						$warranty = $sql->smart($_POST['warranty']);
						$shipping_exc_vat = $sql->smart($_POST['shipping_exc_vat']);
						$upgrades_exc_vat = $sql->smart($_POST['upgrades_exc_vat']);
						$other_exc_vat = $sql->smart($_POST['other_exc_vat']);
						$sql->query("UPDATE `orders` SET `vat` = $vat, `order_type` = $order_type, `first_name` = $first_name, `last_name` = $last_name, `company_name` = $company_name, `email` = $email, `mobile_number` = $mobile_number, `home_number` = $home_number, `work_number` = $work_number, `order_ref` = $order_ref, `billing_line1` = $billing_line1, `billing_line2` = $billing_line2, `billing_line3` = $billing_line3, `billing_line4` = $billing_line4, `billing_postcode` = $billing_postcode, `shipping_line1` = $shipping_line1, `shipping_line2` = $shipping_line2, `shipping_line3` = $shipping_line3, `shipping_line4` = $shipping_line4, `shipping_postcode` = $shipping_postcode, `priority` = $priority, `invoice_date` = $invoice_date, `shipment_date` = $shipment_date, `committed` = $committed, `order_status` = $order_status, `build_service` = $build_service, `delivery_method` = $delivery_method, `delivery_instructions` = $delivery_instructions, `saturday` = $saturday, `number_of_parcels` = $number_of_parcels, `warranty` = $warranty, `shipping_exc_vat` = $shipping_exc_vat, `upgrades_exc_vat` = $upgrades_exc_vat, `other_exc_vat` = $other_exc_vat WHERE `order_id` = $order_id");
						page::alert("Order successfully updated!", "success");
						$page->logAction("order", $order['order_id'], false, "order_updated");
						$find_order = $sql->query("SELECT * FROM `orders` WHERE `order_id` = $order_id");
						$order = $sql->fetch_array($find_order);
						$sendStatusUpdate = isset($_POST['sendStatusUpdate']) ? true : false;
						if($sendStatusUpdate === true){
							$email = url . "/email/5/" . $order['order_id'];
							?>
							<script type="text/javascript">
								window.open("<?php echo $email; ?>", "_blank");
							</script>
							<?php
						}
					}
				}
			}
			$invoice = $sql->fetch_array($sql->query("SELECT `invoice_number` FROM `invoices` WHERE `order_id` = $order_id"));
			?>
			<ul class="nav nav-tabs margin">
				<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
				<li><a href="#products" data-toggle="tab">Products</a></li>
				<li><a href="#payment" data-toggle="tab">Payment</a></li>
				<li><a href="#notes" data-toggle="tab">Notes</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="information">
					<form method="POST" class="form-horizontal">
						<fieldset>
							<legend>Customer Information</legend>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="batch_number" class="col-xs-4 control-label">Order ID</label>
									<div class="col-xs-8">
										<div class="input-group">
											<input type="text" class="form-control" value="<?=$order['order_id']?>" style="width: 330px" readonly>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="batch_number" class="col-xs-4 control-label">Invoice #</label>
									<div class="col-xs-8">
										<?php if(empty($invoice['invoice_number'])): ?>
											<input type="text" class="form-control" value="Proforma" readonly>
										<?php else: ?>
											<div class="input-group">
												<span class="input-group-addon">TH-</span>
												<input type="text" class="form-control" value="<?=$invoice['invoice_number']?>" readonly>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group">
									<label for="first_name" class="col-xs-4 control-label">First name*</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="first_name" placeholder="First name" value="<?=$order['first_name']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="company_name" class="col-xs-4 control-label">Company name</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="company_name" placeholder="Company name" value="<?=$order['company_name']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Mobile number</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="mobile_number" placeholder="Mobile number" value="<?=$order['mobile_number']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Work number</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="work_number" placeholder="Work number" value="<?=$order['work_number']?>">
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="last_name" class="col-xs-4 control-label">Order type</label>
									<div class="col-xs-8">
										<select class="form-control" name="type">
											<?php
											$pc = ($order['order_type'] == "pc") ? " selected" : "";
											$parts = ($order['order_type'] == "parts") ? " selected" : "";
											?>
											<option value="pc"<?=$pc?>>PC</option>
											<option value="parts"<?=$parts?>>Services & Parts Sale</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="batch_number" class="col-xs-4 control-label">Created</label>
									<div class="col-xs-8">
										<div class="input-group">
											<input type="text" class="form-control" value="<?=date("d/m/Y H:i", $order['created'])?>" style="width: 330px" readonly>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="last_name" class="col-xs-4 control-label">Last name*</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="last_name" placeholder="Last name" value="<?=$order['last_name']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="email" class="col-xs-4 control-label">Email</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="email" placeholder="Email" value="<?=$order['email']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="home_number" class="col-xs-4 control-label">Home number</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="home_number" placeholder="Home number" value="<?=$order['home_number']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="order_ref" class="col-xs-4 control-label">Order reference</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="order_ref" placeholder="Order reference" value="<?=$order['order_ref']?>">
									</div>
								</div>
							</div>
							<legend>Addresses</legend>
							<div class="col-xs-6">
								<legend>Billing Address</legend>
								<div class="form-group">
									<label for="first_name" class="col-xs-4 control-label">Search Postcode</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" onBlur="findAddress(this, 'billing')" placeholder="Find Postcode">
										<select class="form-control" id="billing_addressSelection" onChange="retrieveAddress(this, 'billing')">
											<option selected disabled>Enter Postcode for Listing</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="first_name" class="col-xs-4 control-label">Line 1</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="billing_line1" id="billing_line1" placeholder="Line 1" value="<?=$order['billing_line1']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="company_name" class="col-xs-4 control-label">Line 2</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="billing_line2" id="billing_line2" placeholder="Line 2" value="<?=$order['billing_line2']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Line 3</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="billing_line3" id="billing_line3" placeholder="Line 3" value="<?=$order['billing_line3']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Line 4</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="billing_line4" id="billing_line4" placeholder="Line 4" value="<?=$order['billing_line4']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Postcode</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="billing_postcode" id="billing_postcode" placeholder="Postcode" value="<?=$order['billing_postcode']?>">
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<legend>Shipping Address</legend>
								<div class="form-group">
									<label for="first_name" class="col-xs-4 control-label">Search Postcode</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" onBlur="findAddress(this, 'shipping')" placeholder="Find Postcode">
										<select class="form-control" id="shipping_addressSelection" onChange="retrieveAddress(this, 'shipping')">
											<option selected disabled>Enter Postcode for Listing</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="first_name" class="col-xs-4 control-label">Line 1</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="shipping_line1" id="shipping_line1" placeholder="Line 1" value="<?=$order['shipping_line1']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="company_name" class="col-xs-4 control-label">Line 2</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="shipping_line2" id="shipping_line2" placeholder="Line 2" value="<?=$order['shipping_line2']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Line 3</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="shipping_line3" id="shipping_line3" placeholder="Line 3" value="<?=$order['shipping_line3']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Line 4</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="shipping_line4" id="shipping_line4" placeholder="Line 4" value="<?=$order['shipping_line4']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="mobile_number" class="col-xs-4 control-label">Postcode</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="shipping_postcode" id="shipping_postcode" placeholder="Postcode" value="<?=$order['shipping_postcode']?>">
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<div class="controls">
											<label>
												<input type="checkbox" id="same_address" onChange="sameAddress()">
												<span class="lbl"> Same as Billing Address</span>
											</label>
										</div>
									</div>
								</div>
							</div>
							<legend>Order & Delivery</legend>
							<div class="col-xs-6">
								<legend>Order Information</legend>
								<div class="form-group">
									<label for="priority" class="col-xs-4 control-label">Priority</label>
									<div class="col-xs-8">
										<div class="btn-group" data-toggle="buttons">
											<?php
											$normal = ($order['priority'] == "normal") ? " checked" : "";
											$urgent = ($order['priority'] == "urgent") ? " checked" : "";
											$normal_active = ($order['priority'] == "normal") ? " active" : "";
											$urgent_active = ($order['priority'] == "urgent") ? " active" : "";
											?>
											<label class="btn btn-primary<?=$normal_active?>">
												<input type="radio" name="priority" id="option1" autocomplete="off" value="normal"<?=$normal?>> Normal
											</label>
											<label class="btn btn-primary<?=$urgent_active?>">
												<input type="radio" name="priority" id="option2" autocomplete="off" value="urgent"<?=$urgent?>> Urgent
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="build_service" class="col-xs-4 control-label">Build Service</label>
									<div class="col-xs-8">
										<select class="form-control" name="build_service">
											<option disabled selected>Select build service</option>
											<?php
											foreach($page->build_service as $key => $name){
												$selected = ($order['build_service'] == $key) ? " selected" : "";
												?>
												<option value="<?=$key?>"<?=$selected?>><?=$name?></option>
												<?php
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="committed" class="col-xs-4 control-label">Committed</label>
									<div class="col-xs-8">
										<?php
										$committed = ($order['committed'] == 1) ? " checked" : "";
										$committed_active = ($order['committed'] == 1) ? " active" : "";
										?>
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary<?=$committed_active?>">
												<input type="checkbox" name="committed" autocomplete="off"<?=$committed?>> Committed
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="invoice_date" class="col-xs-4 control-label">Invoice Date*</label>
									<div class="col-xs-8">
										<input type="text" class="form-control datepicker" name="invoice_date" placeholder="Invoice Date" value="<?=$order['invoice_date']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="shipment_date" class="col-xs-4 control-label">Shipment Date</label>
									<div class="col-xs-8">
										<input type="text" class="form-control datepicker" id="shipment_date" name="shipment_date" placeholder="Shipment Date" value="<?=date("d/m/Y", $order['shipment_date'])?>">
									</div>
								</div>
								<div class="form-group">
									<label for="order_status" class="col-xs-4 control-label">Order Status</label>
									<div class="col-xs-8">
										<div class="input-group">
											<span class="input-group-addon" style="padding: 3px 12px;">
												<label>
													<input type="checkbox" name="sendStatusUpdate">
													<span class="lbl"></span>
												</label>
											</span>
											<select class="form-control" name="order_status">
												<?php
												foreach($page->order_status as $key => $name){
													$selected = ($order['order_status'] == $key) ? " selected" : "";
													?>
													<option value="<?=$key?>"<?=$selected?>><?=$name?></option>
													<?php
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<legend>Delivery Information</legend>
								<div class="form-group">
									<label for="delivery_method" class="col-xs-4 control-label">Delivery method</label>
									<div class="col-xs-8">
										<select class="form-control" name="delivery_method" id="delivery_method">
											<option disabled selected>Select delivery method</option>
											<?php
											$delivery_methods = $sql->query("SELECT * FROM `delivery_methods` WHERE `orders` = 1 ORDER BY `delivery_name` ASC");
											while($delivery_method = $sql->fetch_array($delivery_methods)){
												$selected = ($delivery_method['delivery_key'] == $order['delivery_method']) ? " selected" : "";
												?>
												<option value="<?=$delivery_method['delivery_key']?>"<?=$selected?>><?=$delivery_method['delivery_name']?></option>
												<?php
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="delivery_instructions" class="col-xs-4 control-label">Delivery Instructions</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="delivery_instructions" placeholder="Delivery Instructions" value="<?=$order['delivery_instructions']?>">
									</div>
								</div>
								<div class="form-group">
									<label for="saturday" class="col-xs-4 control-label">Saturday</label>
									<div class="col-xs-8">
										<?php
										$saturday = ($order['saturday'] == 1) ? " checked" : "";
										$saturday_active = ($order['saturday'] == 1) ? " active" : "";
										?>
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary<?=$saturday_active?>">
												<input type="checkbox" id="saturday" name="saturday" autocomplete="off"<?=$saturday?>> Deliver on Saturday
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label">Set Delivery</label>
									<div class="col-xs-8">
										<button type="button" class="btn btn-primary" onClick="estDelDate()">Set Delivery Date</button>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label">Delivery Date</label>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="delivery_date" placeholder="Delivery Date" readonly>
									</div>
								</div>
								<div class="form-group">
									<label for="number_of_parcels" class="col-xs-4 control-label">Number of Parcels</label>
									<div class="col-xs-8">
										<input type="number" class="form-control" name="number_of_parcels" placeholder="Number of Parcels" value="<?=$order['number_of_parcels']?>" min="0">
									</div>
								</div>
							</div>
							<legend>Warranty Information</legend>
							<div class="form-group">
								<label for="warranty" class="col-xs-2 control-label">Warranty Selection</label>
								<div class="col-xs-10">
									<select class="form-control" name="warranty">
										<?php
										$warranty_options = $sql->query("SELECT * FROM `warranty_options` ORDER BY `id` ASC");
										while($warranty_option = $sql->fetch_array($warranty_options)){
											$selected = ($warranty_option['id'] == $order['warranty']) ? " selected" : "";
											?>
											<option value="<?=$warranty_option['id']?>"<?=$selected?>><?=$warranty_option['description']?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
							<legend>Pricing Information</legend>
							<div class="form-group">
								<label for="vat" class="col-xs-2 control-label">VAT</label>
								<div class="col-xs-3">
									<select class="form-control" name="vat" id="vat" onChange="updateOrderTotal('exc')">
										<option value="20.00" <?php echo ($order['vat'] == "20.00") ? "selected" : ""; ?>>20.00%</option>
										<option value="0.00" <?php echo ($order['vat'] == "0.00") ? "selected" : ""; ?>>0.00%</option>
									</select>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label for="shipping_exc_vat" class="col-xs-4 control-label">Shipping exc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="shipping_exc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('exc')" name="shipping_exc_vat" placeholder="Shipping exc. VAT" value="<?=$order['shipping_exc_vat']?>">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="upgrades_exc_vat" class="col-xs-4 control-label">Upgrades exc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="upgrades_exc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('exc')" name="upgrades_exc_vat" placeholder="Upgrades exc. VAT" value="<?=$order['upgrades_exc_vat']?>">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="surcharge_exc_vat" class="col-xs-4 control-label">Other Charges exc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="other_exc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('exc')" name="other_exc_vat" placeholder="Other Charges exc. VAT" value="<?=$order['other_exc_vat']?>">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label">Total exc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="total_exc_vat" class="form-control" readonly>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label class="col-xs-4 control-label">Shipping inc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="shipping_inc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('inc')" placeholder="Shipping inc. VAT">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label">Upgrades inc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="upgrades_inc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('inc')" placeholder="Upgrades inc. VAT">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label">Other Charges inc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="other_inc_vat" class="form-control" onclick="money(this)" onblur="money(this), updateOrderTotal('inc')" placeholder="Other Charges inc. VAT">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-4 control-label">Total inc. VAT</label>
									<div class="col-xs-8 form-inline">
										<div class="form-group col-xs-6">
											<input type="text" id="total_inc_vat" class="form-control" readonly>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
						<?php
						if($order['order_status'] == "completed"){
							if($page->hasLevel(1)){
								?>
								<div class="form-button">
									<button type="submit" class="btn btn-primary btn-sm" name="edit_order">Edit order</button>
								</div>
								<?php
							}
						}else{
							?>
							<div class="form-button">
								<button type="submit" class="btn btn-primary btn-sm" name="edit_order">Edit order</button>
							</div>
							<?php
						}
						?>
					</form>
				</div>
				<div class="tab-pane fade" id="products">
					<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tbody>
							<tr>
								<?php
								$find_parts = $sql->query("SELECT `spec_id` FROM `order_specs` WHERE `type` = 'parts' AND `order_id` = $order_id");
								$countSpecs = $sql->num_rows($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `order_id` = $order_id AND `type` = 'pc'"));
								if(isset($_POST['update_quantity']) AND isset($_POST['pc_quantity'])){
									if($order['order_status'] == "completed"){
										if($page->hasLevel(1)){
											$proceed = true;
										}
									}else{
										$proceed = true;
									}
									if(isset($proceed) AND $proceed){
										$pc_quantity = $_POST['pc_quantity'];
										if(($pc_quantity - $countSpecs) < 0){
											$limit = abs($pc_quantity - $countSpecs);
											for($i = 1; $i <= $limit; $i++){
												$latest = $sql->fetch_array($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `order_id` = $order_id AND `type` = 'pc' ORDER BY `spec_id` DESC LIMIT 1"));
												$latest_id = $sql->smart($latest['spec_id']);
												$sql->query("DELETE FROM `order_specs` WHERE `spec_id` = $latest_id");
												$sql->query("DELETE FROM `order_items` WHERE `order_spec_id` = $latest_id");
											}
										}elseif(($pc_quantity - $countSpecs) == 0){

										}else{
											for($i = 1; $i <= ($pc_quantity - $countSpecs); $i++){
												$sql->query("INSERT INTO `order_specs` (`order_id`, `type`, `cost_exc_vat`, `quantity`) VALUES ($order_id, 'pc', '0.00', '1')");
											}
										}
										$countSpecs = $sql->num_rows($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `order_id` = $order_id AND `type` = 'pc'"));
										page::alert("PC Quantity updated!", "success");
									}
								}
								if(isset($_POST['add_parts'])){
									if($order['order_status'] == "completed"){
										if($page->hasLevel(1)){
											$proceed = true;
										}
									}else{
										$proceed = true;
									}
									if(isset($proceed) AND $proceed){
										$sql->query("INSERT INTO `order_specs` (`order_id`, `type`) VALUES ($order_id, 'parts')");
										page::alert("Parts added successfully!", "success");
										$find_parts = $sql->query("SELECT `spec_id` FROM `order_specs` WHERE `type` = 'parts' AND `order_id` = $order_id");
									}
								}
								if(isset($_POST['remove_parts'])){
									if($order['order_status'] == "completed"){
										if($page->hasLevel(1)){
											$proceed = true;
										}
									}else{
										$proceed = true;
									}
									if(isset($proceed) AND $proceed){
										$parts = $sql->fetch_array($find_parts);
										$spec_id = $parts['spec_id'];
										$sql->query("DELETE FROM `order_items` WHERE `order_spec_id` = $spec_id");
										$sql->query("DELETE FROM `order_specs` WHERE `type` = 'parts' AND `order_id` = $order_id");
										page::alert("Parts removed successfully!", "success");
										$find_parts = $sql->query("SELECT `spec_id` FROM `order_specs` WHERE `type` = 'parts' AND `order_id` = $order_id");
									}
								}
								$servicesCount = $sql->num_rows($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `type` = 'aservices' AND `order_id` = $order_id"));
								if(isset($_POST['add_services'])){
									if($servicesCount == 0){
										if($order['order_status'] == "completed"){
											if($page->hasLevel(1)){
												$proceed = true;
											}
										}else{
											$proceed = true;
										}
										if(isset($proceed) AND $proceed){
											$sql->query("INSERT INTO `order_specs` (`order_id`, `type`) VALUES ($order_id, 'aservices')");
											page::alert("Services added successfully!", "success");
											$servicesCount = $sql->num_rows($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `type` = 'aservices' AND `order_id` = $order_id"));
										}
									}else{
										page::alert("Services already exist in this order!", "danger");
									}
								}
								if(isset($_POST['remove_services'])){
									if($servicesCount == 1){
										if($order['order_status'] == "completed"){
											if($page->hasLevel(1)){
												$proceed = true;
											}
										}else{
											$proceed = true;
										}
										if(isset($proceed) AND $proceed){
											$services = $sql->fetch_array($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `type` = 'aservices' AND `order_id` = $order_id"));
											$spec_id = $services['spec_id'];
											$sql->query("DELETE FROM `order_items` WHERE `order_spec_id` = $spec_id");
											$sql->query("DELETE FROM `order_specs` WHERE `type` = 'aservices' AND `order_id` = $order_id");
											$servicesCount = $sql->num_rows($sql->query("SELECT `spec_id` FROM `order_specs` WHERE `type` = 'aservices' AND `order_id` = $order_id"));
											page::alert("Services removed successfully!", "success");
										}
									}else{
										page::alert("Services do not exist in this order!", "danger");
									}
								}
								$partCount = $sql->num_rows($find_parts);
								?>
								<form method="POST">
									<?php
									if($order['order_type'] == "pc"){
										?>
										<td>PC Quantity</td>
										<td><input type="number" class="form-control" name="pc_quantity" value="<?=$countSpecs?>" value="PC Quantity"></td>
										<?php
										if($order['order_status'] == "completed"){
											if($page->hasLevel(1)){
												?>
												<td><button type="submit" class="btn btn-primary btn-sm" name="update_quantity"><span class="glyphicon glyphicon-random"></span> Update</button></td>
												<?php
											}
										}else{
											?>
											<td><button type="submit" class="btn btn-primary btn-sm" name="update_quantity"><span class="glyphicon glyphicon-random"></span> Update</button></td>
											<?php
										}
									}else{
										?>
										<td colspan="2" width="400px"></td>
										<?php
									}
									if($order['order_status'] == "completed"){
										if($page->hasLevel(1)){
											if($partCount == 0){
												?>
												<td><button type="submit" class="btn btn-primary btn-sm" name="add_parts"><span class="glyphicon glyphicon-plus"></span> Add parts</button></td>
												<?php
											}else{
												?>
												<td><button type="submit" class="btn btn-primary btn-sm" name="remove_parts"><span class="glyphicon glyphicon-remove"></span> Remove parts</button></td>
												<?php
											}
										}
									}else{
										if($partCount == 0){
											?>
											<td><button type="submit" class="btn btn-primary btn-sm" name="add_parts"><span class="glyphicon glyphicon-plus"></span> Add parts</button></td>
											<?php
										}else{
											?>
											<td><button type="submit" class="btn btn-primary btn-sm" name="remove_parts"><span class="glyphicon glyphicon-remove"></span> Remove parts</button></td>
											<?php
										}
									}
									if($order['order_status'] == "completed"){
										if($page->hasLevel(1)){
											if($servicesCount == 0){
												?>
												<td><button type="submit" class="btn btn-primary btn-sm" name="add_services"><span class="glyphicon glyphicon-plus"></span> Add services</button></td>
												<?php
											}else{
												?>
												<td><button type="submit" class="btn btn-primary btn-sm" name="remove_services"><span class="glyphicon glyphicon-remove"></span> Remove services</button></td>
												<?php
											}
										}
									}else{
										if($servicesCount == 0){
											?>
											<td><button type="submit" class="btn btn-primary btn-sm" name="add_services"><span class="glyphicon glyphicon-plus"></span> Add services</button></td>
											<?php
										}else{
											?>
											<td><button type="submit" class="btn btn-primary btn-sm" name="remove_services"><span class="glyphicon glyphicon-remove"></span> Remove services</button></td>
											<?php
										}
									}
									?>
								</form>
							</tr>
							<?php
							if(isset($_POST['duplicate']) AND isset($_POST['from']) AND isset($_POST['to'])){
								if($order['order_status'] == "completed"){
									if($page->hasLevel(1)){
										$proceed = true;
									}
								}else{
									$proceed = true;
								}
								if(isset($proceed) AND $proceed){
									$from = $_POST['from'];
									$to = $_POST['to'];
									if($from == $to){
										page::alert("You selected the same specification!", "danger");
									}else{
										$get_spec = $sql->fetch_array($sql->query("SELECT `cost_exc_vat`, `quantity` FROM `order_specs` WHERE `spec_id` = $from"));
										$get_cost = $sql->smart($get_spec['cost_exc_vat']);
										$get_quantity = $sql->smart($get_spec['quantity']);
										$sql->query("UPDATE `order_specs` SET `cost_exc_vat` = $get_cost, `quantity` = $get_quantity WHERE `spec_id` = $to");
										$sql->query("DELETE FROM `order_items` WHERE `order_spec_id` = $to");
										$from_spec = $sql->query("SELECT `upc`, `quantity` FROM `order_items` WHERE `order_spec_id` = $from");
										while($spec = $sql->fetch_array($from_spec)){
											$upc = $spec['upc'];
											$quantity = $spec['quantity'];
											$sql->query("INSERT INTO `order_items` (`order_spec_id`, `upc`, `quantity`, `order_id`) VALUES ($to, $upc, $quantity, $order_id)");
										}
										page::alert("Duplication was successful!", "success");
									}
								}
							}
							if($countSpecs > 1){
								?>
								<form method="POST">
									<td>Duplicating</td>
									<td>
										<select class="form-control" name="from">
											<option selected disabled>Select Specification</option>
											<?php
											$ii = 1;
											$list_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id AND `type` = 'pc'");
											while($spec = $sql->fetch_array($list_specs)){
												?>
												<option value="<?=$spec['spec_id']?>"><?="PC-" . $ii?></option>
												<?php
												$ii++;
											}
											?>
										</select>
									</td>
									<td>
										<select class="form-control" name="to">
											<option selected disabled>Select Specification</option>
											<?php
											$ii = 1;
											$list_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id AND `type` = 'pc'");
											while($spec = $sql->fetch_array($list_specs)){
												?>
												<option value="<?=$spec['spec_id']?>"><?="PC-" . $ii?></option>
												<?php
												$ii++;
											}
											?>
										</select>
									</td>
									<?php
									if($order['order_status'] == "completed"){
										if($page->hasLevel(1)){
											?>
											<td colspan="2"><button type="submit" class="btn btn-primary btn-sm" name="duplicate">Duplicate</button></td>
											<?php
										}
									}else{
										?>
										<td colspan="2"><button type="submit" class="btn btn-primary btn-sm" name="duplicate">Duplicate</button></td>
										<?php
									}
									?>
								</tr>
							</form>
							<?php
						}
						?>
					</tbody>
				</table>
				<legend>Add Items</legend>
				<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Category</th>
							<th>Sub category</th>
							<th>Item</th>
							<th>UPC</th>
							<th>Quantity</th>
							<th>Add</th>
						</tr>
					</thead>
					<tbody>
						<?php
						for($i = 1; $i <= 10; $i++){
							?>
							<tr>
								<td id="item_count"><?=$i?></td>
								<td style="width: 250px;">
									<select class="form-control input-sm" onchange="get_subcat(<?=$i?>)" id="select_cat_<?=$i?>">
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
								<td style="width: 250px;">
									<span id="subcat_<?=$i?>" onchange="get_catitems_list(<?=$i?>)">
										<select class="form-control input-sm">
											<option disabled selected>Select category to load sub categories</option>
										</select>
									</span>
								</td>
								<td style="width: 250px;">
									<span id="catitems_list_<?=$i?>">
										<select class="form-control input-sm">
											<option disabled selected>Select sub category to load items</option>
										</select>
									</span>
								</td>
								<td style="width: 100px;"><input type="text" class="form-control input-sm" id="upc_<?=$i?>" name="upc[]" placeholder="UPC"></td>
								<td style="width: 50px;"><input class="form-control input-sm" type="number" id="quantity_<?=$i?>" placeholder="Quantity" min="1" value="0"></td>
								<?php
								if($order['order_status'] == "completed"){
									if($page->hasLevel(1)){
										?>
										<td>
											<div class="btn-group">
												<button type="button" class="btn btn-primary" disabled>Add</button>
												<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
													<span class="caret"></span>
													<span class="sr-only">Toggle Dropdown</span>
												</button>
												<ul class="dropdown-menu" role="menu">
													<?php
													$ii = 1;
													$get_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id ORDER BY `type` DESC");
													while($spec = $sql->fetch_array($get_specs)){
														?>
														<li class="pointer"><a onClick="addItem(<?=$spec['spec_id']?>, <?=$i?>, <?=$order_id?>)"><?=($spec['type'] == "pc") ? "PC-" . $ii : "Parts"?></a></li>
														<?php
														$ii++;
													}
													?>
												</ul>
											</div>
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
										<div class="btn-group">
											<button type="button" class="btn btn-primary" disabled>Add</button>
											<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
												<span class="caret"></span>
												<span class="sr-only">Toggle Dropdown</span>
											</button>
											<ul class="dropdown-menu" role="menu">
												<?php
												$ii = 1;
												$get_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id AND `type` != 'aservices' ORDER BY `type` DESC");
												while($spec = $sql->fetch_array($get_specs)){
													?>
													<li class="pointer"><a onClick="addItem(<?=$spec['spec_id']?>, <?=$i?>, <?=$order_id?>)"><?=($spec['type'] == "pc") ? "PC-" . $ii : "Parts"?></a></li>
													<?php
													$ii++;
												}
												?>
											</ul>
										</div>
									</td>
									<?php
								}
								?>
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
				<?php if($servicesCount == 1): ?>
					<legend>Add Services</legend>
					<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Service</th>
								<th>Cost exc VAT</th>
								<th>Quantity</th>
								<th>Add</th>
							</tr>
						</thead>
						<tbody>
							<?php
							for($i = 1; $i <= 1; $i++){
								?>
								<tr>
									<td id="item_count"><?=$i?></td>
									<td>
										<select class="form-control" onchange="get_service_price(<?=$i?>, false)" id="select_service_<?=$i?>">
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
									<td><input class="form-control" type="text" id="service_cost_<?=$i?>" onclick="money(this)" onblur="money(this)" name="cost_exc_vat_<?=$i?>" type="text" placeholder="Cost exc VAT" value="0.00"></td>
									<td style="width: 50px;"><input class="form-control input-sm" type="number" id="service_quantity_<?=$i?>" placeholder="Quantity" min="1" value="0"></td>
									<?php
									if($order['order_status'] == "completed"){
										if($page->hasLevel(1)){
											?>
											<td>
												<div class="btn-group">
													<button type="button" class="btn btn-primary" disabled>Add</button>
													<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
														<span class="caret"></span>
														<span class="sr-only">Toggle Dropdown</span>
													</button>
													<ul class="dropdown-menu" role="menu">
														<?php
														$get_services = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id AND `type` = 'aservices'");
														while($service_spec = $sql->fetch_array($get_services)){
															?>
															<li class="pointer"><a onClick="addService(<?=$service_spec['spec_id']?>, <?=$i?>, <?=$order_id?>)">Services</a></li>
															<?php
														}
														?>
													</ul>
												</div>
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
											<div class="btn-group">
												<button type="button" class="btn btn-primary" disabled>Add</button>
												<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
													<span class="caret"></span>
													<span class="sr-only">Toggle Dropdown</span>
												</button>
												<ul class="dropdown-menu" role="menu">
													<?php
													$get_services = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id AND `type` = 'aservices'");
													while($service_spec = $sql->fetch_array($get_services)){
														?>
														<li class="pointer"><a onClick="addService(<?=$service_spec['spec_id']?>, <?=$i?>, <?=$order_id?>)">Services</a></li>
														<?php
													}
													?>
												</ul>
											</div>
										</td>
										<?php
									}
									?>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				<?php endif; ?>
				<legend>Products</legend>
				<div id="specifications">
					<form method="POST">
						<?php
						if(isset($_POST['update_items']) AND isset($_POST['item_id']) AND isset($_POST['quantity']) AND isset($_POST['item_cost_exc_vat']) AND isset($_POST['spec_id'])){
							if($order['order_status'] == "completed"){
								if($page->hasLevel(1)){
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
												if($page->hasLevel(1)){
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
							if($page->hasLevel(1)){
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
				</div>
			</div>
			<div class="tab-pane fade" id="payment">
				<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Payment Date</th>
							<th>Payment Type</th>
							<th>Payment Reference</th>
							<th>Amount</th>
							<?php if($page->hasLevel(2)): ?>
								<th>Delete</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						if(isset($_POST['add_payment'])){
							if($order['order_status'] == "completed"){
								if($page->hasLevel(1)){
									$proceed = true;
								}
							}else{
								$proceed = true;
							}
							if(isset($proceed) AND $proceed){
								$errors = array();
								if(empty($_POST['payment_date'])){
									$errors[] = "You forgot to select the payment date!";
								}
								if(empty($_POST['amount'])){
									$errors[] = "You forgot enter the amount!";
								}
								if(empty($_POST['reference'])){
									$errors[] = "You forgot to enter the payment reference!";
								}
								if(empty($_POST['type'])){
									$errors[] = "You forgot to select the payment type!";
								}else{
									if($_POST['type'] == "bank_transfer"){
										if(empty($_POST['transfer_account_name'])){
											$errors[] = "You forgot to enter the account name!";
										}
									}elseif($_POST['type'] == "instore_card"){
										if(!isset($_POST['instore_card_type'])){
											$errors[] = "You forgot to select the card type!";
										}
									}elseif($_POST['type'] == "cheque"){
										if(empty($_POST['cheque_number'])){
											$errors[] = "You forgot to enter the cheque number!";
										}
										if(empty($_POST['cheque_bank_name'])){
											$errors[] = "You forgot to enter the cheque bank name!";
										}
										if(isset($_POST['cheque_cleared'])){
											if(empty($_POST['cheque_clearance'])){
												$errors[] = "You forgot to select the cheque clearance date!";
											}
										}
									}
								}
								if(count($errors) > 0){
									foreach($errors as $error){
										page::alert($error, "danger");
									}
								}else{
									$payment_date = $sql->smart($_POST['payment_date']);
									$amount = $sql->smart($_POST['amount']);
									$reference = $sql->smart($_POST['reference']);
									$type = $sql->smart($_POST['type']);
									$transfer_account_name = $sql->smart($_POST['transfer_account_name']);
									if(isset($_POST['instore_card_type'])){
										$instore_card_type = $sql->smart($_POST['instore_card_type']);
									}else{
										$instore_card_type = $sql->smart("");
									}
									$cheque_number = $sql->smart($_POST['cheque_number']);
									$cheque_bank_name = $sql->smart($_POST['cheque_bank_name']);
									if(isset($_POST['cheque_cleared'])){
										$cheque_cleared = 1;
									}else{
										$cheque_cleared = 0;
									}
									$cheque_clearance = $sql->smart($_POST['cheque_clearance']);
									$sql->query("INSERT INTO `payment_entries` (`payment_date`, `type`, `amount`, `reference`, `transfer_account_name`, `instore_card_type`, `cheque_number`, `cheque_cleared`, `cheque_clearance`, `cheque_bank_name`, `order_id`) VALUES ($payment_date, $type, $amount, $reference, $transfer_account_name, $instore_card_type, $cheque_number, $cheque_cleared, $cheque_clearance, $cheque_bank_name, $order_id)");
									page::alert("Payment successfully added!", "success");
									$payments = $sql->query("SELECT * FROM `payment_entries` WHERE `order_id` = $order_id ORDER BY `id` ASC");
									if($sql->num_rows($payments) == 1){
										$sql->query("INSERT INTO `invoices` (`order_id`) VALUES ($order_id)");
										$sql->query("UPDATE `orders` SET `invoice_date` = $payment_date WHERE `order_id` = $order_id");
									}
								}
							}
						}
						$paid = 0;
						$payments = $sql->query("SELECT * FROM `payment_entries` WHERE `order_id` = $order_id ORDER BY `id` ASC");
						while($payment = $sql->fetch_array($payments)){
							?>
							<tr>
								<td><?=$payment['payment_date']?></td>
								<td><?=$page->payment_types[$payment['type']]?></td>
								<td><?=$payment['reference']?></td>
								<td>£<?=$payment['amount']?></td>
								<?php if($page->hasLevel(2)): ?>
									<td><span class="glyphicon glyphicon-remove pointer" onClick="removePayment(<?php echo $payment['id']; ?>)"></span></td>
								<?php endif; ?>
							</tr>
							<?php
							$paid += $payment['amount'];
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
						}
						$total = number_format((float)(($order['other_exc_vat'] + $order['shipping_exc_vat'] + $order['upgrades_exc_vat'] + $item_total + $spec_total) * page::multiplyVAT($order['vat'])), 2, ".", "");
						if($total == $paid){
							$payment_info = number_format((float)$total - $paid, 2, ".", "");
							$payment_colour = "green";
							$payment_icon = "ok";
						}elseif($total > $paid){
							$payment_info = number_format((float)$total - $paid, 2, ".", "");
							$payment_colour = "red";
							$payment_icon = "exclamation-sign";
						}else{
							$payment_info = number_format((float)$total - $paid, 2, ".", "");
							$payment_colour = "#FFCC00";
							$payment_icon = "warning-sign";
						}
						?>
						<tr>
							<td colspan="5"></td>
						</tr>
						<tr>
							<td><strong>Total inc VAT</strong></td>
							<td><?=$total?></td>
							<td><strong>Payment Due</strong></td>
							<td style="color: <?=$payment_colour?>;" colspan="<?php echo ($page->hasLevel(2)) ? 2 : 1 ?>"><span class="glyphicon glyphicon-<?=$payment_icon?>"></span> <?=$payment_info?></td>
						</tr>
					</tbody>
				</table>
				<hr />
				<form method="POST">
					<div class="form-group">
						<label class="col-xs-4">Payment date*</label>
						<div class="col-xs-8">
							<input type="text" class="form-control datepicker" name="payment_date" placeholder="Payment date" value="<?=date("d/m/Y")?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4">Amount*</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="amount" onclick="money(this)" onblur="money(this)"  placeholder="Amount" value="0.00">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4">Payment reference*</label>
						<div class="col-xs-8">
							<input type="text" class="form-control" name="reference" placeholder="Payment reference">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4">Payment Type*</label>
						<div class="col-xs-8">
							<select class="form-control" name="type" onChange="paymentType(this.value)">
								<option selected disabled>Select payment type</option>
								<?php
								foreach($page->payment_types as $key => $name){
									?>
									<option value="<?=$key?>"<?=$selected?>><?=$name?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="payment" id="bank_transfer" style="display: none;">
						<div class="form-group">
							<label class="col-xs-4">Account name*</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="transfer_account_name" placeholder="Account name">
							</div>
						</div>
					</div>
					<div class="payment" id="instore_card" style="display: none;">
						<div class="form-group">
							<label class="col-xs-4">Card type*</label>
							<div class="col-xs-8">
								<select class="form-control" name="instore_card_type">
									<option selected disabled>Select card type</option>
									<option value="Visa Credit Card">Visa Credit Card</option>
									<option value="Master Card Credit Card">Master Card Credit Card</option>
									<option value="Switch/Maestro Debit Card">Switch/Maestro Debit Card</option>
									<option value="Solo Debit Card">Solo Debit Card</option>
									<option value="Visa Electron Debit Card">Visa Electron Debit Card</option>
									<option value="Visa Debit Card">Visa Debit Card</option>
								</select>
							</div>
						</div>
					</div>
					<div class="payment" id="cheque" style="display: none;">
						<div class="form-group">
							<label class="col-xs-4">Cheque number*</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="cheque_number" placeholder="Cheque number">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4">Cheque cleared</label>
							<div class="col-xs-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="cheque_cleared" id="cleared" onChange="clearance()">
										<span class="lbl"> Cheque cleared</span>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group" id="clearance_date" style="display: none;">
							<label class="col-xs-4">Clearance date*</label>
							<div class="col-xs-8">
								<input type="text" class="form-control datepicker" name="cheque_clearance" placeholder="Clearance date">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4">Cheque bank name*</label>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="cheque_bank_name" placeholder="Cheque bank name">
							</div>
						</div>
					</div>
					<div style="display: inline-block"></div>
					<?php
					if($order['order_status'] == "completed"){
						if($page->hasLevel(1)){
							?>
							<div class="form-button">
								<button type="submit" class="btn btn-primary btn-sm" name="add_payment">Add payment</button>
							</div>
							<?php
						}
					}else{
						?>
						<div class="form-button">
							<button type="submit" class="btn btn-primary btn-sm" name="add_payment">Add payment</button>
						</div>
						<?php
					}
					?>
				</form>
			</div>
			<div class="tab-pane fade" id="notes">
				<?php
				if(isset($_POST['add_note'])){
					if($order['order_status'] == "completed"){
						if($page->hasLevel(1)){
							$proceed = true;
						}
					}else{
						$proceed = true;
					}
					if(isset($proceed) AND $proceed){
						if(empty($_POST['note'])){
							page::alert("You forgot to enter the note!", "danger");
						}else{
							$note = $sql->smart($_POST['note']);
							$date = $sql->smart(time());
							$added_by_id = $sql->smart($global_user_id);
							if(isset($_POST['invoice'])){
								$invoice = 1;
							}else{
								$invoice = 0;
							}
							$sql->query("INSERT INTO `order_notes` (`order_id`, `note`, `date`, `added_by_id`, `invoice`) VALUES ($order_id, $note, $date, $added_by_id, $invoice)");
							page::alert("Note added successfully!", "success");
							$notes = $sql->query("SELECT * FROM `order_notes` WHERE `order_id` = $order_id ORDER BY `date` DESC");
						}
					}
				}
				?>
				<div style="width: 700px; margin: 0 auto;">
					<form method="POST">
						<textarea type="text" class="form-control" name="note" placeholder="Note..." rows="10"></textarea>
						<div class="pull-right">
							<input type="checkbox" name="invoice">
							<span class="lbl"> Show in Invoice?</span>
						</div>
						<div class="clearfix visible-xs-block"></div>
						<?php
						if($order['order_status'] == "completed"){
							if($page->hasLevel(1)){
								?>
								<div class="form-button">
									<button type="submit" class="btn btn-primary btn-sm" name="add_note">Add note</button>
								</div>
								<?php
							}
						}else{
							?>
							<div class="form-button">
								<button type="submit" class="btn btn-primary btn-sm" name="add_note">Add note</button>
							</div>
							<?php
						}
						?>
					</form>
				</div>
				<legend>Entered Notes</legend>
				<div id="loadNotes">
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
						<?php
						$i = 1;
						$notes = $sql->query("SELECT * FROM `order_notes` WHERE `order_id` = $order_id ORDER BY `date` DESC");
						while($note = $sql->fetch_array($notes)){
							if($i == 1){
								$collapse = "collapse in";
							}else{
								$collapse = "collapse";
							}
							?>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="heading_<?=$i?>">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?=$i?>" aria-controls="collapse_<?=$i?>">
											From <strong><?=page::getUserInfo($note['added_by_id'], "name")?> <?=page::getUserInfo($note['added_by_id'], "surname")?></strong> on <?=date("d/m/Y H:i", $note['date'])?>
										</a>
										<?php
										if(page::hasLevel(1)){
											?>
											<span class="pull-right pointer glyphicon glyphicon-remove" onClick="removeNote(<?=$note['id']?>, <?=$order_id?>)"></span>
											<?php
										}
										?>
									</h4>
								</div>
								<div id="collapse_<?=$i?>" class="panel-collapse <?=$collapse?>" role="tabpanel" aria-labelledby="heading_<?=$i?>">
									<div class="panel-body">
										<?php echo nl2br($note['note']); ?>
									</div>
								</div>
							</div>
							<?php
							$i++;
						}
						?>
					</div>
				</div>
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
						$logs = $sql->query("SELECT * FROM `logs` WHERE `parent_type` = 'order' AND `parent_id` = $order_id ORDER BY `id` ASC");
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
							$email_logs = $sql->query("SELECT * FROM `email_logs` WHERE `parent_type` = 'order' AND `parent_id` = $order_id ORDER BY `id` ASC");
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
		page::alert("No ID was specified to locate an order!", "danger");
	}
}elseif($action == "search"){
	$page->changeTitle("Search item");
	if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
		$upc = (int)$pageInfo[3];
		$find_upc = $sql->num_rows($sql->query("SELECT `item_id` FROM `order_items` WHERE `upc` = $upc"));
		if($find_upc == 0){
			page::alert("No orders were found with this UPC!", "danger");
		}else{
			?>
			<legend>Shipped</legend>
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
						<th>Total price</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$shipped_items = $sql->query("SELECT DISTINCT `order_id` FROM `order_items` WHERE `upc` = $upc");
					while($shipped_item = $sql->fetch_array($shipped_items)){
						$shipped_order_id = $shipped_item['order_id'];
						$shipped_orders = $sql->query("SELECT * FROM `orders` WHERE `order_status` = 'completed' AND `order_id` = $shipped_order_id");
						while($order = $sql->fetch_array($shipped_orders)){
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
							}
							$total = number_format((float)(($order['other_exc_vat'] + $order['shipping_exc_vat'] + $item_total + $spec_total) * page::multiplyVAT($order['vat'])), 2, ".", "");
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
								<td colspan="3"><span class="glyphicon glyphicon-phone"></span> M - <?=$order['mobile_number']?> / H - <?=$order['home_number']?> / W - <?=$order['work_number']?></td>
								<td colspan="4"></td>
								<td style="color: <?=$payment_colour?>; font-weight: bold;" colspan="3"><span class="glyphicon glyphicon-<?=$payment_icon?>"></span> <?=$payment_info?></td>
							</tr>
							<?php
						}
					}
					?>
					<tr>
						<td colspan="10"></td>
					</tr>
					<tr>
						<td colspan="8"></td>
						<td style="width: 200px;"><b>Total Shipped Quantity</b></td>
						<td><?=page::returnShipped($upc)?></td>
					</tr>
				</tbody>
			</table>
			<legend>Committed</legend>
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
						<th>Total price</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$committed_total = 0;
					$committed_items = $sql->query("SELECT DISTINCT `order_id` FROM `order_items` WHERE `upc` = $upc");
					while($committed_item = $sql->fetch_array($committed_items)){
						$committed_order_id = $committed_item['order_id'];
						$committed_orders = $sql->query("SELECT * FROM `orders` WHERE `order_status` != 'completed' AND `order_id` = $committed_order_id");
						while($order = $sql->fetch_array($committed_orders)){
							$order_id = $order['order_id'];
							$committed = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `order_items` WHERE `upc` = $upc AND `order_id` = $order_id"));
							$committed_total += $committed['total'];
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
								<td colspan="3"><span class="glyphicon glyphicon-phone"></span> M - <?=$order['mobile_number']?> / H - <?=$order['home_number']?> / W - <?=$order['work_number']?></td>
								<td colspan="4"></td>
								<td style="color: <?=$payment_colour?>; font-weight: bold;" colspan="3"><span class="glyphicon glyphicon-<?=$payment_icon?>"></span> <?=$payment_info?></td>
							</tr>
							<?php
						}
					}
					?>
					<tr>
						<td colspan="10"></td>
					</tr>
					<tr>
						<td colspan="8"></td>
						<td style="width: 200px;"><b>Total Committed Quantity</b></td>
						<td><?=page::returnCommitted($upc)?></td>
					</tr>
				</tbody>
			</table>
			<?php
		}
	}else{
		page::alert("No UPC was specified!", "danger");
	}
}elseif($action == "build"){
	$orders_count = $sql->num_rows($sql->query("SELECT * FROM `orders` WHERE `order_status` != 'completed' AND `order_type` = 'pc'"));
	if($orders_count > 0){
		?>
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
					<th>Total price</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				list($pagertop, $pagerbottom, $limit) = page::pagination(10, $orders_count, url . "/orders/page/", 4);
				$orders = $sql->query("SELECT * FROM `orders` WHERE `order_status` != 'completed' AND `order_type` = 'pc' ORDER BY `order_id` DESC $limit");
				$order_total = 0;
				$pc_total = 0;
				$parts_total = 0;
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
							<a target="_blank" href="<?=url?>/tobuild/<?=$order['order_id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Build</button></a>
						</td>
					</tr>
					<tr>
						<td colspan="3"><span class="glyphicon glyphicon-phone"></span> M - <?=$order['mobile_number']?> / H - <?=$order['home_number']?> / W - <?=$order['work_number']?></td>
						<td colspan="4"></td>
						<td style="color: <?=$payment_colour?>; font-weight: bold;" colspan="3"><span class="glyphicon glyphicon-<?=$payment_icon?>"></span> <?=$payment_info?></td>
					</tr>
					<?php
					$order_total += $total;
				}
				?>
				<tr>
					<td colspan="10"></td>
				</tr>
				<tr>
					<td colspan="7"></td>
					<td>PCs: <?php echo $pc_total; ?></td>
					<td><strong>Total</strong></td>
					<td><strong>£<?php echo number_format((float)abs($order_total), 2, ".", ""); ?></strong></td>
				</tr>
			</tbody>
		</table>
		<?php
		echo $pagerbottom;
	}else{
		page::alert("Nothing to build at the moment!", "info");
	}
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

	$last_week = date("d/m/Y", strtotime("- 1 week", time()));
	$next_week = date("d/m/Y", strtotime("+ 1 week", time()));
	$from_d = (isset($_POST['from'])) ? $_POST['from'] : $last_week;
	$to_d = (isset($_POST['to'])) ? $_POST['to'] : $next_week;
	if(isset($_POST['view'])){
		if(!empty($_POST['from']) AND !empty($_POST['to'])){
			$to = $_POST['to'];
			$from = $_POST['from'];
		}else{
			$to = $next_week;
			$from = $last_week;
		}
		$orders_count = $sql->num_rows($sql->query("SELECT * FROM `orders` WHERE `invoice_date` <= '$to' AND `invoice_date` >= '$from'"));
		$orders = $sql->query("SELECT * FROM `orders` WHERE `invoice_date` <= '$to' AND `invoice_date` >= '$from' ORDER BY `order_id` DESC");
		$searched = true;
	}else{
		$orders_count = $sql->num_rows($sql->query("SELECT * FROM `orders`"));
		list($pagertop, $pagerbottom, $limit) = page::pagination(10, $orders_count, url . "/orders/page/", 3);
		$orders = $sql->query("SELECT * FROM `orders` ORDER BY `order_id` DESC $limit");
	}
	$order_total = 0;
	$pc_total = 0;
	$parts_total = 0;
	$i = 1;
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
	<div class="pull-right">
		<button type="button" class="btn btn-default" style="margin-bottom: 11px;" onClick="duplicateEntry('order')">Duplicate Selected</button>
	</div>
	<form class="form-inline" role="form" method="POST">
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
	</form>
	<?php
	if($orders_count > 0 AND (!isset($searched))){
		echo $pagerbottom;
	}
}