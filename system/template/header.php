<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?=title?> :: <?=$this->lapa?></title>
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/checkbox-radio-switch.css">
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/datepicker.css">
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/datepicker.css">
	<link rel="stylesheet" type="text/css" href="<?=url?>/assets/css/timepicker.css">
	<script src="<?=url?>/assets/js/jquery.js"></script>
	<script src="<?=url?>/assets/js/jquery-ui.min.js"></script>
	<script src="<?=url?>/assets/js/bootstrap.js"></script>
	<script src="<?=url?>/assets/js/maskedinput.min.js"></script>
	<script src="<?=url?>/assets/js/datepicker.min.js"></script>
	<script src="<?=url?>/assets/js/timepicker.min.js"></script>
	<script src="<?=url?>/assets/js/bootbox.js"></script>
	<script src="<?=url?>/assets/js/findAddress.js"></script>
	<script src="<?=url?>/assets/js/flot.min.js"></script>
	<script src="<?=url?>/assets/js/flot.pie.min.js"></script>
	<script src="<?=url?>/assets/js/flot.resize.min.js"></script>
	<script src="<?=url?>/assets/js/autosize.min.js"></script>
	<script src="<?=url?>/assets/js/knob.min.js"></script>
	<script src="<?=url?>/assets/js/page.js"></script>
	<link rel="shortcut icon" href="<?=url?>/assets/images/logo.png" />
	<link type="favicon" href="<?=url?>/assets/images/logo.png">
</head>
<body>
	<div class="container">
		<?php if($this->isLoggedIn()): ?>
			<nav class="navbar navbar-default navbar-fixed-top">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
							<li><a href="<?=url?>"><span class="glyphicon glyphicon-home"></span> Home</a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-file"></span> Orders <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">Orders</li>
									<li><a href="<?=url?>/orders">List</a></li>
									<li><a href="<?=url?>/orders/add">Add order</a></li>
									<li><a href="<?=url?>/orders/build">Build list</a></li>
									<li class="dropdown-header">Warranty</li>
									<li><a href="<?=url?>/warranty">List</a></li>
									<?php if(page::hasLevel(1)): ?>
										<li><a href="<?=url?>/warranty/options">Manage options</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-share-alt"></span> Shipment <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">Shipment</li>
									<li><a href="<?=url?>/shipment">List Shipment</a></li>
									<li><a href="<?=url?>/shipment/today">List Today's Shipment</a></li>
									<?php if(page::hasLevel(1)): ?>
										<li class="dropdown-header">Delivery</li>
										<li><a href="<?=url?>/shipment/methods">Delivery methods</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-barcode"></span> Stock <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">Stock</li>
									<li><a href="<?=url?>/stock">Report</a></li>
									<?php if(page::hasLevel(2)): ?>
										<li class="dropdown-header">Stock Management</li>
										<li><a href="<?=url?>/stock/categories">Manage categories</a></li>
										<li><a href="<?=url?>/stock/add/cat">Add category</a></li>
										<li><a href="<?=url?>/stock/add/item">Add item</a></li>
									<?php endif; ?>
									<?php if(page::hasLevel(1)): ?>
										<li class="divider"></li>
										<li class="dropdown-header">Opening stock</li>
										<li><a href="<?=url?>/stock/opening">Opening stock</a></li>
										<li><a href="<?=url?>/stock/opening/add">Add opening stock</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Suppliers</li>
										<li><a href="<?=url?>/suppliers">Suppliers</a></li>
										<li><a href="<?=url?>/suppliers/add">Add supplier</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-shopping-cart"></span> Purchases <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">Purchases</li>
									<li><a href="<?=url?>/purchases">List</a></li>
									<?php if(page::hasLevel(2)): ?>
										<li><a href="<?=url?>/purchases/add">Add purchase</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Credit notes</li>
										<li><a href="<?=url?>/creditnotes">List</a></li>
										<li><a href="<?=url?>/creditnotes/add">Add credit note</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-book"></span> Receipts <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">Receipts</li>
									<li><a href="<?=url?>/receipts">List</a></li>
									<li><a href="<?=url?>/receipts/add">Add receipt</a></li>
									<li class="dropdown-header">Services</li>
									<li><a href="<?=url?>/services">List</a></li>
									<?php if(page::hasLevel(2)): ?>
										<li><a href="<?=url?>/services/add">Add service</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-warning-sign"></span> RMA <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">RMA</li>
									<li><a href="<?=url?>/rma">List</a></li>
									<?php if(page::hasLevel(2)): ?>
										<li><a href="<?=url?>/rma/add">Add RMA</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-envelope"></span> Emailing <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">Email templates</li>
									<li><a href="<?=url?>/emails">List</a></li>
									<?php if(page::hasLevel(2)): ?>
										<li><a href="<?=url?>/emails/add">Add template</a></li>
									<?php endif; ?>
									<?php if(page::hasLevel(2)): ?>
										<li class="dropdown-header">Email categories</li>
										<li><a href="<?=url?>/emails/categories">List</a></li>
										<li><a href="<?=url?>/emails/categories/add">Add cateogry</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<?php if(page::hasLevel(2)): ?>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-comment"></span> SMS <b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li class="dropdown-header">SMS</li>
										<li><a href="<?=url?>/sms/logs">SMS logs</a></li>
										<li><a class="pointer" data-toggle="modal" data-target="#sendSMSWindow">Send SMS</a></li>
										<li class="dropdown-header">SMS Templates</li>
										<li><a href="<?=url?>/sms/templates">List</a></li>
										<li><a href="<?=url?>/sms/templates/add">Add template</a></li>
									</ul>
								</li>
							<?php endif; ?>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<?php
							$time = date("Hi", time());
							if($time >= 0000 AND $time < 1200){
								$welcome = "Good morning";
							}elseif($time >= 1200 AND $time < 1800){
								$welcome = "Good afternoon";
							}elseif($time >= 1800){
								$welcome = "Good evening";
							}else{
								$welcome = "Hello";
							}
							?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=$username?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="dropdown-header">Me</li>
									<li class="dropdown-submenu">
										<a tabindex="-1" href="#">Attendance</a>
										<ul class="dropdown-menu">
											<li><a href="<?=url?>/attendance">My Attendance</a></li>
											<li><a href="<?=url?>/mark">Mark Attendance</a></li>
										</ul>
									</li>
									<li><a href="<?=url?>/todo">To-Do List (<?=page::countToDo($_COOKIE['user_id'])?>)</a></li>
									<li class="divider"></li>
									<li class="dropdown-header">Account</li>
									<li><a href="<?=url?>/profile">Edit profile</a></li>
									<li><a href="<?=url?>/profile/changepw">Change password</a></li>
									<li><a href="<?=url?>/logout">Logout</a></li>
									<?php if(page::hasLevel(1)): ?>
										<li class="divider"></li>
										<li class="dropdown-header">Users</li>
										<li><a href="<?=url?>/users/new">New user</a></li>
										<li><a href="<?=url?>/users">Manage users</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Attendance & Wages</li>
										<li><a href="<?=url?>/attendance/all">Manage attendance</a></li>
										<li><a href="<?=url?>/wages/list">Manage wages</a></li>
										<li><a href="<?=url?>/wages/add">Add wage</a></li>
									<?php endif; ?>
								</ul>
							</li>
							<li><a><?=$welcome?>, <b><?=$name?></b></a></li>
						</ul>
					</div>
				</div>
			</nav>
			<div class="header">
				<h3 class="pull-left">
					<a href="<?=url?>"><img src="<?=url?>/assets/images/logo.png" id="logo"></a> 
					<span class="glyphicon glyphicon-phone number"></span> 0208 778 0090
				</h3>
				<div id="todo">
					<?php $todos = $sql->query("SELECT * FROM `todo` WHERE `user_id` = $user_id AND `done` = 0 AND `dismissed` = 0 ORDER BY `id` ASC LIMIT 1"); ?>
					<?php if($sql->num_rows($todos) != 0): ?>
						<?php $todo = $sql->fetch_array($todos); ?>
						<?php
						$do_by = strtotime(str_replace("/", "-", $todo['do_by'] . " " . $todo['do_by_time']));
						$now = time();
						if($do_by < $now){
							$class = "danger";
						}elseif($todo['do_by'] == date("d/m/Y")){
							$class = "warning";
						}else{
							$class = "info";
						}
						?>
						<div class="alert alert-<?=$class?>" style="padding: 5px;">
							<button type="button" class="close" data-dismiss="alert" onClick="dismissToDo(<?=$todo['id']?>)"><span aria-hidden="true">×</span></button>
							<strong>To-Do:</strong> <?=page::limit($todo['content'], 120)?> - <a href="<?=url?>/todo">todo...</a>
						</div>
					<?php endif; ?>
				</div>
				<?php
				if($pageInfo[1] == "search" AND !empty($pageInfo[2])){
					# Orders
					$query = $pageInfo[2];
					$type = "orders";
				}elseif(isset($pageInfo[2]) AND $pageInfo[2] == "search" AND !empty($pageInfo[3])){
					# Receipts
					$query = $pageInfo[3];
					$type = "receipts";
				}else{
					$query = "";
					$type = "receipts";
				}
				?>
				<form method="POST" action="<?=url?>/search" style="margin-top: 10px;">
					<div class="row">
						<div class="col-lg-4 pull-right" style="width: auto;">
							<div class="input-group">
								<div class="input-group-btn" style="width: 0;">
									<select class="form-control" name="type" style="width: 110px;">
										<option value="receipts" <?php echo ($type == "receipts") ? "selected" : ""; ?>>Receipts</option>
										<option value="orders" <?php echo ($type == "orders") ? "selected" : ""; ?>>Orders</option>
									</select>
								</div>
								<div class="form-group">
									<div class="inner-addon left-addon pull-right">
										<span class="glyphicon glyphicon-search"></span>
										<input type="text" class="form-control" name="query" value="<?php echo urldecode($query); ?>" placeholder="Search...">
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		<?php endif; ?>
		<?php if($pageInfo[1] == "login"): ?>
			<div class="inside-login">
			<?php elseif(($pageInfo[1] == "orders" AND isset($pageInfo[2]) AND ($pageInfo[2] == "edit" OR $pageInfo[2] == "add")) OR $pageInfo[1] == "sms" OR ($pageInfo[1] == "receipts" AND isset($pageInfo[2]) AND ($pageInfo[2] == "edit" OR $pageInfo[2] == "add")) OR $pageInfo[1] == "express" OR $pageInfo[1] == "profile" OR $pageInfo[1] == "todo" OR $pageInfo[1] == "users" OR $pageInfo[1] == "emails" OR $pageInfo[1] == "mark" OR $pageInfo[1] == "attendance" OR ($pageInfo[1] == "purchases" AND isset($pageInfo[2]) AND ($pageInfo[2] == "edit" OR $pageInfo[2] == "add")) OR $pageInfo[1] == "warranty" OR $pageInfo[1] == "creditnotes" OR ($pageInfo[1] == "rma" AND !isset($pageInfo[2]))): ?>
				<div class="inside-smaller">
				<?php else: ?>
					<div class="inside">
					<?php endif; ?>