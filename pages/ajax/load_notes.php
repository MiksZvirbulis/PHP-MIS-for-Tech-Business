<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/system/config.php");
if(isset($_POST['order_id']) AND page::isLoggedIn()){
	?>
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		<?php
		$i = 1;
		$order_id = $sql->smart($_POST['order_id']);
		$notes = $sql->query("SELECT * FROM `order_notes` WHERE `order_id` = $order_id ORDER BY `date` DESC");
		while($note = $sql->fetch_array($notes)){
			if($i == 1){
				$collapse = "collapse in";
			}else{
				$collapse = "collapse";
			}
			?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="heading_<?=$i?>">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?=$i?>" aria-controls="collapse_<?=$i?>">
							From <strong><?=page::getUserInfo($note['added_by_id'], "name")?> <?=page::getUserInfo($note['added_by_id'], "surname")?></strong> on <?=date("d/m/Y H:i", $note['date'])?>
						</a>
						<?php
						if(page::hasLevel(1)){
							?>
							<span class="pull-right pointer glyphicon glyphicon-remove" onClick="removeNote(<?=$note['id']?>, $order_id)"></span>
							<?php
						}
						?>
					</h4>
				</div>
				<div id="collapse_<?=$i?>" class="panel-collapse <?=$collapse?>" role="tabpanel" aria-labelledby="heading_<?=$i?>">
					<div class="panel-body">
						<?php echo nl2br($note['note']); ?>
					</div>
				</div>
			</div>
			<?php
			$i++;
		}
		?>
	</div>
	<?php
}else{
	exit;
}
?>