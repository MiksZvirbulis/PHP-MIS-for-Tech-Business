<?php
if(isset($pageInfo[2]) AND !empty($pageInfo[2])):
	$order_id = (int)$pageInfo[2];
$query = $sql->query("SELECT * FROM `orders` WHERE `order_id` = $order_id");
if($sql->num_rows($query) == 0):
	header("Location: " . url . "/orders");
else:
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
	<title>Built Specification</title>
	<link rel="stylesheet" href="http://mis9.tech-house.co.uk/assets/css/bootstrap.css">
	<style>
		body, html{
			margin: 0 auto;
			width: 1000px;
			font-size: 10pt;
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
			position: relative;
		}

		@media print{
			.container{ 
				page-break-after: always;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span4 well invoice-thank">
				<table class="table table-bordered invoice-head">
					<tbody>
						<tr>
							<td><strong>Name</strong></td>
							<td><?php echo $order['first_name'] . " " . $order['last_name']; ?></td>
							<td><strong>Email</strong></td>
							<td><?php echo $order['email']; ?></td>
							<td><strong>Phone Number</strong></td>
							<td colspan="3"><strong>Mo:</strong> <?php echo $order['mobile_number']; ?> <span class="pull-right"><strong>H:</strong> <?php echo $order['home_number']; ?></span></td>
						</tr>
						<tr>
							<td><strong>Order ID</strong></td>
							<td><?php echo $order['order_id']; ?></td>
							<td><strong>Invoice Number</strong></td>
							<td><?php echo $invoice_number; ?></td>
							<td><strong>Invoice Date</strong></td>
							<td><?php echo $order['invoice_date']; ?></td>
							<td><strong>Shipping Date</strong></td>
							<td><?php echo date("d/m/Y", $order['shipment_date']); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<h2>PC-1</h2>
		<div class="row">
			<div class="span8 well invoice-body">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>UPC</th>
							<th>Item</th>
							<th>Batch Nr</th>
							<th>Serial Nr</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$find_specs = $sql->query("SELECT * FROM `order_specs` WHERE `order_id` = $order_id ORDER BY `type` DESC");
						while($spec = $sql->fetch_array($find_specs)){
							$spec_id = $sql->smart($spec['spec_id']);
							$find_items = $sql->query("SELECT * FROM `order_items` WHERE `order_spec_id` = $spec_id ORDER BY `item_id` ASC");
							$type = ($spec['type'] == "pc") ? "PC-" . $i . ":" : "";
							if($spec['type'] == "pc"){
								$count_items = $sql->num_rows($find_items);
								while($item = $sql->fetch_array($find_items)){
									$quantity = $item['quantity'];
									?>
									<tr>
										<td width="5px" style="vertical-align: middle;"><?php echo $i; ?></td>
										<td width="30px" style="vertical-align: middle;"><?php echo $item['upc']; ?></td>
										<td>
											<?php
											for($ii = 1; $ii <= $quantity; $ii++){
												if($item['quantity'] == 1){
													echo page::getItemInfo($item['upc'], "description");
													if($ii < $count_items){
														echo "<br />";
													}
												}else{
													echo page::getItemInfo($item['upc'], "description");
													if($ii < $count_items){
														echo "<br />";
													}
												}
											}
											?>
										</td>
										<td width="80px"></td>
										<td width="300px">
											<?php
											for($ii = 1; $ii <= $quantity; $ii++){
												if($item['quantity'] == 1){
												}else{
													echo "S" . $ii . ":";
													if($ii < $count_items){
														echo "<br />";
													}
												}
											}
											?>
										</td>
									</tr>
									<?php
									$i++;
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<h2>Builders Note:</h2>
		<div class="well" style="background-color: #ffffff; padding: 80px;"></div>
		<h2>Licencing:</h2>
		<div class="well" style="background-color: #ffffff; padding: 50px;"></div>
	</div>
	<div class="container">
		<div class="row">
			<div class="span4 well invoice-thank">
				<table class="table table-bordered invoice-head">
					<tbody>
						<tr>
							<td><strong>Name</strong></td>
							<td><?php echo $order['first_name'] . " " . $order['last_name']; ?></td>
							<td><strong>Email</strong></td>
							<td><?php echo $order['email']; ?></td>
							<td><strong>Phone Number</strong></td>
							<td colspan="3"><strong>Mo:</strong> <?php echo $order['mobile_number']; ?> <span class="pull-right"><strong>H:</strong> <?php echo $order['home_number']; ?></span></td>
						</tr>
						<tr>
							<td><strong>Order ID</strong></td>
							<td><?php echo $order['order_id']; ?></td>
							<td><strong>Invoice Number</strong></td>
							<td><?php echo $invoice_number; ?></td>
							<td><strong>Invoice Date</strong></td>
							<td><?php echo $order['invoice_date']; ?></td>
							<td><strong>Shipping Date</strong></td>
							<td><?php echo date("d/m/Y", $order['shipment_date']); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<h2>Order Note</h2>
		<?php $notes = $sql->query("SELECT * FROM `order_notes` WHERE `order_id` = $order_id ORDER BY `id`"); ?>
		<?php while($note = $sql->fetch_array($notes)){ ?>
		<div class="well" style="background-color: #ffffff;"><?php echo nl2br($note['note']); ?></div>
		<?php } ?>
	</div>
</body>
</html>
<?php
endif;
else:
	header("Location: " . url . "/orders");
endif;
?>