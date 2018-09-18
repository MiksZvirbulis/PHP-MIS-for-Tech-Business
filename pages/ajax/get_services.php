<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['service_name']) AND page::isLoggedIn()){
	$service_name = $_POST['service_name'];
	$search_name = $sql->smart("%$service_name%");
	$services = $sql->query("SELECT * FROM `services` WHERE `title` LIKE $search_name OR `description` LIKE $search_name OR `cost` LIKE $search_name");
	if($sql->num_rows($services) == 0){
		page::alert("No services were found!", "danger");
	}else{
		?>
		<table class="table table-striped table-bordered" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Title</th>
					<th>Description</th>
					<th>Cost</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				while($service = $sql->fetch_array($services)){
					?>
					<tr>
						<td><?=$service['title']?></td>
						<td><?=$service['description']?></td>
						<td>£<?=$service['cost']?></td>
						<td>
							<a class="confirm" href="<?=url?>/services/del/<?=$service['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-remove" style="opacity:0.4;"></span> Delete</button></a>
							<a href="<?=url?>/services/edit/<?=$service['id']?>"><button class="btn btn-info btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity:0.4;"></span> Edit</button></a>
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
	exit;
}
?>