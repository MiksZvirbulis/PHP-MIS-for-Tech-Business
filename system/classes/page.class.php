<?php
class page
{
	public $lapa;
	private $template;
	var $receipt_status = array("repair_progress" => "In Repair Progress", "under_inspection" => "Under Inspection", "awaiting_parts" => "Waiting for Parts", "not_fixable" => "Not Fixable", "completed" => "Ready for Collection", "shipped" => "Completed", "partially_shipped" => "Partially Shipped", "cancelled" => "Cancelled");
	var $order_status = array("open_order" => "Open Order", "to_be_built" => "To Be Built", "awaiting_parts" => "Waiting for Parts", "under_testing" => "Under Testing", "ready" => "Ready for Collection / Shipment", "cancelled" => "Cancelled", "completed" => "Completed");
	var $build_service = array("not_applicable" => "Not Applicable", "3days" => "3 Working Days", "5days" => "5 Working Days", "10days" => "10 Working Days");
	var $payment_types = array("cash" => "Cash", "bank_transfer" => "Bank Transfer", "instore_card" => "In Store Card Payment", "cheque" => "Cheque", "paypal" => "PayPal");
	var $sms_errors = array(

		-400 => "Wrong API KEY for the request",
		-500 => "Missing required parameters",
		-501 => "Wrong “type”, must be “txt” or “bin”",
		-503 => "Destination address is blocked",
		-504 => "Not available for this operator",
		-508 => "Wrong destination address",
		-509 => "Wrong message encoding",
		-511 => "Number does not exist or operator/owner has been changed",
		-513 => "Wrong message length",
		-514 => "Sender name is not available for you",
		-515 => "Not enough funds to send the message",
		-555 => "General system error",

		);
	var $actions = array(
		"receipt_updated" => "Receipt Updated",
		"receipt_created" => "Receipt Created",
		"order_updated" => "Order Updated",
		"order_created" => "Order Created",
		"new_services" => "New services have been added"
		);
	function __construct($lapas_nosaukums, $access_level, $construct_template = true){
		global $pageInfo;
		global $sql;
		if(self::isLoggedIn()){
			setcookie("auth_time", time(), time() + 86400, "/");
			$user_id = $_COOKIE['user_id'];
			$timestamp = $sql->smart(time());
			$sql->query("UPDATE `users` SET `last_action_time` = $timestamp WHERE `id` = $user_id");
			$user_query = $sql->fetch_array($sql->query("SELECT * FROM `users` WHERE `id` = $user_id"));
			$name = $user_query['name'];
			$username = $user_query['username'];
			$test_access = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `id` = $user_id AND `level` <= $access_level"));
			if($test_access == 0){
				header("Location: /#noaccess");
			}
			if(isset($_COOKIE['auth_time'])){
				$inactive = time() - $_COOKIE['auth_time'];
				if($inactive >= 3600){
					setcookie("user_id", null, -1, "/");
					setcookie("password", null, -1, "/");
					setcookie("auth_time", null, -1, "/");
					header("Location: /#session");
				}
			}
		}
		$this->lapa = $lapas_nosaukums;
		$this->template = $construct_template;
		if($this->template === true){
			include dir . "/system/template/header.php";
			echo "\n";
		}
	}

	function __destruct(){
		global $start;
		global $pageInfo;
		global $sql;
		if($this->template === true){
			include dir . "/system/template/footer.php";
		}
	}

	public static function multiplyVAT($rate){
		if($rate == "20.00"){
			return 1.2;
		}else{
			return 1;
		}
	}

	public function changeTitle($title){
		echo '<script type="text/javascript">changeTitle("' . $title . '");</script>';
	}

	public static function escape($string){
		$string = stripslashes($string);
		$string = mysql_real_escape_string($string);
		$string = strip_tags($string);
		$string = str_replace("%", "", $string);
		$string = str_replace("_", "", $string);
		return $string;
	}

	public static function countToDo($user_id){
		global $sql;
		$user_id = $sql->smart($user_id);
		$query = $sql->query("SELECT `id` FROM `todo` WHERE `user_id` = $user_id AND `done` = 0");
		return $sql->num_rows($query);
	}

	public static function limit($string, $characters){
		$string = strip_tags($string);
		if(strlen($string) <= $characters){
			return $string;
		}else{
			$string = substr($string, 0, $characters) . "...";
			return $string;
		}
	}

	public static function generateRandomString($length = 32){
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$randomString = "";
		for($i = 0; $i < $length; $i++){
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	public static function getCatInfo($cat_id, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `stock_cat` WHERE `cat_id` = $cat_id");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function getSubcatInfo($subcat_id, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `stock_subcat` WHERE `subcat_id` = $subcat_id");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function vat($exvat){
		$incvat = $exvat+(vat*($exvat/100)); 
		$incvat = round($incvat, 2);
		return $incvat;
	} 

	public static function alert($message, $type, $dismissable = false){
		echo '<div class="alert alert-' . $type . '">';
		if($dismissable){
			echo '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span></button>';
		}
		echo $message;
		echo '</div>';
	}

	public static function generatePassword($length = 9, $strength = 0){
		$vowels = "aeuy";
		$consonants = "bdghjmnpqrstvz";
		if($strength >= 1){
			$consonants .= "BDGHJLMNPQRSTVWXZ";
		}
		if($strength >= 2){
			$vowels .= "AEUY";
		}
		if($strength >= 4){
			$consonants .= "23456789";
		}
		if($strength >= 8){
			$consonants .= "@#$%:[];";
		}
		$password = "";
		$alt = time() % 2;
		for($i = 0; $i < $length; $i++){
			if($alt == 1){
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			}else{
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

	public static function hashPassword($password, $newsalt = false, $cursalt = false){
		if($newsalt){
			$salt = self::generatePassword(10, 8);
		}else{
			$salt = $cursalt;
		}
		$pHash = md5(md5(md5($password).$salt).$salt);
		return array("hash" => $pHash, "salt" => $salt);
	}

	public static function isLoggedIn(){
		global $sql;
		if(isset($_COOKIE['user_id']) AND isset($_COOKIE['password'])){
			if((int)$_COOKIE['user_id'] > 0 AND strlen($_COOKIE['password']) == 32){
				$user_id = $sql->smart($_COOKIE['user_id']);
				$query = $sql->query("SELECT `password` FROM `users` WHERE `id` = $user_id AND `active` = 1");
				$uRow = $sql->fetch_array($query);
				$return = $uRow['password'] == $_COOKIE['password'] ? true : false;
			}else{
				$return = false;
			}
		}else{
			$return = false;
		}
		return $return;
	}

	public static function hasLevel($level){
		global $sql;
		if(self::isLoggedIn()){
			$user_id = $sql->smart($_COOKIE['user_id']);
			$level = $sql->smart($level);
			$access = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `id` = $user_id AND `level` <= $level"));
			if($access == 0){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}

	public static function moduleAccess($level){
		global $sql;
		if(self::isLoggedIn()){
			$user_id = $sql->smart($_COOKIE['user_id']);
			$level = $sql->smart($level);
			$access = $sql->num_rows($sql->query("SELECT `id` FROM `users` WHERE `id` = $user_id AND `level` <= $level"));
			if($access == 0){
				header("Location: /main#noaccess");
			}else{
				return true;
			}
		}else{
			header("Location: /main#noaccess");
		}
	}

	public static function getItemInfo($upc, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `stock_items` WHERE `upc` = $upc");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function getSupplierInfo($id, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `suppliers` WHERE `id` = $id");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function getUserInfo($id, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `users` WHERE `id` = $id");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function getServiceInfo($id, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `services` WHERE `id` = $id");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function getPurchaseInfo($purchase_id, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `purchases` WHERE `purchase_id` = $purchase_id");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function getCreditNoteInfo($id, $row){
		global $sql;
		$query = $sql->query("SELECT `$row` FROM `credit_notes` WHERE `id` = $id");
		$result = $sql->fetch_array($query);
		return $result[$row];
	}

	public static function returnCommitted($upc){
		global $sql;
		$comm_parts_item_total = 0;
		$committed_orders = $sql->query("SELECT `order_id` FROM `orders` WHERE `order_status` != 'completed'");
		while($order = $sql->fetch_array($committed_orders)){
			$order_id = $order['order_id'];
			# Sum of All Part Items
			$part_items = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `order_items` WHERE `upc` = $upc AND `order_id` = $order_id AND `cost_exc_vat` <> ''"));
			# Sum of All Part Items
			$committed_data[] = array("order_id" => $order_id);
			$comm_parts_item_total += $part_items['total'];
		}
		$comm_pc_item_total = 0;
		if(isset($committed_data)){
			$find_specs = $sql->query("SELECT * FROM `order_specs` WHERE `type` = 'pc'");
			while($spec = $sql->fetch_array($find_specs)){
				foreach($committed_data as $committed){
					$order_id = $committed['order_id'];
					if($order_id == $spec['order_id']){
						$spec_quantity = $spec['quantity'];
						$spec_id = $spec['spec_id'];
						$pc_items = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `order_items` WHERE `upc` = $upc AND `order_id` = $order_id AND `order_spec_id` = $spec_id"));
						$comm_pc_item_total += $spec_quantity * $pc_items['total'];
					}
				}
			}
		}
		return $comm_parts_item_total + $comm_pc_item_total;
	}

	public static function returnShipped($upc){
		global $sql;
		$ship_parts_item_total = 0;
		$shipped_orders = $sql->query("SELECT `order_id` FROM `orders` WHERE `order_status` = 'completed'");
		while($order = $sql->fetch_array($shipped_orders)){
			$order_id = $order['order_id'];
			# Sum of All Part Items
			$part_items = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `order_items` WHERE `upc` = $upc AND `order_id` = $order_id AND `cost_exc_vat` <> ''"));
			# Sum of All Part Items
			$shipped_data[] = array("order_id" => $order_id);
			$ship_parts_item_total += $part_items['total'];
		}
		$ship_pc_item_total = 0;
		if(isset($shipped_data)){
			$find_specs = $sql->query("SELECT * FROM `order_specs` WHERE `type` = 'pc'");
			while($spec = $sql->fetch_array($find_specs)){
				foreach($shipped_data as $shipped){
					$order_id = $shipped['order_id'];
					if($order_id == $spec['order_id']){
						$spec_quantity = $spec['quantity'];
						$spec_id = $spec['spec_id'];
						$pc_items = $sql->fetch_array($sql->query("SELECT SUM(`quantity`) AS `total` FROM `order_items` WHERE `upc` = $upc AND `order_id` = $order_id AND `order_spec_id` = $spec_id"));
						$ship_pc_item_total += $spec_quantity * $pc_items['total'];
					}
				}
			}
		}
		return $ship_parts_item_total + $ship_pc_item_total;
	}

	public static function getIP(){
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		if(filter_var($client, FILTER_VALIDATE_IP)){
			$ip_address = $client;
		}elseif(filter_var($forward, FILTER_VALIDATE_IP)){
			$ip_address = $forward;
		}else{
			$ip_address = $remote;
		}
		return $ip_address;
	}

	public static function attendanceMarked($session = "morning", $user_id = false){
		global $sql;
		$user_id = ($user_id === false) ? $_COOKIE['user_id'] : $user_id;
		$user_id = (int)$user_id;
		$user_check = $sql->fetch_array($sql->query("SELECT `mark_attendance` FROM `users` WHERE `id` = $user_id"));
		if($user_check['mark_attendance'] == 1){
			$date = $sql->smart(date("d/m/Y"));
			if($session == "morning"){
				$check_today = $sql->num_rows($sql->query("SELECT `id` FROM `attendance` WHERE `user_id` = $user_id AND `date` = $date AND `start_time` != ''"));
			}else{
				$check_today = $sql->num_rows($sql->query("SELECT `id` FROM `attendance` WHERE `user_id` = $user_id AND `date` = $date AND `finish_time` != ''"));
			}
			if($check_today == 0){
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}

	public static function pagination($rpp, $count, $href, $pageNumber, $minus = 0, $opts = array()){
		global $pageInfo;
		$pages = ceil($count / $rpp);
		if(!isset($opts["lastpagedefault"])) $pagedefault = 0;
		else{
			$pagedefault = floor(($count - 1) / $rpp);
			if ($pagedefault < 0) $pagedefault = 0;
		}
		if(isset($pageInfo[$pageNumber])){
			$page = $pageInfo[$pageNumber] - 1;
			if($page < 0)
				$page = $pagedefault;
		}else
		$page = $pagedefault;
		$pager2 = "";
		$lastpage = $page;

		if($lastpage == 0 AND $pages > 1){
			$next = '<li><a href="' . $href . ($lastpage + 2) . '">Next &raquo;</a></li>';
		}elseif($pages == 1){
			
		}elseif($pages == ($lastpage + 1)){
			$previous = '<li><a href="' . $href . ($lastpage) . '">&laquo; Previuous</a></li>';
		}else{
			$next = '<li><a href="' . $href . ($lastpage + 2) . '">Next &raquo;</a></li>';
			$previous = '<li><a href="' . $href . ($lastpage) . '">&laquo; Previuous</a></li>';
		}
		
		if($count){
			$pagerarr = array();
			$dotted = 0;
			$dotspace = 3;
			$dotend = $pages+1 - $dotspace;
			$curdotend = $page +1 - $dotspace;
			$curdotstart = $page +1 + $dotspace;
			for($i = 1; $i <= $pages; $i++){
				if(($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)){
					if(!$dotted)
						$pagerarr[] = "";
					$dotted = 1;
					continue;
				}
				$dotted = 0;
				$start = $i * $rpp;
				$end = $start + $rpp - 1;
				if($end > $count)
					$end = $count;
				$href2 = $href. $i;
				$text = $i;
				if($i != $page +1)
					$pagerarr[] = "<li><a href=\"{$href2}\">$text</a></li>\n";
				else
					$pagerarr[] = "<li class=\"active\"><a>$text</a></li>\n";
			}
			$pagerstr = join("", $pagerarr);
			$pagertop = '<ul class="pagination  pagination-sm">' . $pagerstr . $pager2 . '</ul>';
			if($lastpage == 0 AND $pages > 1){
				$pagerbottom = '<ul class="pagination  pagination-sm">' . $pagerstr . $pager2 . $next . '</ul>';
			}elseif($pages == 1){
				$pagerbottom = '<ul class="pagination  pagination-sm">' . $pagerstr . $pager2 . '</ul>';
			}elseif($pages == ($lastpage + 1)){
				$pagerbottom = '<ul class="pagination  pagination-sm">' . $previous . $pagerstr . $pager2 . '</ul>';
			}else{
				$pagerbottom = '<ul class="pagination  pagination-sm">' . $previous . $pagerstr . $pager2 . $next . '</ul>';
			}
		}else{
			$pagerstr = "";
			$pagertop = '<ul class="pagination  pagination-sm">';
			$pagertop = "$pager$pagerstr$pager2\n";
			$pagerbottom = "$pager$pagerstr$pager2\n";
		}
		$start = $page * $rpp;
		return array($pagertop, $pagerbottom, "LIMIT $start, $rpp");
	}

	public function sendEmail($to, $from, $subject, $message, $headers = true, $cc = true){
		if($headers === true){
			$headers = "From: " . $from . "\r\n";
			if($cc === true){
				$headers .= "CC: " . $from . "\r\n";
			}
			$headers .= "Reply-To: " . $from . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		}
		if(mail($to, '=?utf-8?B?' . base64_encode($subject) . '?=', $message, $headers)){
			return true;
		}else{
			return false;
		}
	}

	public function logAction($parent_type, $parent_id, $custom, $value){
		global $sql;
		$user_id = $_COOKIE['user_id'];
		$time = $sql->smart(time());
		if($custom === true){
			$action = $sql->smart("");
			$custom_action = $sql->smart($value);
		}else{
			$action = $sql->smart($value);
			$custom_action = $sql->smart("");
		}
		$sql->query("INSERT INTO `logs` (`user_id`, `time`, `action`, `custom_action`, `parent_type`, `parent_id`)
			VALUES
			($user_id, $time, $action, $custom_action, '$parent_type', $parent_id)
			");
	}
}
?>