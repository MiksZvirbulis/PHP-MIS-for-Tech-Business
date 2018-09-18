<?php
$page = new page("SMS", 1);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$action = $pageInfo[2];
}else{
	$action = "sms";
}
?>
<?php if($action == "logs"): ?>
	<table class="table table-bordered">
		<thead>
			<th>Message ID</th>
			<th>Sent to</th>
			<th>Message</th>
			<th>Sent by</th>
			<th>Retries</th>
			<th>Status</th>
			<th>Sent on</th>
			<th>Responded on</th>
		</thead>
		<tbody>
			<?php
			
			$sms_logs_count = $sql->num_rows($sql->query("SELECT * FROM `orders`"));
			list($pagertop, $pagerbottom, $limit) = page::pagination(10, $sms_logs_count, url . "/sms/logs/page/", 4);
			$sms_logs = $sql->query("SELECT * FROM `sms_logs` ORDER BY `id` DESC $limit");
			?>
			<?php while($sms_log = $sql->fetch_array($sms_logs)): ?>
				<tr>
					<td><strong><?php echo $sms_log['message_id']; ?></strong></td>
					<td><?php echo "+" . $sms_log['phone_number']; ?></td>
					<td><?php echo $sms_log['message']; ?></td>
					<td><?php echo page::getUserInfo($sms_log['sent_by_id'], "name") . " " .  page::getUserInfo($sms_log['sent_by_id'], "surname"); ?></td>
					<td><?php echo $sms_log['retries']; ?></td>
					<td><strong><?php echo ucfirst($sms_log['status']); ?></strong></td>
					<td><?php echo date("d/m/Y H:i", $sms_log['time_sent']); ?></td>
					<td><?php echo (empty($sms_log['time_responded'])) ? "No response yet" : date("d/m/Y H:i", $sms_log['time_responded']); ?></td>
				</tr>
			<?php endwhile; ?>
			<tr>
				<td colspan="8"></td>
			</tr>
			<?php
			$delievered = $sql->fetch_array($sql->query("SELECT COUNT(`id`) AS `total` FROM `sms_logs` WHERE `status` = 'delivered'"));
			$unde_canc = $sql->fetch_array($sql->query("SELECT COUNT(`id`) AS `total` FROM `sms_logs` WHERE `status` = 'undelivered' OR `status` = 'canceled'"));
			$reje_expi = $sql->fetch_array($sql->query("SELECT COUNT(`id`) AS `total` FROM `sms_logs` WHERE `status` = 'rejected' OR `status` = 'expired'"));
			$unkown = $sql->fetch_array($sql->query("SELECT COUNT(`id`) AS `total` FROM `sms_logs` WHERE `status` = 'unknown' OR `status` = 'pending'"));
			?>
			<tr>
				<td>Delivered</td>
				<td><strong><?php echo $delievered['total']; ?></strong></td>
				<td>Undelivered or Canceled</td>
				<td><strong><?php echo $unde_canc['total']; ?></strong></td>
				<td>Rejected or Expired</td>
				<td><strong><?php echo $reje_expi['total']; ?></strong></td>
				<td>Unkown or Pending</td>
				<td><strong><?php echo $unkown['total']; ?></strong></td>
			</tr>
		</tbody>
	</table>
	<?php if($sms_logs_count > 0){ echo $pagerbottom; } ?>
<?php elseif($action == "templates"): ?>
	<?php if(isset($pageInfo[3]) AND $pageInfo[3] == "add"):
	if(isset($_POST['add_template'])){
		if(empty($_POST['description'])){
			$errors[] = "You forgot to enter the templates description!";
		}
		$errors = array();
		if(empty($_POST['type'])){
			$errors[] = "You forgot to select the templates type!";
		}
		if(empty($_POST['content'])){
			$errors[] = "You forgot to enter the templates content!";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$type = $sql->smart($_POST['type']);
			$description = $sql->smart($_POST['description']);
			$content = $sql->smart($_POST['content']);
			$sql->query("INSERT INTO `sms_templates` (`type`, `description`, `content`, `created_by_id`, `last_updated_by_id`) VALUES ($type, $description, $content, $global_user_id, $global_user_id)");
			page::alert("SMS template added successfully!", "success");
		}
	}
	?>
	<form method="POST" style="width: 500px; margin: 0 auto;">
		<input type="text" class="form-control" name="description" placeholder="Template description...">
		<select class="form-control" name="type">
			<option selected disabled>Select type</option>
			<option value="order">Order</option>
			<option value="receipt">Receipt</option>
		</select>
		<textarea type="text" class="form-control" name="content" rows="5" maxlength="160" placeholder="Template content..."></textarea>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm" name="add_template">Add template</button>
		</div>
	</form>
<?php elseif(isset($pageInfo[3]) AND $pageInfo[3] == "edit"): ?>
	<?php
	if(isset($pageInfo[4]) AND !empty($pageInfo[4])){
		$id = (int)$pageInfo[4];
		$find_template = $sql->num_rows($sql->query("SELECT `id` FROM `sms_templates` WHERE `id` = $id"));
		if($find_template == 0){
			page::alert("No template found with that ID!", "danger");
		}else{
			if(isset($_POST['edit_template'])){
				if(empty($_POST['description'])){
					$errors[] = "You forgot to enter the templates description!";
				}
				$errors = array();
				if(empty($_POST['type'])){
					$errors[] = "You forgot to select the templates type!";
				}
				if(empty($_POST['content'])){
					$errors[] = "You forgot to enter the templates content!";
				}
				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$type = $sql->smart($_POST['type']);
					$description = $sql->smart($_POST['description']);
					$content = $sql->smart($_POST['content']);
					$sql->query("UPDATE `sms_templates` SET `type` = $type, `description` = $description, `content` = $content, `last_updated_by_id` = $global_user_id WHERE `id` = $id");
					page::alert("SMS template added successfully!", "success");
				}
			}
			$template = $sql->fetch_array($sql->query("SELECT * FROM `sms_templates` WHERE `id` = $id"));
			?>
			<form method="POST" style="width: 500px; margin: 0 auto;">
				<input type="text" class="form-control" name="description" placeholder="Template description..." value="<?php echo $template['description']; ?>">
				<select class="form-control" name="type">
					<option selected disabled>Select type</option>
					<option value="order"<?php echo ($template['type'] == "order") ? " selected" : ""; ?>>Order</option>
					<option value="receipt"<?php echo ($template['type'] == "receipt") ? " selected" : ""; ?>>Receipt</option>
				</select>
				<textarea type="text" class="form-control" name="content" rows="5" maxlength="160" placeholder="Template content..."><?php echo $template['content']; ?></textarea>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="edit_template">Edit template</button>
				</div>
			</form>
			<?php }
		}else{
			page::alert("No ID specified!", "danger");
		}
		else: ?>
		<table class="table table-bordered">
			<thead>
				<th>#</th>
				<th>Description</th>
				<th>Type</th>
				<th>Template</th>
				<th>Created by</th>
				<th>Actions</th>
			</thead>
			<tbody>
				<?php $sms_templates = $sql->query("SELECT * FROM `sms_templates` ORDER BY `id` ASC"); ?>
				<?php $i = 1; ?>
				<?php while($sms_template = $sql->fetch_array($sms_templates)): ?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $sms_template['description']; ?></td>
						<td><?php echo ucfirst($sms_template['type']); ?></td>
						<td><?php echo $sms_template['content']; ?></td>
						<td><?php echo page::getUserInfo($sms_template['created_by_id'], "name") . " " . page::getUserInfo($sms_template['created_by_id'], "surname"); ?></td>
						<td>
							<a href="<?=url?>/sms/templates/edit/<?php echo $sms_template['id']; ?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Edit</button></a>
						</td>
					</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
	<?php endif; ?>
<?php elseif($action == "send"):
	$errors = array();
	if(isset($pageInfo[4]) AND !empty($pageInfo[4]) AND is_numeric($pageInfo[4]) AND strlen($pageInfo[4]) == 6 AND isset($pageInfo[3]) AND !empty($pageInfo[3])){
		$sms_id = (int)$pageInfo[3];
		$id = (int)$pageInfo[4];
		$find_template = $sql->query("SELECT * FROM `sms_templates` WHERE `id` = $sms_id");
		if($sql->num_rows($find_template) == 0){
			$errors[] = "Template not found";
		}else{
			$template = $sql->fetch_array($find_template);
			if($template['type'] == "receipt"){
				$find_receipt = $sql->query("SELECT * FROM `receipts` WHERE `id` = $id");
				if($sql->num_rows($find_receipt) == 0){
					$errors[] = "Receipt not found";
				}else{
					$receipt = $sql->fetch_array($find_receipt);
					if(empty($receipt['customer_number'])){
						$errors[] = "No mobile number specified. No one to send to!";
					}else{
						# Definitions
						$from = array(
							"FIRST_LAST",
							"RECEIPT_NO",
							"STATUS"
							);
						$to = array(
							$receipt['customer_name'] . " " . $receipt['customer_surname'],
							$receipt['id'],
							$page->receipt_status[$receipt['status']]
							);
						$content = $template['content'];
						$content = strip_tags($content);
						$content = str_replace($from, $to, $content);
						try{
							$sms = new Text2reach_SMS_Bulk(text2reach_api_key);
							$sms->from = "Tech House";
							$sms->phone = "44" . substr($receipt['customer_number'], 1);
							$sms->message = $content;
							$sms->report_url = url . "/sms_report.php";;
							$sms->send();

							$message_id = $sms->id;
							$phone_number = $sql->smart("44" . substr($receipt['customer_number'], 1));
							$message = $sql->smart($content);
							$time = $sql->smart(time());
							$sql->query("INSERT INTO `sms_logs` (`message_id`, `phone_number`, `message`, `sent_by_id`, `status`, `time_sent`) VALUES ($message_id, $phone_number, $message, $global_user_id, 'pending', $time)");
							?>
							<script type="text/javascript">
								window.close();
							</script>
							<?php
						}
						catch(Text2reach_Exception $e){
							$errors[] = $e->getMessage();
							$errors[] = $page->sms_errors[$sms->response()];
						}
					}
				}
			}else{
				$find_order = $sql->query("SELECT * FROM `orders` WHERE `order_id` = $id");
				if($sql->num_rows($find_order) == 0){
					$errors[] = "Order not found";
				}else{
					$order = $sql->fetch_array($find_order);
					if(empty($order['mobile_number'])){
						$errors[] = "No mobile number specified. No one to send to!";
					}else{
						$from = array(
							"FIRST_LAST",
							"SHIPPING_ADDRESS"
							);
						$to = array(
							$order['first_name'] . " " . $order['last_name'],
							$shipping_address
							);
						$content = $template['content'];
						$content = strip_tags($content);
						$content = str_replace($from, $to, $content);
						try{
							$sms = new Text2reach_SMS_Bulk(text2reach_api_key);
							$sms->from = "Tech House";
							$sms->phone = "44" . substr($order['mobile_number'], 1);
							$sms->message = $content;
							$sms->report_url = url . "/sms_report.php";;
							$sms->send();

							$message_id = $sms->id;
							$phone_number = $sql->smart("44" . substr($order['mobile_number'], 1));
							$message = $sql->smart($content);
							$time = $sql->smart(time());
							$sql->query("INSERT INTO `sms_logs` (`message_id`, `phone_number`, `message`, `sent_by_id`, `status`, `time_sent`) VALUES ($message_id, $phone_number, $message, $global_user_id, 'pending', $time)");
							?>
							<script type="text/javascript">
								window.close();
							</script>
							<?php
						}
						catch(Text2reach_Exception $e){
							$errors[] = $e->getMessage();
							$errors[] = $page->sms_errors[$sms->response()];
						}
					}
				}
			}
		}
	}else{
		$errors[] = "Nothing specified";
	}
	if(count($errors) > 0):
		foreach($errors as $error):
			page::alert($error, "danger");
		endforeach;
		endif;
		else:
			if(isset($_POST['send'])){
				$errors = array();

				if(empty($_POST['phone_number'])){
					$errors[] = "You forgot to enter the phone number!";
				}else{
					if($_POST['prefix'] == "44"){
						if(strlen($_POST['phone_number']) != 10){
							$errors[] = "The phone number entered does not meet 10 digits!";
						}
					}else{
						if(strlen($_POST['phone_number']) != 8){
							$errors[] = "The phone number entered does not meet 8 digits!";
						}
					}
				}

				if(empty($_POST['message'])){
					$errors[] = "You forgot to enter the message!";
				}else{
					if(strlen($_POST['message']) > 160){
						$errors[] = "The message is longer than the 160 character limit!";
					}
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					try{
						$sms = new Text2reach_SMS_Bulk(text2reach_api_key);
						$sms->from = $_POST['from'];
						$sms->phone = $_POST['prefix'] . $_POST['phone_number'];
						$sms->message = $_POST['message'];
						$sms->report_url = url . "/sms_report.php";;
						$sms->send();
						page::alert("Message requested to be sent with ID: " . $sms->id, "success");
						$message_id = $sms->id;
						$phone_number = $sql->smart($_POST['prefix'] . $_POST['phone_number']);
						$message = $sql->smart($_POST['message']);
						$time = $sql->smart(time());
						$sql->query("INSERT INTO `sms_logs` (`message_id`, `phone_number`, `message`, `sent_by_id`, `status`, `time_sent`) VALUES ($message_id, $phone_number, $message, $global_user_id, 'pending', $time)");
					}
					catch(Text2reach_Exception $e){
						page::alert($e->getMessage(), "danger");
						page::alert($page->sms_errors[$sms->response()], "danger");
					}
				}
			}
			?>
			<form method="POST" class="form-inline" style="width: 550px; margin: 0 auto;">
				<div class="form-group" style="width: 100%;">
					<select class="form-control" name="from" style="width: 100%;">
						<option value="Tech House" selected>Tech House</option>
						<option value="Arbico">Arbico</option>
					</select>           
				</div>
				<div class="input-group" style="width: 100%; margin: 10px auto;">
					<span class="input-group-addon"><span class="glyphicon glyphicon-earphone"></span></span>
					<div class="form-group">
						<select class="form-control" name="prefix">
							<option value="44" selected>+44</option>
							<option value="371">+371</option>
						</select>           
					</div>
					<div class="form-group" style="width: 428px;">
						<input type="text" class="form-control" name="phone_number" placeholder="Enter the phone number" style="width: 100%;">       
					</div>
				</div>
				<textarea type="text" class="form-control" name="message" placeholder="Enter the message... limited to 160 characters!" maxlength="160" rows="5" style="width: 100%;"></textarea>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="send">Send</button>
				</div>
			</form>
		<?php endif;