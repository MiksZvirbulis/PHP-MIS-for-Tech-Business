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
					<div class="span4">
						<img src="<?=url?>/assets/images/logo.png" class="logo">
						<address>
							<strong>Tech House Trading Limited</strong><br />
							<span class="glyphicon glyphicon-home"></span> 96 Sydenham Road,<br />
							Sydenham, London<br />
							SE26 5JX<br />
							<span class="glyphicon glyphicon-phone"></span> 02087780090<br />
							<span class="glyphicon glyphicon-envelope"></span> info@tech-house.co.uk<br />
							<span class="glyphicon glyphicon-globe"></span> http://tech-house.co.uk
						</address>
					</div>
					<div class="span4 well">
						<div style="float: right;">
							<strong>Computer Information</strong><br />
							<?=nl2br($receipt['computer_information'])?>
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
									<td><?=$receipt['customer_name']?> <?=$receipt['customer_surname']?></td>
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
				<?php
				if(!empty($receipt['customer_note'])){
					?>
					<div class="row">
						<div class="span8 well invoice-thank">
							<h5><strong>Note: </strong><?=$receipt['customer_note']?></h5>
						</div>
					</div>
					<?php
				}
				?>
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
						<h2>Receipt # <?=$receipt['id']?></h2>
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
				<div class="row">
					<div class="span8 well invoice-thank">
						<h5 style="text-align: center;"><strong>Please bring this receipt when collecting your computer!</strong></h5>
					</div>
				</div>
				<div class="row" style="font-size: 10pt;">
					Terms for Repair & Services undertaken by Tech House:<br />
					Tech House does not accept responsibility for the loss of any software or data stored on any system or storage media handed to Tech House for inspection, repair, upgrade or other.<br />
					All Software including Operating system and Antivirus, Consumables, repair and data recovery services are non refundable and are not provided with any Warranty unless stated in invoice.<br />
					It is the responsibility of the buyer to return and collect goods to / from the Tech House’s premises. Any items that have not been collected within 12 weeks of the advised collection date will be disposed off and the customer shall not have any recourse to the Tech House after this.<br />
					Tech House will remains the owner of all goods / items received for repair / service until the Tech House has been paid in full for the goods supplied / work done / services rendered.<br />
					Tech House reserves the right to levy charges for any of the following services:<br />
					i. Inspection of any piece of computer equipment and installation / replacement of either software or hardware.<br />
					ii. Diagnosis and testing of any problems encountered with any piece of hardware or software<br />
					iii. Recovery of any hardware or software problems and backup of any data when requested by the customer.<br />
					When items are received for repair / service, Tech House does not accept responsibility for any failures to the buyer’s components within its possession.<br />
					Tech House will only be responsible for the condition of the items after the completion of the Diagnostic Checks.<br />
					Tech House does not accept responsibility for the loss of any software or data stored on any system or storage media handed to Tech House for inspection, repair, upgrade or other.<br />
					When agreeing to system reinstall or reset to factory defaults, the customer accepts that all data, personalization, programs and settings will be wiped and unrecoverable.<br />
					Any repairs conducted or work done is provided with a return to base warranty (if any) for the period specified in this invoice starting from the invoice date. If there is no mention of a Warranty period in the invoice provided, then the item is deemed to have been supplied as seen without any warranty.<br />
					Software or faults unrelated to the items supplied / work done are also excluded from the warranty.<br />
					The Supplier will take all reasonable precautions to keep the details of your order and payment secure but unless the Supplier is negligent, the Supplier will not be liable for unauthorised access to information supplied by you.<br />
					Please visit http://www.tech-house.co.uk/terms_conditions for detail information.
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