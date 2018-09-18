<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['service_id']) AND page::isLoggedIn()){
	$service_id = (int)$_POST['service_id'];
	$find_receipts = $sql->query("SELECT `receipt_id` FROM `receipt_items` WHERE `service_id` = $service_id");
	?>
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
			while($ser_receipt = $sql->fetch_array($find_receipts)){
				$receipt_id = $ser_receipt['receipt_id'];
				$receipt = $sql->fetch_array($sql->query("SELECT DISTINCT * FROM `receipts` WHERE `id` = $receipt_id ORDER BY `status` DESC, `added` DESC"));
				if($receipt['status'] == 'shipped' OR $receipt['status'] == 'cancelled'){
					$rec_id = $receipt['id'];
					$service_sum = $sql->fetch_array($sql->query("SELECT SUM(`price` * `computer_quantity`) AS `total_sum` FROM `receipt_items` WHERE `receipt_id` = $rec_id"));
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
					$page = new page("", 3, false)
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
			}
			?>
		</tbody>
	</table>
	<?php
}else{
	exit;
}
?>