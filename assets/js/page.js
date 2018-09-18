(function ($) {
	$.fn.stickyTabs = function() {
		context = this
		var showTabFromHash = function() {
			var hash = window.location.hash;
			var selector = hash ? 'a[href="' + hash + '"]' : "li:first-child a";
			$(selector, context).tab("show");
		}
		showTabFromHash(context)
		window.addEventListener("hashchange", showTabFromHash, false);
		$("a", context).on("click", function(e) {
			history.pushState(null, null, this.href);
		});
		return this;
	};
}( jQuery ));

$(document).ready(function(){
	$(".nav-tabs").stickyTabs();
	$(".datepicker").datepicker();
	$(".timepicker").timepicker({
		minuteStep: 1,
		showSeconds: false,
		showMeridian: false,
		defaultTime: false
	});
	if(location.href.indexOf("#") != -1){
		var url = "";
	}else{
		var url = window.location;
	}
	$("ul.nav li a[href='"+ url +"']").parent().addClass("active");
	$("ul.nav li a").filter(function() {
		return this.href == url;
	}).parent().addClass("active");

	$("#duplicate-item").click(function(){
		$("#item").clone().insertAfter("#item");
	});

	$("a.back").click(function(){
		var $link = document.referrer;
		document.location.assign($link);
	});

	$(".confirm").click(function(e){
		e.preventDefault();
		var $link = $(this);
		bootbox.confirm({
			title: "Delete",
			message: "Are you sure you want to continue?",
			buttons: {
				"cancel": {
					label: "Cancel",
					className: "btn-default pull-right"
				},
				"confirm": {
					label: "Continue",
					className: "btn-danger pull-right"
				}
			},
			callback: function(confirmation){
				confirmation && document.location.assign($link.attr("href"));
			}
		});
	});

	if(window.location.hash && window.location.hash == "#noaccess"){
		bootbox.alert("No access!");
		window.location.hash = "";
	}

	if(window.location.hash && window.location.hash == "#session"){
		bootbox.alert("Your session has expired. Login to use the system!", function() {
			window.location.hash = "";
		});
	}

	var uri = window.location.pathname;
	var uri_array = uri.split("/");

	if(uri_array[1] == "purchases" || uri_array[1] == "creditnotes"){
		var count_items = $("tr#item_count").size();
		for(i = 1; i <= count_items; i++){
			item_total(i);
		}
		update_total();
	}

	if(uri_array[1] == "receipts"){
		var count_services = $("tr#service_count").size();
		for(i = 1; i <= count_services; i++){
			service_total(i);
		}
		update_receipt_total();
	}

	if(uri_array[1] == "rma"){
		var count_rma_items = $("tr#rma_item_count").size();
		for(i = 1; i <= count_rma_items; i++){
			editReturnAction(i);
			upgradeExtra(i);
			receivedStatus(i, "rejected");
			receivedStatus(i, "replaced");
			receivedStatus(i, "upgraded");
		}
	}

	if(uri_array[1] == "orders"){
		updateOrderTotal("exc");
		estDelDate();
	}

	$("form#sendSMS").on("submit", function(e) {
		var dataString = $("form#sendSMS").serialize();
		$.ajax({
			type: "POST",
			url: "/pages/ajax/sendSMS.php",
			data: dataString,
			cache: false,
			async: false
		}).done(function(returned) {
			if(returned == "success"){
				$("form#reportBug").hide();
				$("div.form.modal-form div#sendSMSErrors").html('<div class="alert alert-success">SMS sent successfully!</div>');
				setTimeout(function(){
					$("#sendSMSWindow").modal("hide");
					$("form#sendSMS").show();
					$("form#sendSMS input").val("");
					$("form#sendSMS textarea").val("");
					$("div.form.modal-form div#sendSMSErrors").hide();
				}, 3000); 
			}else{
				$("div.form.modal-form div#sendSMSErrors").html(returned).fadeIn("slow");
			}
		});
		return false;
	});

	$("#checkAll").click(function(event){
		if(this.checked) {
			$("input[type=checkbox]").each(function(){
				this.checked = true;             
			});
		}else{
			$("input[type=checkbox]").each(function(){
				this.checked = false;                     
			});         
		}
	});
});

var delayTimer;

function addItem(spec_id, element_id, order_id){
	upc = $("input#upc_" + element_id).val();
	quantity = $("input#quantity_" + element_id).val();

	var dataString = { spec_id: spec_id, upc: upc, quantity: quantity, order_id: order_id };            
	$.ajax({
		type: "POST",
		url: "/pages/ajax/add_item.php",
		data: dataString,
		cache: false
	}).done(function(x) {
		if(x == "true"){
			loadItems(order_id);
		}else{
			alert(x);
		}
	});
}

function addService(spec_id, element_id, order_id){
	service_id = $("select#select_service_" + element_id).val();
	cost_exc_vat = $("input#service_cost_" + element_id).val();
	quantity = $("input#service_quantity_" + element_id).val();
	var dataString = { spec_id: spec_id, service_id: service_id, cost_exc_vat: cost_exc_vat, quantity: quantity, order_id: order_id };            
	$.ajax({
		type: "POST",
		url: "/pages/ajax/add_service.php",
		data: dataString,
		cache: false
	}).done(function(x) {
		if(x == "true"){
			loadItems(order_id);
		}else{
			alert("Spec was not found, so item was not added!");
		}
	});
}

function fillUPC(element, element_id){
	$("input#upc_" + element_id).val(element.value);
}

function loadItems(order_id){
	var dataString = { order_id: order_id };            
	$.ajax({
		type: "POST",
		url: "/pages/ajax/load_items.php",
		data: dataString,
		cache: false
	}).done(function(x) {
		$("#specifications").html(x);
	});
}

function dismissToDo(todo_id){
	var dataString = { todo_id: todo_id };            
	$.ajax({
		type: "POST",
		url: "/pages/ajax/dismiss_todo.php",
		data: dataString,
		cache: false
	});
}

function removeItem(item_id, order_id){
	if(confirm("Are you sure you want to delete this item?") == true){
		var dataString = { item_id: item_id };            
		$.ajax({
			type: "POST",
			url: "/pages/ajax/remove_item.php",
			data: dataString,
			cache: false
		}).done(function(x) {
			if(x == "true"){
				loadItems(order_id);
			}else{
				alert("Item was not found!");
			}
		});
	}
}

function removePayment(payment_id){
	if(confirm("Are you sure you want to delete this payment?") == true){
		var dataString = { payment_id: payment_id };            
		$.ajax({
			type: "POST",
			url: "/pages/ajax/remove_payment.php",
			data: dataString,
			cache: false
		}).done(function(x) {
			if(x == "success"){
				location.reload();
			}else{
				alert(x);
			}
		});
	}
}

function loadNotes(order_id){
	var dataString = { order_id: order_id };            
	$.ajax({
		type: "POST",
		url: "/pages/ajax/load_notes.php",
		data: dataString,
		cache: false
	}).done(function(x) {
		$("#loadNotes").html(x);
	});
}

function removeNote(note_id, order_id){
	if(confirm("Are you sure you want to delete this note?") == true){
		var dataString = { note_id: note_id };            
		$.ajax({
			type: "POST",
			url: "/pages/ajax/remove_note.php",
			data: dataString,
			cache: false
		}).done(function(x) {
			if(x == "true"){
				loadNotes(order_id);
			}else{
				alert("Note was not found!");
			}
		});
	}
}

function estDelDate(){
	shipment_date = $("input#shipment_date").val();
	delivery_method = $("select#delivery_method").val();
	if(shipment_date != ""){
		if($("#saturday").prop("checked")){
			saturday = 1;
		}else{
			saturday = 0;
		}
		var dataString = { saturday: saturday, shipment_date: shipment_date, delivery_method: delivery_method };            
		$.ajax({
			type: "POST",
			url: "/pages/ajax/est_del_date.php",
			data: dataString,
			cache: false
		}).done(function(x) {
			$("#delivery_date").val(x);
		});
	}
}

function sameAddress(){
	if($("#same_address").prop("checked")){
		line1 = $("input#billing_line1").val();
		line2 = $("input#billing_line2").val();
		line3 = $("input#billing_line3").val();
		line4 = $("input#billing_line4").val();
		postcode = $("input#billing_postcode").val();
		// Make Identical
		$("input#shipping_line1").val(line1);
		$("input#shipping_line2").val(line2);
		$("input#shipping_line3").val(line3);
		$("input#shipping_line4").val(line4);
		$("input#shipping_postcode").val(postcode);
		// Make Read Only
		$("input#shipping_line1").prop("readonly", true);
		$("input#shipping_line2").prop("readonly", true);
		$("input#shipping_line3").prop("readonly", true);
		$("input#shipping_line4").prop("readonly", true);
		$("input#shipping_postcode").prop("readonly", true);
	}else{
		$("input#shipping_line1").prop("readonly", false);
		$("input#shipping_line2").prop("readonly", false);
		$("input#shipping_line3").prop("readonly", false);
		$("input#shipping_line4").prop("readonly", false);
		$("input#shipping_postcode").prop("readonly", false);
	}
}

var i = 1;
$("#duplicate-item").click(function(){
	$("table#item-table tbody tr:first").clone().find("input").each(function(){
		$(this).val("").attr("id", function(_, id) { return id + i });
	}).end().appendTo("table#item-table");
	i++;
});

function get_stock_subcat(all){
	$.post("/pages/ajax/get_stock_subcat.php",{
		cat_id: $("#select_cat").val(),
		all: all
	}, function (x){
		$("#subcat").html(x);
	})
}

function get_subcat(element_id){
	$.post("/pages/ajax/get_subcat.php",{
		insert_element_id: element_id,
		cat_id: $("#select_cat_" + element_id).val(),
	}, function (x){
		$("#subcat_" + element_id).html(x);
	})
}

function get_items(){
	$("#loader").show();
	clearTimeout(delayTimer);
	delayTimer = setTimeout(function() {
		var dataString = { item_name: $("#search_item").val() };            
		$.ajax({
			type: "POST",
			url: "/pages/ajax/get_items.php",
			data: dataString,
			cache: false
		}).done(function(x) {
			$("#items").html(x);
			$("#loader").hide();
		});
	}, 1000);
}

function list_item_upc(){
	if(($("#search_item").val()).length <= 1){
		return;
	}else{
		$("#loader").show();
		clearTimeout(delayTimer);
		delayTimer = setTimeout(function() {
			var dataString = { item_name: $("#search_item").val() };            
			$.ajax({
				type: "POST",
				url: "/pages/ajax/list_item_upc.php",
				data: dataString,
				cache: false
			}).done(function(x) {
				$("#items").html(x);
				$("#loader").hide();
			});
		}, 1000);
	}
}

function check(element){
	$("input#" + element).prop("checked", true);
}

function get_services(){
	if(($("#search_service").val()).length <= 2){
		return;
	}else{
		$("#loader").show();
		clearTimeout(delayTimer);
		delayTimer = setTimeout(function() {
			var dataString = { service_name: $("#search_service").val() };            
			$.ajax({
				type: "POST",
				url: "/pages/ajax/get_services.php",
				data: dataString,
				cache: false
			}).done(function(x) {
				$("#services").html(x);
				$("#loader").hide();
			});
		}, 1000);
	}
}

function get_catitems(){
	$.post("/pages/ajax/get_catitems.php",{
		subcat_id: $("#select_subcat").val(),
	}, function (x){
		$("#catitems").html(x);
	})
}

function get_catitems_list(element_id){
	$.post("/pages/ajax/get_catitems_list.php",{
		insert_element_id: element_id,
		subcat_id: $("#select_subcat_" + element_id).val(),
	}, function (x){
		$("#catitems_list_" + element_id).html(x);
	})
}

function getTemplateList(type){
	clearTimeout(delayTimer);
	delayTimer = setTimeout(function() {
		var dataString = { category_id: $("#category").val(), type: type };            
		$.ajax({
			type: "POST",
			url: "/pages/ajax/getTemplateList.php",
			data: dataString,
			cache: false
		}).done(function(x) {
			$("#templates").html(x);
		});
	}, 1000);
}

function sendEmail(){
	template_id = $("#template").val();
	$("input[type=checkbox]").each(function () {
		if($(this).is(":checked")){
			if($(this).val() != "on"){
				window.open("/email/" + template_id + "/" + $(this).val(), "_blank");
			}
		}
	});
}

function sendSMS(){
	template_id = $("#sms_template").val();
	$("input[type=checkbox]").each(function () {
		if($(this).is(":checked")){
			if($(this).val() != "on"){
				window.open("/sms/send/" + template_id + "/" + $(this).val(), "_blank");
			}
		}
	});
}

function selectAll(element){
	$(element).select();
}

function money(obj){
	if(obj.value == ""){	
		obj.value = "0.00";
	}else if(obj.value == "0.00"){
		obj.value = "";
	}
	obj.value = Number(obj.value).toFixed(2);
}

$(".money").bind("change", function () { 
	$(this).val(function(i, v) { return parseFloat(v).toFixed(2); });
});

function changeTitle(title){
	document.title = "Tech-House MIS :: " + title;
}

function item_total(element_id){
	item_cost = document.getElementById("item_cost_" + element_id).value;
	item_quantity = document.getElementById("item_quantity_" + element_id).value;
	calculation = item_cost * item_quantity;
	document.getElementById("item_total_" + element_id).value = calculation.toFixed(2);
}

function new_item_total(element_id){
	item_cost = document.getElementById("new_item_cost_" + element_id).value;
	item_quantity = document.getElementById("new_item_quantity_" + element_id).value;
	calculation = item_cost * item_quantity;
	document.getElementById("new_item_total_" + element_id).value = calculation.toFixed(2);
}

function update_total(){
	var sum = 0;
	var shipping_charges = Number($("#shipping_charges").val());
	var other_charges = Number($("#other_charges").val());
	var amount_paid = Number($("#amount_paid").val());
	$(".item_total").each(function(){
		sum += Number($(this).val());
	});
	$(".new_item_total").each(function(){
		sum += Number($(this).val());
	});
	total = sum + shipping_charges + other_charges;
	if($("#vat").prop("checked")){
		total_sum = total * 1.2;
		var element = "#total_inc_vat";
		$("#total_exc_vat").val("0.00");
		$("#total_vat").val((total_sum - (total_sum / 1.2)).toFixed(2));
	}else{
		total_sum = total;
		var element = "#total_exc_vat";
		$("#total_inc_vat").val("0.00");
		$("#total_vat").val("0.00");
	}
	$(element).val(total_sum.toFixed(2));
	payment_due = total_sum - amount_paid;
	$("#payment_due").val(payment_due.toFixed(2));
}

function rma_sent(selection){
	if(selection.value == 1){
		$("input#date_sent").prop("disabled", false);
	}else{
		$("input#date_sent").prop("disabled", true).val("");
	}
}

function receivedStatus(element_id, type){
	if($("input#" + type + "_rcvd_back_" + element_id).prop("checked")){
		$("#" + type + "_return_" + element_id).hide();
		$("." + type + "_return_" + element_id).show();
	}else{
		$("." + type + "_return_" + element_id).hide();
		$("#" + type + "_return_" + element_id).show();
	}
}

function upgradeExtra(element_id){
	if($("input#paid_extra_" + element_id).prop("checked")){
		$("#extra_" + element_id).hide();
		$(".extra_" + element_id).show();
	}else{
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
	}
}

function returnAction(element_id){
	var type_selected = $("select#return_action_" + element_id).val();
	if(type_selected == "outstanding"){
		$("#" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$("input#rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".rejected_return_" + element_id + " input").val("");
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$("input#replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".replaced_return_" + element_id + " input").val("");
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#credited_" + element_id).hide();
		$("input#credit_note_id_" + element_id).val("");
		/* Credited */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$("input#upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#upgrade_new_upc_" + element_id).val("");
		$("input#upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".upgraded_return_" + element_id + " input").val("");
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$("input#paid_extra_" + element_id).prop("checked", false);
		$("#upgrade_paid_amount_" + element_id).val("");
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "rejected"){
		$("#" + type_selected + "_" + element_id).show();
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$("input#replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".replaced_return_" + element_id + " input").val("");
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#credited_" + element_id).hide();
		$("input#credit_note_id_" + element_id).val("");
		/* Credited */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$("input#upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#upgrade_new_upc_" + element_id).val("");
		$("input#upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".upgraded_return_" + element_id + " input").val("");
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$("input#paid_extra_" + element_id).prop("checked", false);
		$("#upgrade_paid_amount_" + element_id).val("");
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "replaced"){
		$("#" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$("input#rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".rejected_return_" + element_id + " input").val("");
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Credited */
		$("#credited_" + element_id).hide();
		$("input#credit_note_id_" + element_id).val("");
		/* Credited */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$("input#upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#upgrade_new_upc_" + element_id).val("");
		$("input#upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".upgraded_return_" + element_id + " input").val("");
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$("input#paid_extra_" + element_id).prop("checked", false);
		$("#upgrade_paid_amount_" + element_id).val("");
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "credited"){
		$("#" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$("input#rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".rejected_return_" + element_id + " input").val("");
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$("input#replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".replaced_return_" + element_id + " input").val("");
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$("input#upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#upgrade_new_upc_" + element_id).val("");
		$("input#upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".upgraded_return_" + element_id + " input").val("");
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$("input#paid_extra_" + element_id).prop("checked", false);
		$("#upgrade_paid_amount_" + element_id).val("");
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else{
		$("." + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$("input#rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".rejected_return_" + element_id + " input").val("");
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$("input#replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".replaced_return_" + element_id + " input").val("");
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#credited_" + element_id).hide();
		$("input#credit_note_id_" + element_id).val("");
		/* Credited */
	}
}

function editReturnAction(element_id){
	var type_selected = $("select#return_action_" + element_id).val();
	if(type_selected == "outstanding"){
		$("#" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#credited_" + element_id).hide();
		/* Credited */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "rejected"){
		$("#" + type_selected + "_" + element_id).show();
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#credited_" + element_id).hide();
		/* Credited */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "replaced"){
		$("#" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Credited */
		$("#credited_" + element_id).hide();
		/* Credited */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "credited"){
		$("#" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Upgraded */
		$(".upgraded_" + element_id).hide();
		$(".upgraded_return_" + element_id).hide();
		$("#upgraded_return_" + element_id).show();
		$(".extra_" + element_id).hide();
		$("#extra_" + element_id).show();
		/* Upgraded */
	}else{
		$("." + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#rejected_" + element_id).hide();
		$(".rejected_return_" + element_id).hide();
		$("#rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#replaced_" + element_id).hide();
		$(".replaced_return_" + element_id).hide();
		$("#replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#credited_" + element_id).hide();
		/* Credited */
	}
}

function newReceivedStatus(element_id, type){
	if($("input#new_" + type + "_rcvd_back_" + element_id).prop("checked")){
		$("#new_" + type + "_return_" + element_id).hide();
		$(".new_" + type + "_return_" + element_id).show();
	}else{
		$(".new_" + type + "_return_" + element_id).hide();
		$("#new_" + type + "_return_" + element_id).show();
	}
}

function newUpgradeExtra(element_id){
	if($("input#new_paid_extra_" + element_id).prop("checked")){
		$("#new_extra_" + element_id).hide();
		$(".new_extra_" + element_id).show();
	}else{
		$(".new_extra_" + element_id).hide();
		$("#new_extra_" + element_id).show();
	}
}

function newReturnAction(element_id){
	var type_selected = $("select#new_return_action_" + element_id).val();
	if(type_selected == "outstanding"){
		$("#new_" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#new_rejected_" + element_id).hide();
		$("input#new_rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".new_rejected_return_" + element_id + " input").val("");
		$(".new_rejected_return_" + element_id).hide();
		$("#new_rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#new_replaced_" + element_id).hide();
		$("input#new_replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".new_replaced_return_" + element_id + " input").val("");
		$(".new_replaced_return_" + element_id).hide();
		$("#new_replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#new_credited_" + element_id).hide();
		$("input#new_credit_note_id_" + element_id).val("");
		/* Credited */
		/* Upgraded */
		$(".new_upgraded_" + element_id).hide();
		$("input#new_upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#new_upgrade_new_upc_" + element_id).val("");
		$("input#new_upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".new_upgraded_return_" + element_id + " input").val("");
		$(".new_upgraded_return_" + element_id).hide();
		$("#new_upgraded_return_" + element_id).show();
		$("input#new_paid_extra_" + element_id).prop("checked", false);
		$("#new_upgrade_paid_amount_" + element_id).val("");
		$(".new_extra_" + element_id).hide();
		$("#new_extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "rejected"){
		$("#new_" + type_selected + "_" + element_id).show();
		/* Replaced */
		$("#new_replaced_" + element_id).hide();
		$("input#new_replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".new_replaced_return_" + element_id + " input").val("");
		$(".new_replaced_return_" + element_id).hide();
		$("#new_replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#new_credited_" + element_id).hide();
		$("input#new_credit_note_id_" + element_id).val("");
		/* Credited */
		/* Upgraded */
		$(".new_upgraded_" + element_id).hide();
		$("input#new_upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#new_upgrade_new_upc_" + element_id).val("");
		$("input#new_upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".new_upgraded_return_" + element_id + " input").val("");
		$(".new_upgraded_return_" + element_id).hide();
		$("#new_upgraded_return_" + element_id).show();
		$("input#new_paid_extra_" + element_id).prop("checked", false);
		$("#new_upgrade_paid_amount_" + element_id).val("");
		$(".new_extra_" + element_id).hide();
		$("#new_extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "replaced"){
		$("#new_" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#new_rejected_" + element_id).hide();
		$("input#new_rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".new_rejected_return_" + element_id + " input").val("");
		$(".new_rejected_return_" + element_id).hide();
		$("#new_rejected_return_" + element_id).show();
		/* Rejected */
		/* Credited */
		$("#new_credited_" + element_id).hide();
		$("input#new_credit_note_id_" + element_id).val("");
		/* Credited */
		/* Upgraded */
		$(".new_upgraded_" + element_id).hide();
		$("input#new_upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#new_upgrade_new_upc_" + element_id).val("");
		$("input#new_upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".new_upgraded_return_" + element_id + " input").val("");
		$(".new_upgraded_return_" + element_id).hide();
		$("#new_upgraded_return_" + element_id).show();
		$("input#new_paid_extra_" + element_id).prop("checked", false);
		$("#new_upgrade_paid_amount_" + element_id).val("");
		$(".new_extra_" + element_id).hide();
		$("#new_extra_" + element_id).show();
		/* Upgraded */
	}else if(type_selected == "credited"){
		$("#new_" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#new_rejected_" + element_id).hide();
		$("input#new_rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".new_rejected_return_" + element_id + " input").val("");
		$(".new_rejected_return_" + element_id).hide();
		$("#new_rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#new_replaced_" + element_id).hide();
		$("input#new_replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".new_replaced_return_" + element_id + " input").val("");
		$(".new_replaced_return_" + element_id).hide();
		$("#new_replaced_return_" + element_id).show();
		/* Replaced */
		/* Upgraded */
		$(".new_upgraded_" + element_id).hide();
		$("input#new_upgrade_reason_" + element_id).val("Found faulty and upgraded");
		$("input#new_upgrade_new_upc_" + element_id).val("");
		$("input#new_upgraded_rcvd_back_" + element_id).prop("checked", false);
		$(".new_upgraded_return_" + element_id + " input").val("");
		$(".new_upgraded_return_" + element_id).hide();
		$("#new_upgraded_return_" + element_id).show();
		$("input#new_paid_extra_" + element_id).prop("checked", false);
		$("#new_upgrade_paid_amount_" + element_id).val("");
		$(".new_extra_" + element_id).hide();
		$("#new_extra_" + element_id).show();
		/* Upgraded */
	}else{
		$(".new_" + type_selected + "_" + element_id).show();
		/* Rejected */
		$("#new_rejected_" + element_id).hide();
		$("input#new_rejected_rcvd_back_" + element_id).prop("checked", false);
		$(".new_rejected_return_" + element_id + " input").val("");
		$(".new_rejected_return_" + element_id).hide();
		$("#new_rejected_return_" + element_id).show();
		/* Rejected */
		/* Replaced */
		$("#new_replaced_" + element_id).hide();
		$("input#new_replaced_rcvd_back_" + element_id).prop("checked", false);
		$(".new_replaced_return_" + element_id + " input").val("");
		$(".new_replaced_return_" + element_id).hide();
		$("#new_replaced_return_" + element_id).show();
		/* Replaced */
		/* Credited */
		$("#new_credited_" + element_id).hide();
		$("input#new_credit_note_id_" + element_id).val("");
		/* Credited */
	}
}

function get_service_price(element_id, update){
	$.post("/pages/ajax/get_service_price.php",{
		service_id: $("#select_service_" + element_id).val()
	}, function (x){
		$("#service_cost_" + element_id).val(x);
		if(update === true){
			service_total(element_id);
			update_receipt_total();
		}
	})
}

function fillitemUPC(upc){
	$("input[name='upc[]']").each(function() {
		if ( this.value === '' ) {
			this.value = upc;
			return false;
		}
	});
}

function receipt_payment_type(amount_paid){
	if(amount_paid.value > 0){
		$("#payment_type").show();
	}else{
		$("#payment_type").hide();
	}
}

function receipt_discount_reason(discount){
	if(discount.value > 0){
		$("#discount_reason").show();
	}else{
		$("#discount_reason input").val("");
		$("#discount_reason").hide();
	}
}

function new_get_service_price(element_id){
	$.post("/pages/ajax/get_service_price.php",{
		service_id: $("#new_select_service_" + element_id).val()
	}, function (x){
		$("#new_service_cost_" + element_id).val(x);
		service_total(element_id);
		update_receipt_total();
	})
}

function service_total(element_id){
	service_cost = document.getElementById("service_cost_" + element_id).value;
	service_quantity = document.getElementById("service_quantity_" + element_id).value;
	calculation = service_cost * service_quantity;
	document.getElementById("service_total_" + element_id).value = calculation.toFixed(2);
}

function new_service_total(element_id){
	service_cost = document.getElementById("new_service_cost_" + element_id).value;
	service_quantity = document.getElementById("new_service_quantity_" + element_id).value;
	calculation = service_cost * service_quantity;
	document.getElementById("new_service_total_" + element_id).value = calculation.toFixed(2);
}

function update_receipt_total(){
	var sum = 0;
	var other_charges = Number($("#other_charges").val());
	var amount_paid = Number($("#amount_paid").val());
	var discount = Number($("#discount").val());
	$(".service_total").each(function(){
		sum += Number($(this).val());
	});
	$(".new_service_total").each(function(){
		sum += Number($(this).val());
	});
	if($("#status").val() == "cancelled"){
		total = 0;
	}else{
		total = sum + other_charges - discount;
	}
	var element = "#total";
	$(element).val(total.toFixed(2));
	payment_due = total - amount_paid;
	$("#payment_due").val(payment_due.toFixed(2));
}

function updateOrderTotal(type){
	shipping_inc_vat = $("input#shipping_inc_vat");
	upgrades_inc_vat = $("input#upgrades_inc_vat");
	other_inc_vat = $("input#other_inc_vat");
	shipping_exc_vat = $("input#shipping_exc_vat");
	upgrades_exc_vat = $("input#upgrades_exc_vat");
	other_exc_vat = $("input#other_exc_vat");
	if($("select#vat").val() == "20.00"){
		vat = 1.2;
	}else{
		vat = 1;
	}
	if(type == "exc"){
		$("input#shipping_inc_vat").val((Number(shipping_exc_vat.val()) * vat).toFixed(2));
		$("input#upgrades_inc_vat").val((Number(upgrades_exc_vat.val()) * vat).toFixed(2));
		$("input#other_inc_vat").val((Number(other_exc_vat.val()) * vat).toFixed(2));
	}else{
		$("input#shipping_exc_vat").val((Number(shipping_inc_vat.val()) / vat).toFixed(2));
		$("input#upgrades_exc_vat").val((Number(upgrades_inc_vat.val()) / vat).toFixed(2));
		$("input#other_exc_vat").val((Number(other_inc_vat.val()) / vat).toFixed(2));
	}
	shipping_inc_vat = Number($("input#shipping_inc_vat").val());
	upgrades_inc_vat = Number($("input#upgrades_inc_vat").val());
	other_inc_vat = Number($("input#other_inc_vat").val());
	shipping_exc_vat = Number($("input#shipping_exc_vat").val());
	upgrades_exc_vat = Number($("input#upgrades_exc_vat").val());
	other_exc_vat = Number($("input#other_exc_vat").val());
	total = shipping_exc_vat + upgrades_exc_vat + other_exc_vat;
	total_vat = shipping_inc_vat + upgrades_inc_vat + other_inc_vat;
	$("input#total_exc_vat").val(total.toFixed(2));
	$("input#total_inc_vat").val(total_vat.toFixed(2));
}

function paymentType(type){
	$(".payment").hide();
	$("#" + type).show();
}

function clearance(){
	if($("#cleared").prop("checked")){
		$("#clearance_date").show();
	}else{
		$("#clearance_date").hide();
	}
}

function calcVAT(type){
	calc_price_inc = $("input#calc_price_inc");
	calc_price_exc = $("input#calc_price_exc");
	if(type == "exc"){
		$("input#calc_price_inc").val((Number(calc_price_exc.val()) * 1.2).toFixed(2));
	}else{
		$("input#calc_price_exc").val((Number(calc_price_inc.val()) / 1.2).toFixed(2));
	}
	calc_price_inc = Number($("input#calc_price_inc").val());
	calc_price_exc = Number($("input#calc_price_exc").val());
	vat = calc_price_inc - calc_price_exc;
	$("input#calc_vat").val(vat.toFixed(2));

}

function fillCreditID(element, credit_note_id){
	$("#credit_note_id_" + element).val(credit_note_id);
	$("#credit_note_" + element).modal("hide")
}

function changePublic(status){
	if($(status).prop("checked")){
		$("input#public").val($("input#search_item").val()).prop("readonly", true);
	}else{
		$("input#public").val("").prop("readonly", false);
	}
}

function getReceipts(request){
	value = $(request).val();
	$("div.recTable").hide();
	$("select#services").val($("option:first:disabled").val());
	if(value == "activeService"){
		$("select#services").data("active", "yes");
		$("select#services").show();
	}else if(value == "activeServiceCompleted"){
		$("select#services").data("active", "no");
		$("select#services").show();
	}else{
		$("select#services").hide();
		$("div." + value).show();
	}
}

function requestByService(request){
	var dataString = { service_id: $(request).val() };
	var active = $(request).data("active");
	if(active == "yes"){
		requestType = "requestByService";
	}else{
		requestType = "requestByServiceCompleted";
	}
	$.ajax({
		type: "POST",
		url: "/pages/ajax/" + requestType + ".php",
		data: dataString,
		cache: false
	}).done(function(x){
		$("div.activeService").html(x).show();
	});
}

function duplicateEntry(type){
	$("input[type=checkbox]").each(function () {
		if($(this).is(":checked")){
			if($(this).val() != "on"){
				if(type == "order"){
					window.open("/orders/add/?duplicate=" + $(this).val(), "_blank");
				}else{
					window.open("/receipts/add/?duplicate=" + $(this).val(), "_blank");
				}
			}
		}
	});
}

function toggleCalculator(){
	if($("div#calculator-widget").is(":visible") == true){
		$("div#calculator-widget").hide();
		$("i#calculator-button").show();
	}else{
		$("div#calculator-widget").show();
		$("i#calculator-button").hide();
	}
}