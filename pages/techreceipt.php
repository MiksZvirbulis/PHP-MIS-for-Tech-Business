<?php
if(isset($pageInfo[2])){
	$receipt_id = (int)$pageInfo[2];
	$query = $sql->query("SELECT * FROM `receipts` WHERE `id` = $receipt_id");
	if($sql->num_rows($query) == 0){
		header("Location: " . url . "/receipts");
	}else{
		$receipt = $sql->fetch_array($query);
		$scratches_on_body = ($receipt['scratches_on_body'] == 1) ? "ok" : "remove";
		$scratches_on_screen = ($receipt['scratches_on_screen'] == 1) ? "ok" : "remove";
		$screws_missing = ($receipt['screws_missing'] == 1) ? "ok" : "remove";
		$rubber_pad_missing = ($receipt['rubber_pad_missing'] == 1) ? "ok" : "remove";
		$keyboard_btns_missing = ($receipt['keyboard_btns_missing'] == 1) ? "ok" : "remove";
		$charger_missing = ($receipt['charger_missing'] == 1) ? "ok" : "remove";
		$battery_missing = ($receipt['battery_missing'] == 1) ? "ok" : "remove";
		$checked = ($receipt['checked'] == 1) ? "ok" : "remove";
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="utf-8">
			<title>Receipt</title>
			<link rel="stylesheet" href="<?=url?>/assets/css/bootstrap.css">
			<style>
				body{
					width: 1000px;
					margin: 0 auto;
				}
				.invoice-head td{
					padding: 0 8px;
				}

				.container{
					padding-top: 30px;
				}

				.invoice-body{
					background-color: transparent;
				}

				.invoice-thank{
					padding: 5px;
				}

				address{
					margin-top: 15px;
					float: right;
				}

				img.logo{
					width: 200px;
					height: 150px;
					margin-bottom: 20px;
				}

				.table > tbody > tr > .highrow{
					border-top: 3px solid;
				}

				.well{
					box-shadow: none;
				}
			</style>
		</head>
		<body>
			<div class="container">
				<div class="row">
					<div class="span4 well">
						<div style="float: right;">
							<strong>Computer Information</strong><br />
							<?=$receipt['computer_information']?>
						</div>
						<table class="invoice-head">
							<tbody>
								<tr>
									<td class="pull-left"><strong>Receipt #</strong></td>
									<td><strong><?=$receipt['id']?></strong></td>
									<td class="pull-left"><strong>Customer's Number</strong></td>
									<td><?=$receipt['customer_number']?></td>
								</tr>
								<tr>
									<td class="pull-left"><strong>Estimated Collection Date</strong></td>
									<td><?=$receipt['estimated_collection']?></td>
									<td class="pull-left"><strong>Alternative Number</strong></td>
									<td><?=$receipt['alternative_number']?></td>
								</tr>
								<tr>
									<td class="pull-left"><strong>Amount Paid</strong></td>
									<td><strong>£<?=$receipt['amount_paid']?></strong></td>
									<td class="pull-left"><strong>Customer's Name</strong></td>
									<td><?=$receipt['customer_name']?></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-left"><strong>Customer's Email</strong></td>
									<td><?=$receipt['customer_email']?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="span8 well invoice-thank">
						<h5><strong>Note: </strong><?=$receipt['note']?></h5>
					</div>
				</div>
				<div class="row">
					<div class="span4 well">
						<div style="float: right;">
							<strong>Computer Condition</strong>
						</div>
						<table class="invoice-head">
							<tbody>
								<tr>
									<td class="pull-left"><strong>Scratches on Body</strong></td>
									<td><span class="glyphicon glyphicon-<?=$scratches_on_body?>"></span></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-left"><strong>Scratches on Screen</strong></td>
									<td><span class="glyphicon glyphicon-<?=$scratches_on_screen?>"></span></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-left"><strong>Battery Missing</strong></td>
									<td><span class="glyphicon glyphicon-<?=$battery_missing?>"></span></td>
								</tr>
								<tr>
									<td class="pull-left"><strong>Screws Missing</strong></td>
									<td><span class="glyphicon glyphicon-<?=$screws_missing?>"></span></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-left"><strong>Rubber Pad Missing</strong></td>
									<td><span class="glyphicon glyphicon-<?=$rubber_pad_missing?>"></span></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td class="pull-left"><strong>Keyboard Buttons Missing</strong></td>
									<td><span class="glyphicon glyphicon-<?=$keyboard_btns_missing?>"></span></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="pull-left"><strong>Charger Missing</strong></td>
									<td><span class="glyphicon glyphicon-<?=$charger_missing?>"></span></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php
				if($receipt['checked'] == 0){
					?>
					<div class="row">
						<div class="span8 well invoice-thank">
							<center><h5><strong>Computer has not been checked and verified internally!</strong></h5></center>
						</div>
					</div>
					<?php
				}
				?>
				<div class="row">
					<div class="span8">
						<h2>Receipt # <?=$receipt['id']?> for Technician</h2>
					</div>
				</div>
				<div class="row">
					<div class="span8 well invoice-body">
						<table class="table table-condensed">
							<thead>
								<tr>
									<th>Service</th>
									<th>Price</th>
									<th>Number Of Computers</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$receipt_items = $sql->query("SELECT * FROM `receipt_items` WHERE `receipt_id` = $receipt_id");
								while($receipt_item = $sql->fetch_array($receipt_items)){
									?>
									<tr>
										<td><?=page::getServiceInfo($receipt_item['service_id'], "title")?></td>
										<td>£<?=$receipt_item['price']?></td>
										<td><?=$receipt_item['computer_quantity']?></td>
										<td><strong>£<?=number_format((float)$receipt_item['computer_quantity']*$receipt_item['price'], 2, ".", "")?></strong></td>
									</tr>
									<?php
								}
								$item_total = $sql->fetch_array($sql->query("SELECT SUM(`price` * `computer_quantity`) AS `total_sum` FROM `receipt_items` WHERE `receipt_id` = $receipt_id"));
								$discount_reason = (empty($receipt['discount_reason'])) ? "" : "(" . $receipt['discount_reason'] . ")";
								?>
								<tr>
									<td class="highrow"></td>
									<td class="highrow"></td>
									<td class="highrow"><strong>Other Charges</strong></td>
									<td class="highrow"><strong>£<?=$receipt['other_charges']?></strong></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td><strong>Discount</strong> <?=$discount_reason?></td>
									<td><strong>£<?=$receipt['discount']?></strong></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td><strong>Total</strong></td>
									<td><strong>£<?=number_format((float)$item_total['total_sum'] + $receipt['other_charges'] - $receipt['discount'], 2, ".", "")?></strong></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</body>
		</html>
		<?php
	}
}else{
	header("Location: " . url . "/receipts");
}
?>