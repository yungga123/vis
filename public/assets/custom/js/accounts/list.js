var table, removeRoute;
$(document).ready(function () {
	table = "accounts_table";
	removeRoute = $("#remove_url").val();

	let route = $("#" + table).data("url");

	/* Disable sorting for this column - default is 1st column. 
    1 = 2nd column of the table  */
	let options = {
		columnDefs: {
			targets: 1,
			orderable: false,
		},
	};

	/* Load dataTable */
	loadDataTable(table, route, METHOD.POST, options);
});

function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.get(
				removeRoute + "/" + id,
				function (response) {
					let status = STATUS.SUCCESS,
						message = response.messages;

					if (response.success == true) {
						$("#modal-delete-account").modal("hide");
						refreshDataTable($("#" + table));
					} else {
						status = STATUS.ERROR;
					}

					notifMsgSwal(status, message, status);
				},
				"json"
			);
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}
