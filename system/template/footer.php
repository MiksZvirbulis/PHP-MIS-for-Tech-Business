</div>
<?php if($this->isLoggedIn()): ?>
	<div class="footer">
		<p style="float: left;"><?=title?> &copy; <?=date("Y", time())?></p><p style="float: right;"><a href="mailto:info@tech-house.co.uk">info@tech-house.co.uk</a> ● <a href="http://tech-house.co.uk" target="_blank">tech-house.co.uk</a></p>
	</div>
	<div class="modal fade" id="sendSMSWindow" tabindex="-1" role="dialog" aria-labelledby="sendSMSLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Cancel"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="sendSMSLabel"></h4>
				</div>
				<div class="modal-body">
					<div class="form modal-form">
						<div id="sendSMSErrors"></div>
						<form method="POST" class="form-inline" style="width: 550px; margin: 0 auto;" id="sendSMS">
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
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<?php if(($pageInfo[1] == "orders" AND isset($pageInfo[2]) AND $pageInfo[2] == "edit") OR ($pageInfo[1] == "purchases" AND isset($pageInfo[2]) AND ($pageInfo[2] == "edit" OR $pageInfo[2] == "add"))): ?>
	<div id="calculator">
		<div class="inside" style="display: none;" id="calculator-widget">
			<button type="button" class="close" onClick="toggleCalculator()">
				<span aria-hidden="true">&times;</span>
			</button>
			<div class="form-group">
				<label class="sr-only" for="calc_price_exc">Price exc VAT</label>
				<input type="text" class="form-control" id="calc_price_exc" onclick="money(this)" onblur="money(this), calcVAT('exc')" placeholder="Price exc VAT" value="0.00">
			</div>
			<div class="form-group">
				<label class="sr-only" for="calc_price_inc">Price inc VAT</label>
				<input type="text" class="form-control" id="calc_price_inc" onclick="money(this)" onblur="money(this), calcVAT('inc')" placeholder="Price inc VAT" value="0.00">
			</div>
			<div class="form-group">
				<label class="sr-only" for="calc_vat">VAT Total</label>
				<input type="text" class="form-control" id="calc_vat" value="0.00" readonly>
			</div>
		</div>
		<i class="fa fa-calculator pointer" style="font-size: 20pt;" onClick="toggleCalculator()" id="calculator-button"></i>
	</div>
<?php endif; ?>
<?php endif; ?>
</body>
</html>