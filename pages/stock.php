<?php
$page = new page("Stock", 3);
$global_user_id = $_COOKIE['user_id'];
if(isset($pageInfo[2])){
	$action = $pageInfo[2];
	if($action == "add" AND page::moduleAccess(2)){
		if(isset($pageInfo[3])){
			$module = $pageInfo[3];
			if($module == "cat"){
				if(isset($_POST['add_cat'])){
					$cat_choice = $_POST['cat_choice'];
					if($cat_choice == "new"){
						$errors = array();
						if(empty($_POST['description'])){
							$errors[] = "The description of the new category was left empty!";
						}
						if(empty($_POST['subcat_description'])){
							$errors[] = "The description of the new subcategory was left empty!";
						}
						if(count($errors) > 0){
							foreach($errors as $error){
								page::alert($error, "danger");
							}
						}else{
							$description = $sql->smart($_POST['description']);
							$sql->query("INSERT INTO `stock_cat` (`description`, `created_by_id`) VALUES ($description, $global_user_id)");
							$subcat_description = $sql->smart($_POST['subcat_description']);
							$cat = $sql->fetch_array($sql->query("SELECT `cat_id` FROM `stock_cat` ORDER BY `cat_id` DESC LIMIT 1")) or die(mysql_error());
							$cat_id = $cat['cat_id'];
							$sql->query("INSERT INTO `stock_subcat` (`description`, `cat_id`, `created_by_id`) VALUES ($subcat_description, $cat_id, $global_user_id)") or die(mysql_error());
							page::alert("The new category and subcategory was added!", "success");
						}
					}elseif($cat_choice == "existing"){
						$errors = array();
						if(empty($_POST['subcat_description'])){
							$errors[] = "The description of the new subcategory was left empty!";
						}
						if(count($errors) > 0){
							foreach($errors as $error){
								page::alert($error, "danger");
							}
						}else{
							$subcat_description = $sql->smart($_POST['subcat_description']);
							$cat_id = $_POST['cat_id'];
							$sql->query("INSERT INTO `stock_subcat` (`description`, `cat_id`, `created_by_id`) VALUES ($subcat_description, $cat_id, $global_user_id)") or die(mysql_error());
							page::alert("The new subcategory was added!", "success");
						}
					}
				}
				$existing_cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
				?>
				<form method="POST">
					<div class="input-group">
						<span class="input-group-addon" style="padding: 3px 12px;">
							<label>
								<input type="radio" name="cat_choice" value="new" id="new" checked>
								<span class="lbl"></span>
							</label>
						</span>
						<input type="text" class="form-control" name="description" placeholder="New category description" onfocus="check('new')">
					</div>
					<div class="input-group">
						<span class="input-group-addon" style="padding: 3px 12px;">
							<label>
								<input type="radio" name="cat_choice" value="existing" id="existing">
								<span class="lbl"></span>
							</label>
						</span>
						<select class="form-control" name="cat_id" onfocus="check('existing')">
							<option selected disabled>Choose category</option>
							<?php
							while($existing_cat = $sql->fetch_array($existing_cats)){
								?>
								<option value="<?=$existing_cat['cat_id']?>"><?=$existing_cat['description']?></option>
								<?php
							}
							?>
						</select>
					</div>
					<input type="text" class="form-control" name="subcat_description" placeholder="Subcategory description">
					<div class="form-button">
						<button type="submit" class="btn btn-primary btn-sm" name="add_cat">Add category</button>
					</div>
				</form>
				<?php
			}elseif($module == "item"){
				if(isset($_POST['add_item'])){
					$errors = array();
					if(empty($_POST['cat_id'])){
						$errors[] = "The category of the new item was left empty!";
					}
					if(empty($_POST['subcat_id'])){
						$errors[] = "The subcategory of the new item was left empty!";
					}
					if(empty($_POST['description'])){
						$errors[] = "The description of the new item was left empty!";
					}
					if(empty($_POST['public'])){
						$errors[] = "The public description of the new item was left empty!";
					}
					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$description = $sql->smart($_POST['description']);
						$public = $sql->smart($_POST['public']);
						$cat_id = $_POST['cat_id'];
						$subcat_id = $_POST['subcat_id'];
						$sql->query("INSERT INTO `stock_items` (`description`, `public`, `cat_id`, `subcat_id`, `created_by_id`) VALUES ($description, $public, $cat_id, $subcat_id, $global_user_id)") or die(mysql_error());
						page::alert("Item added in the stock list!", "success");
					}
				}
				$existing_cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
				?>
				<form method="POST">
					<select class="form-control" name="cat_id" onchange="get_stock_subcat()" id="select_cat">
						<option selected disabled>Choose category</option>
						<?php
						while($existing_cat = $sql->fetch_array($existing_cats)){
							?>
							<option value="<?=$existing_cat['cat_id']?>"><?=$existing_cat['description']?></option>
							<?php
						}
						?>
					</select>
					<div id="subcat"></div>
					<input type="text" class="form-control" name="description" id="search_item" onkeyup="list_item_upc()" placeholder="Item description">
					<div class="input-group">
						<span class="input-group-addon" style="padding: 3px 12px;">
							<label>
								<input type="checkbox" onChange="changePublic(this)">
								<span class="lbl"></span>
							</label>
						</span>
						<input type="text" class="form-control" name="public" id="public" placeholder="Item public description">
					</div>
					<div class="form-button">
						<button type="submit" class="btn btn-primary btn-sm" name="add_item">Add item</button>
					</div>
				</form>
				<div id="loader"></div>
				<div id="items"></div>
				<?php
			}else{
				page::alert("The specified module was not found!", "danger");
			}
		}else{
			page::alert("Select module to continue with specified action!", "danger");
		}
		?>
		<?php
	}elseif($action == "categories" AND page::moduleAccess(2)){
		if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
			if($pageInfo[3] == "find"){
				if(isset($pageInfo[4]) AND !empty($pageInfo[4])){
					$subcat_id = $sql->smart((int)$pageInfo[4]);
					$find_subcat = $sql->num_rows($sql->query("SELECT `subcat_id` FROM `stock_subcat` WHERE `subcat_id` = $subcat_id"));
					if($find_subcat == 0){
						page::alert("No Subcategory was found with the ID specified!", "danger");
					}else{
						$items = $sql->query("SELECT * FROM `stock_items` WHERE `subcat_id` = $subcat_id");
						if($sql->num_rows($items) == 0){
							page::alert("This subcategory has no items!", "info");
						}else{
							?>
							<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>UPC</th>
										<th>Category</th>
										<th>Sub category</th>
										<th>Name</th>
										<th>Public Name</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if(isset($_POST['update']) AND isset($_POST['cat_id']) AND isset($_POST['subcat_id']) AND isset($_POST['description'])){
										$errors = array();
										if(empty($_POST['description'])){
											$errors[] = "You forgot to enter the description!";
										}
										if(empty($_POST['public'])){
											$errors[] = "The public description of the new item was left empty!";
										}
										if(count($errors) > 0){
											foreach($errors as $error){
												page::alert($error, "danger");
											}
										}else{
											$upc = $sql->smart($_POST['upc']);
											$cat_id = $sql->smart($_POST['cat_id']);
											$new_subcat_id = $sql->smart($_POST['subcat_id']);
											$description = $sql->smart($_POST['description']);
											$public = $sql->smart($_POST['public']);
											$sql->query("UPDATE `stock_items` SET `cat_id` = $cat_id, `subcat_id` = $new_subcat_id, `description` = $description, `public` = $public WHERE `upc` = $upc");
											page::alert("Item successfully updated!", "success");
											$items = $sql->query("SELECT * FROM `stock_items` WHERE `subcat_id` = $subcat_id");
										}
									}
									$i = 1;
									while($item = $sql->fetch_array($items)){
										$description = htmlentities(str_replace("'", "\'", $item['description']));
										$public = htmlentities(str_replace("'", "\'", $item['public']));
										?>
										<form role="form" method="POST">
											<tr>
												<td><input type="hidden" name="upc" value="<?=$item['upc']?>"><?=$item['upc']?></td>
												<td>
													<select class="form-control" name="cat_id" onchange="get_subcat(<?=$i?>)" id="select_cat_<?=$i?>">
														<?php
														$cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
														while($cat = $sql->fetch_array($cats)){
															$selected = ($cat['cat_id'] == $item['cat_id']) ? " selected" : "";
															?>
															<option value="<?=$cat['cat_id']?>"<?=$selected?>><?=$cat['description']?></option>
															<?php
														}
														?>
													</select>
												</td>
												<td>
													<select class="form-control" name="subcat_id" id="subcat_<?=$i?>">
														<?php
														$cat_id = $sql->smart($item['cat_id']);
														$subcats = $sql->query("SELECT * FROM `stock_subcat` WHERE `cat_id` = $cat_id ORDER BY `description` ASC");
														while($subcat = $sql->fetch_array($subcats)){
															$selected = ($subcat['subcat_id'] == $item['subcat_id']) ? " selected" : "";
															?>
															<option value="<?=$subcat['subcat_id']?>"<?=$selected?>><?=$subcat['description']?></option>
															<?php
														}
														?>
													</select>
												</td>
												<td><input type="text" class="form-control" name="description" value="<?=$description?>"></td>
												<td><input type="text" class="form-control" name="public" value="<?=$public?>"></td>
												<td><button type="submit" class="btn btn-default" name="update">Update</button></td>
											</tr>
										</form>
										<?php
										$i++;
									}
									?>
								</tbody>
							</table>
							<?php
						}
					}
				}else{
					page::alert("No ID was specified!", "danger");
				}
			}elseif($pageInfo[3] == "edit"){
				if(isset($pageInfo[4]) AND !empty($pageInfo[4])){
					$subcat_id = $sql->smart((int)$pageInfo[4]);
					$find_subcat = $sql->query("SELECT * FROM `stock_subcat` WHERE `subcat_id` = $subcat_id");
					if($sql->num_rows($find_subcat) == 0){
						page::alert("No Subcategory was found with the ID specified!", "danger");
					}else{
						if(isset($_POST['update']) AND isset($_POST['cat_id']) AND isset($_POST['description'])){
							$errors = array();
							if(empty($_POST['description'])){
								$errors[] = "You forgot to enter the description!";
							}
							if(count($errors) > 0){
								foreach($errors as $error){
									page::alert($error, "danger");
								}
							}else{
								$cat_id = $sql->smart($_POST['cat_id']);
								$description = $sql->smart($_POST['description']);
								$sql->query("UPDATE `stock_subcat` SET `cat_id` = $cat_id, `description` = $description WHERE `subcat_id` = $subcat_id");
								page::alert("Subcategory successfully updated!", "success");
								$find_subcat = $sql->query("SELECT * FROM `stock_subcat` WHERE `subcat_id` = $subcat_id");
							}
						}
						$subcat = $sql->fetch_array($find_subcat);
						?>
						<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>Category</th>
									<th>Description</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<form role="form" method="POST">
									<tr>
										<td>
											<select class="form-control" name="cat_id">
												<?php
												$cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
												while($cat = $sql->fetch_array($cats)){
													$selected = ($cat['cat_id'] == $subcat['cat_id']) ? " selected" : "";
													?>
													<option value="<?=$cat['cat_id']?>"<?=$selected?>><?=$cat['description']?></option>
													<?php
												}
												?>
											</select>
										</td>
										<td><input type="text" class="form-control" name="description" value="<?=$subcat['description']?>"></td>
										<td><button type="submit" class="btn btn-default" name="update">Update</button></td>
									</tr>
								</form>
							</tbody>
						</table>
						<?php
					}
				}else{
					page::alert("No ID was specified!", "danger");
				}
			}else{
				page::alert("The specified action was not found!", "danger");
			}
		}else{
			if(isset($_POST['update']) AND isset($_POST['cat_id']) AND isset($_POST['description'])){
				$cat_id = $sql->smart($_POST['cat_id']);
				$description = $sql->smart($_POST['description']);
				$sql->query("UPDATE `stock_cat` SET `description` = $description WHERE `cat_id` = $cat_id");
				page::alert("Category name successfully updated!", "success");
			}
			if(isset($_POST['view']) AND isset($_POST['cat_id'])){
				if($_POST['cat_id'] == "all"){
					$cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
				}else{
					$view_cat_id = $sql->smart($_POST['cat_id']);
					$cats = $sql->query("SELECT * FROM `stock_cat` WHERE `cat_id` = $view_cat_id");
				}
			}else{
				$cats = "";
			}
			?>
			<center>
				<form class="form-inline bottom-space" role="form" method="POST">
					<div class="form-group">
						<select class="form-control no-space" name="cat_id">
							<option value="all">All Categories</option>
							<?php
							$listcats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
							while($cat = $sql->fetch_array($listcats)){
								if(isset($_POST['cat_id'])){
									$cat_d = ($_POST['cat_id'] == $cat['cat_id']) ? " selected" : "";
								}else{
									$cat_d = "";
								}
								?>
								<option value="<?=$cat['cat_id']?>"<?=$cat_d?>><?=$cat['description']?></option>
								<?php
							}
							?>
						</select>
					</div>
					<button type="submit" class="btn btn-default" name="view">View</button>
				</form>
			</center>
			<?php
			while($cat = $sql->fetch_array($cats)){
				$cat_id = $sql->smart($cat['cat_id']);
				?>
				<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 5px;">
					<thead>
						<tr>
							<th><?=$cat['description']?></th>
							<th>
								<form class="form-inline" role="form" method="POST">
									<div class="form-group">
										<input type="hidden" name="cat_id" value="<?=$cat['cat_id']?>">
										<input type="text" name="description" class="form-control no-space" value="<?=$cat['description']?>" style="width: 475px;">
									</div>
									<button type="submit" class="btn btn-default" name="update">Update</button>
								</form>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sub_cats = $sql->query("SELECT * FROM `stock_subcat` WHERE `cat_id` = $cat_id ORDER BY `description` ASC");
						while($sub_cat = $sql->fetch_array($sub_cats)){
							?>
							<tr>
								<td width="50%"><?=$sub_cat['description']?></td>
								<td width="50%">
									<a href="<?=url?>/stock/categories/find/<?=$sub_cat['subcat_id']?>" target="_blank"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-search" style="opacity:0.4;"></span> List Items</button></a>
									<a href="<?=url?>/stock/categories/edit/<?=$sub_cat['subcat_id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<?php
			}
		}
	}elseif($action == "opening" AND page::moduleAccess(1)){
		if(isset($pageInfo[3]) AND !empty($pageInfo[3])){
			$opening_action = $pageInfo[3];
		}else{
			$opening_action = "list";
		}
		if($opening_action == "add"){
			if(isset($_POST['add_opening_stock'])){
				$errors = array();
				if(!isset($_POST['upc'])){
					$errors[] = "No items have been selected!";
				}
				if(!isset($_POST['quantity'])){
					$errors[] = "No quantities have been set!";
				}
				if(!isset($_POST['reason'])){
					$errors[] = "No reasons have been set!";
				}
				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$upcs = $_POST['upc'];
					$quantity = $_POST['quantity'];
					$reasons = $_POST['reason'];
					foreach($upcs as $key => $upc){
						if(!empty($upc)){
							$upc = $sql->smart($upc);
							$item_quantity = $sql->smart($quantity[$key]);
							$reason = $sql->smart($reasons[$key]);
							$added = $sql->smart(time());
							$sql->query("INSERT INTO `opening_stock` (`upc`, `quantity`, `reason`, `added`, `added_by_id`) VALUES ($upc, $item_quantity, $reason, $added, $global_user_id)");
						}
					}
					page::alert("Opening stock added successfully!", "success");
				}
			}
			?>
			<form method="POST">
				<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Category</th>
							<th>Sub category</th>
							<th>Item</th>
							<th>UPC</th>
							<th>Quantity</th>
							<th>Reason</th>
						</tr>
					</thead>
					<tbody>
						<?php
						for($i = 1; $i <= 10; $i++){
							?>
							<tr>
								<td><?=$i?></td>
								<td>
									<select class="form-control" onchange="get_subcat(<?=$i?>)" id="select_cat_<?=$i?>">
										<option selected disabled>Choose category</option>
										<?php
										$existing_cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
										while($existing_cat = $sql->fetch_array($existing_cats)){
											?>
											<option value="<?=$existing_cat['cat_id']?>"><?=$existing_cat['description']?></option>
											<?php
										}
										?>
									</select>
								</td>
								<td>
									<span id="subcat_<?=$i?>" onchange="get_catitems_list(<?=$i?>)">
										<select class="form-control">
											<option disabled selected>Select category to load sub categories</option>
										</select>
									</span>
								</td>
								<td>
									<span id="catitems_list_<?=$i?>">
										<select class="form-control">
											<option disabled selected>Select sub category to load items</option>
										</select>
									</span>
								</td>
								<td style="width: 100px;"><input type="text" class="form-control" id="upc_<?=$i?>" name="upc[]" placeholder="UPC"></td>
								<td><input class="form-control" type="number" name="quantity[]" placeholder="Quantity" value="0"></td>
								<td><input class="form-control" type="text" name="reason[]" placeholder="Reason"></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<div id="item_search">
					<input type="text" class="form-control" id="search_item" onkeyup="list_item_upc()" placeholder="Search for Item...">
					<div id="loader"></div>
					<div id="items"></div>
				</div>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="add_opening_stock">Add opening stock</button>
				</div>
			</form>
			<?php
		}elseif($opening_action == "edit"){
			if(isset($pageInfo[4]) AND !empty($pageInfo[4])){
				$opening_stock_id = $sql->smart((int)$pageInfo[4]);
				$find_opening_stock = $sql->query("SELECT * FROM `opening_stock` WHERE `id` = $opening_stock_id");
				if($sql->num_rows($find_opening_stock) == 0){
					page::alert("No opening stock data was found with the specified ID!", "danger");
				}else{
					if(isset($_POST['edit_opening_stock'])){
						$errors = array();
						if(empty($_POST['quantity']) AND strlen($_POST['quantity']) == 0){
							$errors[] = "You left the quantity empty!";
						}
						if(empty($_POST['reason'])){
							$errors[] = "You left the reason empty!";
						}
						if(count($errors) > 0){
							foreach($errors as $error){
								page::alert($error, "danger");
							}
						}else{
							$quantity = $sql->smart($_POST['quantity']);
							$reason = $sql->smart($_POST['reason']);
							$sql->query("UPDATE `opening_stock` SET `quantity` = $quantity, `reason` = $reason WHERE `id` = $opening_stock_id");
							page::alert("Opening stock successfully updated!", "success");
							$find_opening_stock = $sql->query("SELECT * FROM `opening_stock` WHERE `id` = $opening_stock_id");
						}
					}
					?>
					<form method="POST">
						<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%" id="item-table">
							<thead>
								<tr>
									<th>UPC</th>
									<th>Item Name</th>
									<th>Quantity</th>
									<th>Reason</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$stock = $sql->fetch_array($find_opening_stock);
								?>
								<tr>
									<td><?=$stock['upc']?></td>
									<td><?=page::getItemInfo($stock['upc'], "description")?></td>
									<td><input class="form-control" type="number" name="quantity" placeholder="Quantity" value="<?=$stock['quantity']?>"></td>
									<td><input class="form-control" type="text" name="reason" placeholder="Reason" value="<?=$stock['reason']?>"></td>
								</tr>
							</tbody>
						</table>
						<div class="form-button">
							<button type="submit" class="btn btn-primary btn-sm" name="edit_opening_stock">Edit opening stock</button>
						</div>
					</form>
					<?php
				}
			}else{
				page::alert("No ID was specified!", "danger");
			}
		}elseif($opening_action == "search"){
			if(isset($pageInfo[4]) AND !empty($pageInfo[4])){
				$upc = $sql->smart((int)$pageInfo[4]);
				$find_opening_stock = $sql->query("SELECT * FROM `opening_stock` WHERE `upc` = $upc");
				if($sql->num_rows($find_opening_stock) == 0){
					page::alert("No opening stock was found relating to the specified UPC!", "danger");
				}else{
					?>
					<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>UPC</th>
								<th>Item Name</th>
								<th>Quantity</th>
								<th>Reason</th>
								<th>Date Added</th>
								<th>Added By</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php
							while($stock = $sql->fetch_array($find_opening_stock)){
								?>
								<tr>
									<td><b><?=$stock['upc']?></b></td>
									<td><?=page::getItemInfo($stock['upc'], "description")?></td>
									<td><?=$stock['quantity']?></td>
									<td><?=$stock['reason']?></td>
									<td><?=date("d/m/Y", $stock['added'])?></td>
									<td><?=page::getUserInfo($stock['added_by_id'], "name")?> <?=page::getUserInfo($stock['added_by_id'], "surname")?></td>
									<td>
										<a href="<?=url?>/stock/opening/edit/<?=$stock['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<?php
				}
			}else{
				page::alert("No ID was specified!", "danger");
			}
		}else{
			?>
			<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>UPC</th>
						<th>Item Name</th>
						<th>Quantity</th>
						<th>Reason</th>
						<th>Date Added</th>
						<th>Added By</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$opening_stock = $sql->query("SELECT * FROM `opening_stock` ORDER BY `added` DESC");
					while($stock = $sql->fetch_array($opening_stock)){
						?>
						<tr>
							<td><b><?=$stock['upc']?></b></td>
							<td><?=page::getItemInfo($stock['upc'], "description")?></td>
							<td><?=$stock['quantity']?></td>
							<td><?=$stock['reason']?></td>
							<td><?=date("d/m/Y", $stock['added'])?></td>
							<td><?=page::getUserInfo($stock['added_by_id'], "name")?> <?=page::getUserInfo($stock['added_by_id'], "surname")?></td>
							<td>
								<a href="<?=url?>/stock/opening/edit/<?=$stock['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}else{
		page::alert("The specified action was not found", "danger");
	}
}else{
	$page->changeTitle("Stock Reports");
	$existing_cats = $sql->query("SELECT * FROM `stock_cat` ORDER BY `description` ASC");
	?>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#search_by_name" data-toggle="tab">By name</a></li>
		<li><a href="#search_by_category" data-toggle="tab">By category</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="search_by_name">
			<input type="text" class="form-control gap-top" id="search_item" onkeydown="get_items()" placeholder="Search for item...">
			<div id="loader"></div>
			<div id="items"></div>
		</div>
		<div class="tab-pane" id="search_by_category">
			<select class="form-control gap-top" name="cat_id" onchange="get_stock_subcat(true)" id="select_cat">
				<option selected disabled>Choose category</option>
				<option value="all">All</option>
				<?php
				while($existing_cat = $sql->fetch_array($existing_cats)){
					?>
					<option value="<?=$existing_cat['cat_id']?>"><?=$existing_cat['description']?></option>
					<?php
				}
				?>
			</select>
			<div id="subcat" onchange="get_catitems()"></div>
			<div id="catitems"></div>
		</div>
	</div>
	<?php
}
?>