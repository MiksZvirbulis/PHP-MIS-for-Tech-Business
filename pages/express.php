<?php
$page = new page("Express Receipt", 3);
$global_user_id = $_COOKIE['user_id'];
?>
<?php if(isset($_POST['add_receipt'])): ?>
	<?php
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
	if(empty($_POST['computer_information'])){
		$errors[] = "You forgot to enter the computer information!";
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
	if(count($errors) > 0):
		foreach($errors as $error){
			page::alert($error, "danger");
		}
		else:
			$customer_name = $sql->smart($_POST['customer_name']);
		$customer_email = $sql->smart($_POST['customer_email']);
		$customer_number = $sql->smart($_POST['customer_number']);
		$computer_information = $sql->smart($_POST['computer_information']);
		if(empty($_POST['other_charges'])){
			$other_charges = $sql->smart("0.00");
		}else{
			$other_charges = $sql->smart($_POST['other_charges']);
		}
		$discount = $sql->smart($_POST['discount']);
		$discount_reason = $sql->smart($_POST['discount_reason']);
		$note = $sql->smart($_POST['note']);
		$added = $sql->smart(time());
		$created_by_id = $sql->smart($global_user_id);
		$sql->query("INSERT INTO `receipts` 
			(`customer_name`, `customer_email`, `customer_number`, `computer_information`, `checked`, `other_charges`, `discount`, `discount_reason`, `note`, `customer_note`, `amount_paid`, `added`, `created_by_id`, `status`)
			VALUES
			($customer_name, $customer_email, $customer_number, $computer_information, 0, $other_charges, $discount, $discount_reason, $note, $note, '0.00', $added, $created_by_id, 'repair_progress')
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
	<?php endif; ?>
<?php endif; ?>
<form method="POST" class="form-horizontal">
	<?php echo page::alert("<center><strong>PLEASE MAKE SURE YOU ARE LOGGED IN BEFORE SUBMITTING A RECEIPT!</strong></center>", "danger"); ?>
	<fieldset>
		<legend>Customer</legend>
		<div class="col-xs-6">
			<div class="form-group">
				<label for="customer_name" class="col-xs-4 control-label">Customer's name*</label>
				<div class="col-xs-8">
					<input type="text" class="form-control" name="customer_name" placeholder="Name">
				</div>
			</div>
			<div class="form-group">
				<label for="customer_number" class="col-xs-4 control-label">Customer's number*</label>
				<div class="col-xs-8">
					<input type="text" class="form-control" name="customer_number" placeholder="Number">
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label for="customer_email" class="col-xs-4 control-label">Customer's email</label>
				<div class="col-xs-8">
					<input type="text" class="form-control" name="customer_email" placeholder="Email address">
				</div>
			</div>
			<div class="form-group">
				<label for="computer_information" class="col-xs-4 control-label">Computer information*</label>
				<div class="col-xs-8">
					<textarea type="text" class="form-control" name="computer_information" rows="5" placeholder="Computer information"></textarea>
				</div>
			</div>
		</div>
		<legend>Information</legend>
		<div class="col-xs-6">
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
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<label class="col-xs-4 control-label">Total</label>
				<div class="col-xs-8">
					<input type="text" id="total" class="form-control" value="0.00" readonly>
				</div>
			</div>
		</div>
		<div class="col-xs-10">
			<div class="form-group">
				<label for="note" class="col-xs-4 control-label">Note</label>
				<div class="col-xs-8">
					<textarea type="text" class="form-control" name="note" rows="5" placeholder="Note (This note will also be diplayed to the customer)"></textarea>
				</div>
			</div>
		</div>
	</fieldset>
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
			for($i = 1; $i <= 3; $i++){
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