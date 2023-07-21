var formDd, modalDd, tableDd;

$(document).ready(function () {
    tableDd = "supplier_dropdowns_table";
    formDd = "form_add_supplierDd";
    modalDd = "modal_add_supplierDd";
    elemsDd = [
        "dropdown"
    ];

    /* Load dataTable */
	const route = $("#" + tableDd).data("url");
	loadDataTable(tableDd, route, METHOD.POST);

    /* Form for saving item */
    formSubmit($("#" + formDd), "continue", function (res, self) {
        const message = res.errors ?? res.message;

        if (res.status !== STATUS.ERROR) {
            self[0].reset();
            
            //refreshDataTable();
            notifMsgSwal(res.status, message, res.status);

            if ($(`#${modalDd}`).hasClass("edit")) {
                $(`#${modalDd}`).modal("hide");
            }
            fetchDropdowns();
        }

        showAlertInForm(elemsDd, message, res.status);
    });

});


function showDropdownModal() {
    $(`#${modalDd}`).modal("show");
    fetchDropdowns();
}

/* Get supplier details */
function edit(id) {
	fetchDropdowns();
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Supplier");
	$("#supplier_id").val(id);

	clearAlertInForm(elemsDd);
	
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


function fetchDropdowns() {
    let url = router.dropdown.show;
    $('#supplier_type').empty();
    $('#supplier_type').append("<option value=''>---Please Select---</option>");

    $.ajax({
        type: "POST",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {

            $.each(response.dropdowns, function (key, val) { 
                //console.log(val.dropdown);
                
                $('#supplier_type').append("<option value='"+val.dropdown+"'>"+val.dropdown+"</option>");
            });
        }
    });
}


