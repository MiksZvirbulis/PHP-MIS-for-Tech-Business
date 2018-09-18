<?php
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$order_id = (int)$pageInfo[2];
	$query = $sql->query("SELECT * FROM `orders` WHERE `order_id` = $order_id");
	if($sql->num_rows($query) == 0){
		header("Location: " . url . "/orders");
	}else{
		$order = $sql->fetch_array($query);
		$invoice = $sql->fetch_array($sql->query("SELECT `invoice_number` FROM `invoices` WHERE `order_id` = $order_id"));
		if(empty($invoice['invoice_number'])){
			$invoice_number = "Proforma";
		}else{
			$invoice_number = "TH-" . $invoice['invoice_number'];
		}
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="utf-8">
			<title><?php echo $invoice_number; ?></title>
			<link rel="stylesheet" href="<?=url?>/assets/css/bootstrap.css">
			<style>
				body, html{
					margin: 0 auto;
					width: 1000px;
					font-size: 9pt;
				}

				.invoice-head td{
					padding: 0 4px;
				}

				.invoice-body{
					background-color: transparent;
				}

				.invoice-thank{
					padding: 2px;
				}

				address{
					margin-top: 15px;
					float: right;
					font-size: 8pt;
					line-height: 1.1;
				}

				img.logo{
					width: 160px;
					height: 120px;
				}

				.table > tbody > tr > .highrow{
					border-top: 3px solid;
				}

				.well{
					box-shadow: none;
					margin-bottom: 0;
					padding: 6px;
				}

				h2{
					margin-top: 10px;
					margin-bottom: 10px;
				}

				.clear{
					content: "";
					display: block;
					clear: both;
				}

				.table{
					margin-bottom: 0;
				}

				.container{
					height: 1400px;
					position: relative;
				}

				.proforma{
					opacity: 0.5;
					position: absolute;
					overflow: hidden;
					z-index: 99999;
					top: 550px;
					right: -100px;
					left: auto;
					-webkit-transform: rotate(-50deg);
					-moz-transform: rotate(-50deg);
					-ms-transform: rotate(-50deg);
					-o-transform: rotate(-50deg);
					transform: rotate(-50deg);
					-webkit-transform-origin: 50% 50%;
					-moz-transform-origin: 50% 50%;
					-ms-transform-origin: 50% 50%;
					-o-transform-origin: 50% 50%;
					transform-origin: 50% 50%;
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation = 3);
					font-size: 160pt;
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
							UK Company No. 08160544<br />
							VAT No. 140 7487 14<br />
							<span class="glyphicon glyphicon-home"></span> 96 Sydenham Road,<br />
							Sydenham, London<br />
							SE26 5JX<br />
							<span class="glyphicon glyphicon-phone"></span> 020 8778 0090<br />
							<span class="glyphicon glyphicon-globe"></span> www.tech-house.co.uk
						</address>
					</div>
					<div class="span4 well invoice-thank">
						<table class="invoice-head pull-left">
							<tbody>
								<tr>
									<td class="pull-left"><strong>Customer</strong></td>
									<td><?=$order['first_name']?> <?=$order['last_name']?></td>
								</tr>
								<?php
								if(!empty($order['company_name'])){
									?>
									<tr>
										<td class="pull-left"><strong>Company</strong></td>
										<td><?=$order['company_name']?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
						<div class="pull-right">
							<strong>Billing Address</strong>
							<?php
							if(!empty($order['billing_line1'])){
								echo "<br />" . $order['billing_line1'] . "<br />";
							}
							if(!empty($order['billing_line2'])){
								echo $order['billing_line2'] . "<br />";
							}
							if(!empty($order['billing_line3'])){
								echo $order['billing_line3'] . "<br />";
							}
							if(!empty($order['billing_line4'])){
								echo $order['billing_line4'] . "<br />";
							}
							if(!empty($order['billing_postcode'])){
								echo $order['billing_postcode'];
							}
							?>
						</div>
						<div class="pull-right" style="margin-right: 200px;">
							<strong>Shipping Address</strong>
							<?php
							if(!empty($order['shipping_line1'])){
								echo "<br />" . $order['shipping_line1'] . "<br />";
							}
							if(!empty($order['shipping_line2'])){
								echo $order['shipping_line2'] . "<br />";
							}
							if(!empty($order['shipping_line3'])){
								echo $order['shipping_line3'] . "<br />";
							}
							if(!empty($order['shipping_line4'])){
								echo $order['shipping_line4'] . "<br />";
							}
							if(!empty($order['shipping_postcode'])){
								echo $order['shipping_postcode'] ;
							}
							?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="row">
					<div class="span8">
						<h2>Invoice <?=$invoice_number?><span class="pull-right"><?=$order['invoice_date']?></span></h2>
					</div>
				</div>
				<div class="row">
					<div class="span8 well invoice-body">
						<table class="table table-condensed">
							<thead>
								<tr>
									<th>Description</th>
									<th>Price</th>
									<th class="text-center">Quantity</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$find_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id ORDER BY `type` DESC");
								$spec_total = 0;
								$item_total = 0;
								while($spec = $sql->fetch_array($find_specs)){
									$spec_id = $sql->smart($spec['spec_id']);
									$find_items = $sql->query("SELECT * FROM `order_items` WHERE `order_spec_id` = $spec_id ORDER BY `item_id` ASC");
									$type = ($spec['type'] == "pc") ? "PC-" . $i . ":" : "";
									if($spec['type'] == "pc"){
										$ii = 1;
										$count_items = $sql->num_rows($find_items);
										?>
										<tr>
											<td>
												<b><?=$type?></b>
												<?php
												while($item = $sql->fetch_array($find_items)){
													if($item['quantity'] == 1){
														echo page::getItemInfo($item['upc'], "public");
														if($ii < $count_items){
															echo ", ";
														}
													}else{
														echo $item['quantity'] . " x " . page::getItemInfo($item['upc'], "public");
														if($ii < $count_items){
															echo ", ";
														}
													}
													$ii++;
												}
												?>
											</td>
											<td width="60px"><?=$spec['cost_exc_vat']?></td>
											<td width="60px" class="text-center"><?=$spec['quantity']?></td>
											<td width="60px"><strong>£<?=number_format((float)($spec['quantity'] * $spec['cost_exc_vat']), 2, ".", "")?></strong></td>
										</tr>
										<?php
										$spec_total += number_format((float)($spec['quantity'] * $spec['cost_exc_vat']), 2, ".", "");
									}else{
										while($item = $sql->fetch_array($find_items)){
											?>
											<tr>
												<?php
												if($spec['type'] == "aservices"){
													?>
													<td><?=page::getServiceInfo($item['service_id'], "description")?></td>
													<?php
												}else{
													?>
													<td><?=page::getItemInfo($item['upc'], "public")?></td>
													<?php
												}
												?>
												<td width="60px"><?=$item['cost_exc_vat']?></td>
												<td width="60px" class="text-center"><?=$item['quantity']?></td>
												<td width="60px"><strong>£<?=number_format((float)($item['quantity'] * $item['cost_exc_vat']), 2, ".", "")?></strong></td>
											</tr>
											<?php
											$item_total += number_format((float)($item['quantity'] * $item['cost_exc_vat']), 2, ".", "");
										}
									}
									$i++;
								}
								if($order['warranty'] != 8){
									$warranty_id = $sql->smart($order['warranty']);
									$warranty = $sql->fetch_array($sql->query("SELECT * FROM `warranty_options` WHERE `id` = $warranty_id"));
									?>
									<tr>
										<td colspan="4"><strong>Warranty: <?=$warranty['description']?></strong></td>
									</tr>
									<?php
								}
								$notes = $sql->query("SELECT * FROM `order_notes` WHERE `order_id` = $order_id AND `invoice` = 1 ORDER BY `date` ASC");
								if($sql->num_rows($notes) > 0){
									while($note = $sql->fetch_array($notes)){
										?>
										<tr>
											<td colspan="4"><?=$note['note']?></td>
										</tr>
										<?php
									}
								}
								?>
								<tr>
									<td class="highrow" width="80%" style="padding: 0"></td>
									<td class="highrow" colspan="2" width="200px" style="padding: 0"></td>
									<td class="highrow" width="60px" style="padding: 0"></td>
								</tr>
								<?php if($order['shipping_exc_vat'] != "0.00"): ?>
									<tr>
										<td width="80%"></td>
										<td colspan="2" class="highrow" width="200px"><strong>Delivery Charges</strong></td>
										<td width="60px"><strong>£<?=$order['shipping_exc_vat']?></strong></td>
									</tr>
								<?php endif; ?>
								<?php if($order['other_exc_vat'] != "0.00"): ?>
									<tr>
										<td></td>
										<td colspan="2"><strong>Other Charges</strong></td>
										<td><strong>£<?php echo number_format((float)$order['other_exc_vat'], 2, ".", ""); ?></strong></td>
									</tr>
								<?php endif; ?>
								<?php if($order['upgrades_exc_vat'] != "0.00"): ?>
									<tr>
										<td></td>
										<td colspan="2"><strong>Upgrades</strong></td>
										<td><strong>£<?php echo number_format((float)$order['upgrades_exc_vat'], 2, ".", ""); ?></strong></td>
									</tr>
								<?php endif; ?>
								<tr>
									<td></td>
									<td colspan="2"><strong>Sub Total</strong></td>
									<td><strong>£<?php echo number_format((float)($order['other_exc_vat'] + $order['shipping_exc_vat'] + $item_total + $spec_total), 2, ".", ""); ?></strong></td>
								</tr>
								<tr>
									<td></td>
									<td colspan="2"><strong>VAT @ <?php echo $order['vat']; ?>%</strong></td>
									<td><strong>£<?php echo number_format((float)((($order['other_exc_vat'] + $order['shipping_exc_vat'] + $item_total + $spec_total) * page::multiplyVAT($order['vat'])) - ($order['other_exc_vat'] + $order['shipping_exc_vat'] + $item_total + $spec_total)), 2, ".", ""); ?></strong></td>
								</tr>
								<tr>
									<td></td>
									<td colspan="2"><strong>Total Payable</strong></td>
									<td><strong>£<?php echo number_format((float)(($order['other_exc_vat'] + $order['shipping_exc_vat'] + $order['upgrades_exc_vat'] + $item_total + $spec_total) * page::multiplyVAT($order['vat'])), 2, ".", ""); ?></strong></td>
									<?php
									$amount = $sql->fetch_array($sql->query("SELECT SUM(`amount`) AS `amound_paid` FROM `payment_entries` WHERE `order_id` = $order_id"));
									if($amount['amound_paid'] == 0){
										?>
										<div class="proforma">PROFORMA</div>
										<?php
									}
									?>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" style="text-align: justify; font-size: 10pt; position: absolute; bottom: 0; width: 995px; left: auto; right: auto;">
					<p>The above order is subject to our Terms and Conditions a printed copy of which is available upon request. You can also request a copy of them via email or view them on our website at the following address: www.tech-house.co.uk/terms_conditions</p>
					<p>Unless otherwise stated in the invoice above, there is no exchange or refund on any item. Any items supplied should be checked upon collection. We will not entertain any claims for defect, damage or missing items once the goods have been collected / delivered. If the goods supplied to you develop a defect while under warranty or you have any other complaint about the goods, you should notify us in writing via email as soon as possible, but in any event within 3 days of the date you discovered or ought to have discovered the damage, defect or complaint. All faults / problems or defects will be taken to have occurred on the date they are actually reported to us.</p>
					<p><strong>WARRANTY:</strong> Items are only provided with a return to base warranty (if any) for the period specified in this invoice starting from the invoice date. If there is no mention of a Warranty period in the invoice above, then the item is deemed to have been provided without any warranty. Our Warranty only covers problems related to defective hardware supplied by us. The Warranty does not cover any software / networking or other configuration issues, nor does it cover problems arising due to incompatibility with the customers own hardware or software. Any services performed (such as RAID Setups, Overclocking Setups etc, Software Installations, Optimizations, Repairs etc.) are not provided with any warranty. Software or faults unrelated to the system hardware are also excluded from the warranty. In all cases of a hardware fault we will endeavour to repair the defective part(s) or replace them with items of similar specification per our discretion. Should a returned item be found to have no fault then a £25.00 inspection charge will be levied. This warranty does not apply to any cosmetic or other damage to the outer casing of the item, defect in the goods arising from reasons other than fair wear and tear i.e. wilful damage, physical damage, accidental damage, negligence by you or any third party, use other than as recommended by the Supplier, failure to use suitable Electric Surge Protection equipment or follow the Supplier`s instructions. Any alteration or repairs carried out by the customer or third parties are carried out at the customers own cost and risk. Furthermore any such repairs will invalidate the warranty unless otherwise approved by Tech House in writing.</p>
					<p>It is the responsibility of the buyer to return and collect goods to / from the Tech House’s premises. Any items that have not been collected within 12 weeks of the advised collection date will be disposed off and the customer shall not have any recourse to the Tech House after this.</p>
					<p>Tech House will remains the owner of all goods / iems receive for repair / service until the Tech House has been paid in full for the goods supplied / work done / services rendered.</p>
					<p>In case of items received for repair / service, Tech House does not accept responsibility for any failures to the buyer’s components within its possession. The condition of the items cannot be ascertained and therefore deemed to be taken as ""unchecked"" at the time of receipt of the items. The condition of the items will as received by Tech House will be that which is advised upon completion of the Diagnostics. Tech House will only be responsible for the condition of the items after the completion of the Diagnostic stage. Tech House does not accept responsibility for the loss of any software or data stored on any system or storage media handed to Tech House for inspection, repair, upgrade or other.</p>
					<p>When agreeing to system reinstall or reset to factory defaults, the customer accepts that all data, personalizations, programs and settings will be wiped and unrecoverable. In all cases of Operating System re-install the customer will have to reinstall all programs that were on computer from their own back up source and Tech House will not be obliged offer any advise or assistance in this regard. The customer is responsible to maintain suitable backup of their data and ensure that they have copies of any license keys needed to reinstall their programs prior to delivering any items to Tech House for repair. In cases where the customer has paid for data recovery, we cannot guarantee the success or the extent of success of such recovery task. In all cases we can only recover documents, pictures, audio and video files.</p>
					<p>In all cases Tech House will not be responsible to the maximum possible extent for any losses, incidental or otherwise (e.g. Data Loss, Damage to Property or Equipment, Productivity / Time / Revenue Loss etc.), arising as a result of failure of our products or performance of our service. Irrespective of whether you are an end user / consumer, a business or trade customer, Tech House shall not be liable to you for any indirect or consequential loss or damage (whether for loss of profit, loss of business, depletion of goodwill or otherwise), costs, expenses or other claims for consequential compensation whatsoever (howsoever caused) which arise out of or in connection with this invoice.</p>      			
				</div>
			</div>
		</body>
		</html>
		<?php
	}
}else{
	header("Location: " . url . "/orders");
}
?>