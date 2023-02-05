var table,modal,form,elems,editRoute,removeRoute;

$(document).ready(function(){
    table = "customervt_table";
    form = "form_customervt";
    editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
    elems = [
		"customer_name", 
        "contact_person", 
        "address_province",
        "address_city", 
        "address_brgy", 
        "address_sub", 
        "contact_number", 
        "email_address", 
        "source", 
        "notes"
	];
    modal = 'modal_customervt';
    let route = $("#"+table).data('url');

    /* Disable sorting for this column - default is 1st column. 
    1 = 2nd column of the table  */
	let options = {
		columnDefs: {
			targets: [0,1],
			orderable: false,
		},
	};

    loadDataTable(table, route, METHOD.POST, options);

    $("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Customer");
		$(`#${form}`)[0].reset();
		$("#customervt_id").val("");

		clearAlertInForm(elems);
	});


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

        showAlertInForm(elems, message, res.status, prefix = "small");
    });
});

/* Get item details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Customer");
	$("#customervt_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});
				}
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status, prefix='small');
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


