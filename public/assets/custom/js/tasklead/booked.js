var table;

$(document).ready(function () {
	table = "tasklead_booked_table";

	const route = $("#" + table).data("url"),
		options = {
			columnDefs: {
				targets: [4, 5],
				orderable: false,
			},
		};

	loadDataTable(table, route, METHOD.POST, options);
});
