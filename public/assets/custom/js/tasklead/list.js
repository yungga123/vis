var table, modal, form, editRoute, removeRoute, elems;
$(document).ready(function () {
	table = "tasklead_table";
	modal = "modal_tasklead";
	form = "form_tasklead";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = [
		"employee_id",
        "quarter", 
        "status",
		"customer_type",
        "existing_customer",
        "customer_id",
        "branch_id",
        "project", 
        "project_amount", 
        "quotation_num", 
        "forecast_close_date",
        "min_forecast_date",
        "max_forecast_date",
        "remark_next_step", 
        "close_deal_date", 
        "project_start_date", 
        "project_finish_date"
	];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Tasklead");
		$(`#${form}`)[0].reset();
		$("#tasklead_id").val("");

		hideElements();

		let elements = [
			'status',
			'customer_type',
			'existing_customer',
			'customer_id',
			'branch_id',
			'remark_next_step'
		];

		$.each(elements, function(key,value){
			$('.'+value).attr('hidden',false);
		});

		$('#status').val("10.00");
		$('.status_text').val("10% -- Identified");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	const route = $("#" + table).data("url");
	loadDataTable(table, route, METHOD.POST);

	/* Form for saving item */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable();
			notifMsgSwal(res.status, message, res.status);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
		resetCustomer();
		$(`#${modal}`).modal("hide");
	});

    // On Change in Existing Customer
    $('#existing_customer').change(function() {

		let customer_type = $("#customer_type").val();
		let existing_customer = $(this).val();

		$('#branch_id').empty();
		$('#branch_id').attr('disabled',true);
        

        if (existing_customer == 1 && customer_type == "Commercial") {
            appendCustomer("get_customervt_url",1);
        } else if (existing_customer == 0 && customer_type == "Commercial") {
            appendCustomer("get_customervt_url",0);
        } else if (existing_customer == 1 && customer_type == "Residential") {
			appendCustomer("get_customerresidential",1);
		} else if (existing_customer == 0 && customer_type == "Residential") {
			appendCustomer("get_customerresidential",0);
		}
    });

	$('#customer_id').change(function() {
		let id = $(this).val();
		let url = "get_customervtbranch_url";
		let customer_type = $("#customer_type").val();

		if (customer_type == "Commercial") {
			appendBranch(url,id);
		} else {
			$('#branch_id').empty();
			$('#branch_id').attr('disabled',true);
            
		}
	});

	$("#customer_type").change(function() {
		$('#branch_id').empty();
		$('#branch_id').attr('disabled',true);
		$('#existing_customer').val("");
		$('#customer_id').empty();
		$('#customer_id').attr('disabled',true);
	});
});

/* Get item details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#tasklead_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					hideElements();
					$.each(res.data, (key, value) => {
						if (value == '0000-00-00' || value == '0.00') {
							value = null;
						}
						$(`input[name="${key}"]`).val(value);
						//console.log('Key:'+key,'Value:'+value);

						if(key == 'status' && value == '10.00'){
							$.each(elems, function(){
								showElements();
								$('.project').attr('hidden',false);
								$('.remark_next_step').attr('hidden',false);
								//console.log(elems);
							});
						}
					});


				}
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(removeRoute, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable();
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

function appendCustomer(id,forecast){
    let route = $('#'+id).val() + "?forecast="+ forecast;
    //let keys = '';

    $.ajax({
        url: route,
        dataType: "json",
        type: "get",
        success: function(response){
			$('#customer_id').removeAttr('disabled');
            $('#customer_id').empty();
            $('#customer_id').append($('<option>', {
                value: "not",
                text: "---Please Select---"
            }));
            $.each(response.data, (key,value) => {
                // keys = Object.keys(value);
                // console.log(keys);
                // console.log(value['customer_name']);

                $('#customer_id').append($('<option>', {
                    value: value['id'],
                    text: value['customer_name']
                }));
            });
        },
        error: function(){
            alert('Errors Occured');
        }
    });
}

function appendBranch(url,id) {
	let route = $('#'+url).val() + "?id="+ id;
    //let keys = '';

    $.ajax({
        url: route,
        dataType: "json",
        type: "get",
        success: function(response){
			$('#branch_id').removeAttr('disabled');
            $('#branch_id').empty();
            $('#branch_id').append($('<option>', {
                value: "not",
                text: "---Please Select---"
            }));
            $.each(response.data, (key,value) => {
                // keys = Object.keys(value);
                // console.log(keys);
                // console.log(value['customer_name']);

                $('#branch_id').append($('<option>', {
                    value: value['id'],
                    text: value['branch_name']
                }));
            });
        },
        error: function(){
            alert('Errors Occured');
        }
    });
}

function resetCustomer() {
	$('#customer_type').val("");
	$('#existing_customer').val("");
	$('#customer_id').empty();
	$('#customer_id').attr('disabled',true);
	$('#branch_id').empty();
	$('#branch_id').attr('disabled',true);
}

function hideElements(){
	$.each(elems,function(key,value){
		$('.'+value).attr('hidden',true);
	});
}

function showElements(){
	$.each(elems,function(key,value){
		$('.'+value).attr('hidden',false);
	});
}



