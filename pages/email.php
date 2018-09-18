<?php
$page = new page("Email", 3);
$global_user_id = $_COOKIE['user_id'];
$errors = array();
if(isset($pageInfo[3]) AND !empty($pageInfo[3]) AND is_numeric($pageInfo[3]) AND strlen($pageInfo[3]) == 6 AND isset($pageInfo[2]) AND !empty($pageInfo[2])){
	$email_id = (int)$pageInfo[2];
	$id = (int)$pageInfo[3];
	$find_template = $sql->query("SELECT * FROM `email_templates` WHERE `id` = $email_id");
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
				if(empty($receipt['customer_email'])){
					$errors[] = "No email address specified. No one to send to!";
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
						# Definitions
					$subject = str_replace($from, $to, $template['subject']);
					$content = $template['content'];
					$content = nl2br($content);
					$content = str_replace($from, $to, $content);
					$output = "<!DOCTYPE html><html><body>$content</html></body>";
					# Logging
					$sent_to = $sql->smart($receipt['customer_email']);
					$time = $sql->smart(time());
					# Logging
					if($page->sendEmail($receipt['customer_email'], "info@tech-house.co.uk", $subject, $output)){
						$sql->query("INSERT INTO `email_logs` 
							(`subject`, `parent_type`, `parent_id`, `sent_to`, `time`, `sent_by_id`, `sent`)
							VALUES
							('$subject', 'receipt', $id, $sent_to, $time, $global_user_id, 1)
							");
							?>
							<script type="text/javascript">
								window.close();
							</script>
							<?php
						}else{
							$sql->query("INSERT INTO `email_logs` 
								(`subject`, `parent_type`, `parent_id`, `sent_to`, `time`, `sent_by_id`, `sent`)
								VALUES
								('$subject', 'receipt', $id, $sent_to, $time, $global_user_id, 0)
								");
							$errors[] = "Email not sent";
						}
					}
				}
			}else{
				$find_order = $sql->query("SELECT * FROM `orders` WHERE `order_id` = $id");
				if($sql->num_rows($find_order) == 0){
					$errors[] = "Order not found";
				}else{
					$order = $sql->fetch_array($find_order);
					if(empty($order['email'])){
						$errors[] = "No email address specified. No one to send to!";
					}else{
					# Definitions
						$shipping_address = "";
						if(!empty($order['shipping_line1'])){
							$shipping_address .= $order['shipping_line1'] . "<br />";
						}
						if(!empty($order['shipping_line2'])){
							$shipping_address .= $order['shipping_line2'] . "<br />";
						}
						if(!empty($order['shipping_line3'])){
							$shipping_address .= $order['shipping_line3'] . "<br />";
						}
						if(!empty($order['shipping_line4'])){
							$shipping_address .= $order['shipping_line4'] . "<br />";
						}
						if(!empty($order['shipping_postcode'])){
							$shipping_address .= $order['shipping_postcode'] ;
						}
						$from = array(
							"FIRST_LAST",
							"SHIPPING_ADDRESS",
							"ORDER_NO",
							"STATUS"
							);
						$to = array(
							$order['first_name'] . " " . $order['last_name'],
							$shipping_address,
							$order['order_id'],
							$page->order_status[$order['order_status']]
							);
							# Definitions
						$subject = str_replace($from, $to, $template['subject']);
						$content = $template['content'];
						$content = nl2br($content);
						$content = str_replace($from, $to, $content);
						$output = "<!DOCTYPE html><html><body>$content</html></body>";
						# Logging
						$sent_to = $sql->smart($receipt['customer_email']);
						$time = $sql->smart(time());
						# Logging
						if($page->sendEmail($order['email'], "info@tech-house.co.uk", $subject, $output)){
							$sql->query("INSERT INTO `email_logs` 
								(`subject`, `parent_type`, `parent_id`, `sent_to`, `time`, `sent_by_id`, `sent`)
								VALUES
								('$subject', 'order', $id, $sent_to, $time, $global_user_id, 1)
								");
								?>
								<script type="text/javascript">
									window.close();
								</script>
								<?php
							}else{
								$sql->query("INSERT INTO `email_logs` 
									(`subject`, `parent_type`, `parent_id`, `sent_to`, `time`, `sent_by_id`, `sent`)
									VALUES
									('$subject', 'order', $id, $sent_to, $time, $global_user_id, 0)
									");
								$errors[] = "Email not sent";
							}
						}
					}
				}
			}
		}else{
			$errors[] = "Nothing specified";
		}
		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}