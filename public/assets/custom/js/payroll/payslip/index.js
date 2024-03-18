var table, modal, form, elems;

$(document).ready(function () {
	table = "payslip_table";
	modal = "payslip_modal";
	form = "payslip_form";
	elems = [];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Employee");
		$(`#${form}`)[0].reset();
		$("#id").val("");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	loadDataTable(table, router.payroll.payslip.list, METHOD.POST);
});

/* For filtering and reseting */
function filterData(reset = false) {
	const start_date = $("#filter_start_date").val();
	const end_date = $("#filter_end_date").val();
	const params = {
		start_date: start_date,
		end_date: end_date,
	};
	const condition = !isEmpty(start_date) || !isEmpty(end_date);

	filterParam(
		router.payroll.payslip.list,
		table,
		params,
		condition,
		() => {
			$("#filter_start_date").val("");
			$("#filter_end_date").val("");
		},
		reset
	);
}

/* Delete record */
function edit(id) {
	window.ri;
}

/* Delete record */
function remove(id) {
	deleteRecord(router.payroll.payslip.delete, { id: id }, table);
}
