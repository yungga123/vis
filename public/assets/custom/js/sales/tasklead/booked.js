var table, form_upload, _dropzone;

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

	// Initialize dropzone
	_dropzoneInit();

	$(".btn-addfile").on("click", () => {
		// Clear files
		_dropzone.removeAllFiles(true);
	});
});

function getBookedDetails(id) {
	$("#upload_tasklead_id").val(id);
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

				// Fetch files by id
				fetchFiles(id);
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

/* Get files */
function fetchFiles(id) {
	$.get(router.tasklead.booked.files + "/" + id)
		.then((res) => {
			let files = "";
			$.each(res.files, function (i, file) {
				files += `
					<li class="hover" id="file_${file._id}_${i}">
						<a href="${file.url}" class="btn-link text-secondary" target="_blank">
							<i class="${file.icon}"></i> ${file.name}
							<span>
								<a href="javascript:void(0)" class="text-danger"
									onclick="removeFiles(${file._id}, '${file.name}', ${i})"
									title="Remove file"
								>
									<i class="fas fa-sm fa-trash hover-show"></i>
								</a>
							</span>
						</a>
					</li>
				`;
			});

			$(".files").empty().html(files);
		})
		.catch((err) => catchErrMsg(err));
}

/* Remove files */
function removeFiles(id, filename, i) {
	const data = { id: id, filename: filename };

	swalNotifConfirm(
		() => {
			$.post(router.tasklead.booked.files_remove, data)
				.then((res) => {
					const message = res.errors ?? res.message;
					notifMsgSwal(res.status, message, res.status);

					if (res.status === STATUS.SUCCESS)
						$(`ul.files li#file_${id}_${i}`).remove();
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		"Are you sure you want to remove this file? You will not be able to recover this data!",
		STATUS.WARNING
	);
}

/* Dropzone init */
function _dropzoneInit() {
	const form = "upload_form";
	const button = "#modal-addfile .btn-upload";

	_dropzone = dropzoneInit(form, null, button);
	dzOnSuccessEvent(_dropzone, button, _dzOnSuccessEvent);
}

// Dropzone on success event custom callback
function _dzOnSuccessEvent(files, response) {
	const message = response.errors ?? response.message;

	if (response.status !== STATUS.ERROR) {
		$("#modal-addfile").modal("hide");
		fetchFiles(response.id);
	}

	notifMsgSwal(response.status, message, response.status);
}
