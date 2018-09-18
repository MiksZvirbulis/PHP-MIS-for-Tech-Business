<?php
$page = new page("Home", 3);
$time = time();
?>
<center style="width: 500px; margin: 0 auto;">
	<select onChange="getReceipts(this)" class="form-control">
		<option value="active" selected>Active</option>
		<option value="unpaid">Unpaid</option>
		<option value="activeService">Active by Service</option>
		<option value="activeServiceCompleted">Completed by Service</option>
	</select>
	<select onChange="requestByService(this)" class="form-control" id="services" data-active="yes" style="display: none;">
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
</center>
<div class="active recTable">
	<legend>Active Receipts</legend>
	<table class="table" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="50px">ID</th>
				<th width="150px">Name</th>
				<th width="50px">Number</th>
				<th>Amount</th>
				<th>Amount paid</th>
				<th>Created</th>
				<th width="200px">Status</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$receipts = $sql->query("SELECT * FROM `receipts` WHERE `status` != 'shipped' AND `status` != 'cancelled' ORDER BY `status` DESC, `added` DESC");
			while($receipt = $sql->fetch_array($receipts)){
				$receipt_id = $receipt['id'];
				$service_sum = $sql->fetch_array($sql->query("SELECT SUM(`price` * `computer_quantity`) AS `total_sum` FROM `receipt_items` WHERE `receipt_id` = $receipt_id"));
				$total_sum = $receipt['other_charges'] + $service_sum['total_sum'] - $receipt['discount'];
				$total = number_format((float)$total_sum, 2, ".", "");
				$amount_paid = $receipt['amount_paid'];
				if($total > $amount_paid){
					$payment_status = "danger";
				}elseif($total < $amount_paid){
					$payment_status = "warning";
				}else{
					$payment_status = "success";
				}
				?>
				<tr>
					<td><b><?=$receipt['id']?></b></td>
					<td><?=$receipt['customer_name']?> <?=$receipt['customer_surname']?></td>
					<td><?=$receipt['customer_number']?></td>
					<td>£<?=$total?></td>
					<td class="<?=$payment_status?>">£<?=$receipt['amount_paid']?></td>
					<td><?=date("d/m/Y", $receipt['added'])?></td>
					<td><b><?=$page->receipt_status[$receipt['status']]?></b></td>
					<td>
						<a href="<?=url?>/receipts/edit/<?=$receipt['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
						<a href="<?=url?>/receipt/<?=$receipt['id']?>" target="_blank"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-file" style="opacity:0.4;"></span> View</button></a>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
<div class="unpaid recTable" style="display: none;">
	<legend>Unpaid Receipts</legend>
	<table class="table" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="50px">ID</th>
				<th width="150px">Name</th>
				<th width="50px">Number</th>
				<th>Amount</th>
				<th>Amount paid</th>
				<th>Amount due</th>
				<th>Created</th>
				<th width="200px">Status</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$receipts = $sql->query("SELECT * FROM `receipts` ORDER BY `status` DESC");
			$total_amount = 0;
			$total_amount_paid = 0;
			$total_amount_due = 0;
			while($receipt = $sql->fetch_array($receipts)){
				$receipt_id = $receipt['id'];
				$service_sum = $sql->fetch_array($sql->query("SELECT SUM(`price` * `computer_quantity`) AS `total_sum` FROM `receipt_items` WHERE `receipt_id` = $receipt_id"));
				$total_sum = $receipt['other_charges'] + $service_sum['total_sum'] - $receipt['discount'];
				$total = number_format((float)$total_sum, 2, ".", "");
				$amount_paid = $receipt['amount_paid'];
				$amount_due = number_format((float)$total - $receipt['amount_paid'], 2, ".", "");
				if($total > $amount_paid){
					$payment_status = "danger";
				}elseif($total < $amount_paid){
					$payment_status = "warning";
				}else{
					$payment_status = "success";
				}
				if($receipt['status'] == "cancelled"){
					$payment_status = "success";
				}
				if($payment_status != "success"){
					?>
					<tr>
						<td><b><?=$receipt['id']?></b></td>
						<td><?=$receipt['customer_name']?> <?=$receipt['customer_surname']?></td>
						<td><?=$receipt['customer_number']?></td>
						<td>£<?=$total?></td>
						<td class="<?=$payment_status?>">£<?=$receipt['amount_paid']?></td>
						<td style="color: red; font-weight: bold;"><span class="glyphicon glyphicon-warning-sign"></span> £<?=$amount_due?></td>
						<td><?=date("d/m/Y", $receipt['added'])?></td>
						<td><?=$page->receipt_status[$receipt['status']]?></td>
						<td>
							<a href="<?=url?>/receipts/edit/<?=$receipt['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
							<a href="<?=url?>/receipt/<?=$receipt['id']?>" target="_blank"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-file" style="opacity:0.4;"></span> View</button></a>
						</td>
					</tr>
					<?php
					$total_amount += $total;
					$total_amount_paid += $receipt['amount_paid'];
					$total_amount_due += $amount_due;
				}
			}
			?>
			<tr class="active">
				<td colspan="3" class="text-right"><strong>Total</strong></td>
				<td><strong>£<?php echo number_format((float)$total_amount, 2, ".", ""); ?></strong></td>
				<td><strong>£<?php echo number_format((float)$total_amount_paid, 2, ".", ""); ?></strong></td>
				<td><strong>£<?php echo number_format((float)$total_amount_due, 2, ".", ""); ?></strong></td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="9"></td>
			</tr>
			<tr>
				<td class="danger"></td>
				<td colspan="9">Amount paid is smaller than the actual amount</td>
			</tr>
			<tr>
				<td class="success"></td>
				<td colspan="9">Amount paid fully</td>
			</tr>
			<tr>
				<td class="warning"></td>
				<td colspan="9">Amount paid exceeds the actual amount</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="activeService recTable" style="display: none;">

</div>