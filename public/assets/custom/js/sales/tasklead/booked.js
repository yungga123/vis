var table, form_upload;

$(document).ready(function () {
	table = "tasklead_booked_table";
	form_upload = "form_upload";

	const options = {
		columnDefs: {
			targets: [4, 5],
			orderable: false,
		},
	};

	loadDataTable(table, router.tasklead.booked_list, METHOD.POST, options);

	$("#" + form_upload).on("submit", function (e) {
		e.preventDefault();
		let id = $("#upload_id").val();
		let self = $(this);
		let url = self.attr("action");
		$("#tasklead_id").val(id);

		let formData = new FormData(this);

		$.ajax({
			url: url,
			type: METHOD.POST,
			data: formData,
			dataType: "json",
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {
				if (response.success == true) {
					notifMsgSwal("Success!", response.message, STATUS.SUCCESS);
					getTaskleadFiles(id);
					self[0].reset();
					$("#modal-addfile").modal("hide");
				} else {
					notifMsgSwal("Error!", response.errors, STATUS.ERROR);
				}
			},
		});
	});
});

function getBookedDetails(id) {
	$.post(
		router.tasklead.booked_details + "?tasklead_id=" + id,
		{ id: id },
		function (res) {
			$.each(res, function (key, value) {
				$("#upload_id").val(id);
				$(".project_amount").html(value.project_amount);
				$(".project_start_date").html(value.project_start_date);
				$(".project_finish_date").html(value.project_finish_date);
				$(".project").html(value.project);
				$(".customer_name").html(value.customer_name);
				$(".branch_name").html(value.branch_name);
				$(".contact_number").html(value.contact_number);
				$(".quotation_num").html(value.quotation_num);
				$(".forecast_close_date").html(value.forecast_close_date);
				$(".min_forecast_date").html(value.min_forecast_date);
				$(".max_forecast_date").html(value.max_forecast_date);
				$(".status1").html(value.status1);
				$(".employee_name").html(value.employee_name);
				getTaskleadFiles(id);
			});
		}
	);

	$.post(
		router.tasklead.booked_history + "?tasklead_id=" + id,
		{ id: id },
		function (res) {
			$.each(res, function (key, value) {
				if (value.status == "10.00%") {
					$(".post").append("<h3>Status: IDENTIFIED (10%)</h3>");
					$(".post").append("<p>Updated On: " + value.created_at + "</p>");
					$(".post").append(
						"<p>Remark Next Step: " + value.remark_next_step + "</p>"
					);
				}

				if (value.status == "30.00%") {
					$(".post").append("<h3>Status: QUALIFIED (30%)</h3>");
					$(".post").append("<p>Updated On: " + value.created_at + "</p>");
					$(".post").append("<p>Project: " + value.project + "</p>");
					$(".post").append(
						"<p>Remark Next Step: " + value.remark_next_step + "</p>"
					);
				}

				if (value.status == "50.00%") {
					$(".post").append("<h3>Status: DEVELOPED SOLUTION (50%)</h3>");
					$(".post").append("<p>Updated On: " + value.created_at + "</p>");
					$(".post").append(
						"<p>Quotation Num: " + value.quotation_num + "</p>"
					);
					$(".post").append(
						"<p>Remark Next Step: " + value.remark_next_step + "</p>"
					);
				}

				if (value.status == "70.00%") {
					$(".post").append("<h3>Status: EVALUATION (70%)</h3>");
					$(".post").append("<p>Updated On: " + value.created_at + "</p>");
					$(".post").append(
						"<p>Forecast Close Date: " + value.forecast_close_date + "</p>"
					);
					$(".post").append(
						"<p>Project Amount: " + value.project_amount + "</p>"
					);
					$(".post").append(
						"<p>Min Forecast Date: " + value.min_forecast_date + "</p>"
					);
					$(".post").append(
						"<p>Max Forecast Date: " + value.max_forecast_date + "</p>"
					);
					$(".post").append(
						"<p>Remark Next Step: " + value.remark_next_step + "</p>"
					);
				}

				if (value.status == "90.00%") {
					$(".post").append("<h3>Status: NEGOTIATION (90%)</h3>");
					$(".post").append("<p>Updated On: " + value.created_at + "</p>");
					$(".post").append(
						"<p>Project Amount: " + value.project_amount + "</p>"
					);
					$(".post").append(
						"<p>Remark Next Step: " + value.remark_next_step + "</p>"
					);
				}

				if (value.status == "100.00%") {
					$(".post").append("<h3>Status: BOOKED (100%)</h3>");
					$(".post").append("<p>Updated On: " + value.created_at + "</p>");
					$(".post").append(
						"<p>Close Deal Date: " + value.close_deal_date + "</p>"
					);
					$(".post").append(
						"<p>Project Start Date: " + value.project_start_date + "</p>"
					);
					$(".post").append(
						"<p>Project End Date: " + value.project_finish_date + "</p>"
					);
					$(".post").append(
						"<p>Project Duration: " + value.project_duration + "</p>"
					);
					$(".post").append(
						"<p>Remark Next Step: " + value.remark_next_step + "</p>"
					);
				}
			});
		}
	);
}

function getTaskleadFiles(id) {
	$.post(router.tasklead.booked_files, { id: id }, function (response) {
		$(".files").empty();
		$.each(response.map, function (key, value) {
			$(".files").append(
				"<li><a href='" +
					$("#download_url").val() +
					"?id=" +
					$("#upload_id").val() +
					"&file=" +
					value +
					"'class='btn-link text-secondary'><i class='far fa-fw fa-file-word'></i> " +
					value +
					"</a></li>"
			);
		});
	});
}

function downloadFile(id) {
	$.post(router.tasklead.booked_download, { id: id }, function (response) {
		notifMsgSwal("Success!", response.message, STATUS.SUCCESS);
	});
}
