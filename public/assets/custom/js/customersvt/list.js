var table,modal,form,elems;

$(document).ready(function(){
    table = "customervt_table";
    form = "form_customervt";
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
			targets: 1,
			orderable: false,
		},
	};

    loadDataTable(table, route, METHOD.POST, options);

    $("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Item");
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

