var table, modal, form, elems, select2Dropdown, dtOptions, selectedOptions;

$(document).ready(function () {
	table = "table_dropdown";
	modal = "modal_dropdown";
	form = "form_dropdown";
	elems = ["dropdown", "dropdown_type"];
	select2Dropdown = "#dropdown_type";

	select2Init("#filter_dtypes");
	dropdownInit(select2Dropdown);
	dropdownTypesInit();

	/* Load dataTable */
	dtOptions = {
		columnDefs: {
			orderable: false,
			targets: -1,
		},
	};
	loadDataTable(table, router.dropdown.list, METHOD.POST, dtOptions);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Dropdown");
		$(`#${form}`)[0].reset();
		$("input[name='dropdown_id']").val("");
		$(select2Dropdown).val("").trigger("change");

		clearAlertInForm(elems);
	});

	$(select2Dropdown)
		.on("select2:select", function (e) {
			$("#dropdown_type_text").val($(select2Dropdown + " :selected").text());

			if (!isEmpty($(this).val())) editCategoryToggleBtn(false);
			else editCategoryToggleBtn(true);
		})
		.trigger("change");

	/* Form for saving category */
	formSubmit($("#form_dropdown_category"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		clearAlertInForm(["dropdown_category"], res.status);

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable();
			notifMsgSwal(res.status, message, res.status);
			dropdownInit(select2Dropdown);
			toggleCategory(true);
			editCategoryToggleBtn(true);
			dropdownTypesInit();
			$("#dropdown_id_category").val("");

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		} else {
			if (isObject(message) && !isEmpty(message)) {
				$("#dropdown_category").removeClass("is-valid").addClass("is-invalid");
				$(`#alert_dropdown_category`).html(message.dropdown);
			}
		}
	});

	/* Form for saving dropdown */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable();
			notifMsgSwal(res.status, message, res.status);
			dropdownTypesInit();
			toggleCategory(true);
			editCategoryToggleBtn(true);
			$(select2Dropdown).val("").trigger("change");
			$("#dropdown_id").val("");

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* Reset and referesh dataTable */
function reset() {
	$("#filter_dtypes").val("").change();
	dtOptions.params = null;
	loadDataTable(table, router.dropdown.list, METHOD.POST, dtOptions, true);
}

/* Showing and hiding category form */
function toggleCategory(isHide = false) {
	const hide = "d-none";

	$(".btn-back-to-dropdown").removeAttr("disabled");
	if (isHide) {
		$("#form_dropdown_category").fadeOut(500).removeClass(hide).addClass(hide);
		$("#form_dropdown").removeClass();
		$(".btn-delete-category").addClass(hide);

		$("#form_dropdown_category #dropdown_id_category").val("");
		$("#form_dropdown_category #dropdown_category").val("");
	} else {
		$("#form_dropdown_category").fadeIn(500).removeClass(hide);
		$("#form_dropdown").removeClass(hide).addClass(hide);
		$(".btn-delete-category").removeClass(hide);

		if (isEmpty($("#dropdown_id_category").val())) {
			$(".btn-delete-category").addClass(hide);
		}
	}
}

/* Dropdown initialization */
function dropdownInit(select, type) {
	type = type || "category";

	$.post(router.dropdown.show, { dropdown_type: type })
		.then((res) => {
			if (res.status === STATUS.SUCCESS) {
				let dropdowns = "",
					others = "";
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, val) => {
						if (isEmpty(val.other_category_type)) {
							dropdowns += `<option value="${val.dropdown_id}">${val.dropdown}</option>`;
						} else {
							others += `<option value="${val.dropdown_id}">${val.dropdown}</option>`;
						}
					});

					if (type == "category" && others) {
						dropdowns += `<optgroup label="Other Categories">${others}</optgroup>`;
					}
				}

				if (!isEmpty(dropdowns)) {
					let isInitialized = $(select).hasClass("select2-hidden-accessible");
					if (isInitialized) $(select).select2("destroy");

					let options = '<option value="">Select Category</option>' + dropdowns;
					$(select).html("").append(options);
					$(select).select2().val("").trigger("change");
				}
			} else {
				console.log(res.message);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Dropdown types initialization */
function dropdownTypesInit(select) {
	select = select || "#filter_dtypes";

	function select2OnEvent(event) {
		$(select).on(event, function (e) {
			let options = dtOptions;
			options.params = $(this).val();
			selectedOptions = $(this).val();
			loadDataTable(table, router.dropdown.list, METHOD.POST, options, true);
		});
	}

	select2OnEvent("select2:select");
	select2OnEvent("select2:unselect");

	$.get(router.dropdown.types)
		.then((res) => {
			if (res.status === STATUS.SUCCESS) {
				let types = "";
				if (inObject(res, "data") && !isEmpty(res.data)) {
					types = $.map(res.data, function (val, key) {
						return `<option value="${val.parent_id}">${val.dropdown_type}</option>`;
					}).join("");
				}

				if (!isEmpty(types)) {
					let isInitialized = $(select).hasClass("select2-hidden-accessible");
					if (isInitialized) $(select).select2("destroy");

					$(select).html("").append(types);
					$(select).select2().val(selectedOptions).trigger("change");
				}
			} else {
				console.log(res.message);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Toggle for edit button for category */
function editCategoryToggleBtn(hide = false) {
	if (hide) $(".btn-edit-category").addClass("d-none");
	else $(".btn-edit-category").removeClass("d-none");
}

/* Populating data for update */
function editCategory() {
	if (!isEmpty($(select2Dropdown).val())) {
		$("#form_dropdown_category #dropdown_id_category").val(
			$(select2Dropdown).val()
		);
		$("#form_dropdown_category #dropdown_category").val(
			$(select2Dropdown + " :selected").text()
		);
		$(".btn-delete-category").removeClass("d-none");

		toggleCategory();
	} else {
		alert("Please select a category first!");
	}
}

/* Delete category */
function deleteCategory() {
	let id = $("#dropdown_id_category").val();
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(router.dropdown.delete, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					notifMsgSwal(res.status, message, res.status);

					if (res.status === STATUS.SUCCESS) {
						refreshDataTable();
						dropdownInit(select2Dropdown);
						toggleCategory(true);
						editCategoryToggleBtn(true);
						dropdownTypesInit();
						$("#dropdown_id_category").val("");
					}
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

/* Get dropdown details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Dropdown");

	clearAlertInForm(elems);
	showLoading();

	$.post(router.dropdown.edit, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$(`#${modal}`).modal("show");

				if (inObject(res, "data") && !isEmpty(res.data)) {
					if (res.data.dropdown_type === "CATEGORY") {
						toggleCategory();
						$(".btn-back-to-dropdown").attr("disabled", true);
						$("#dropdown_id_category").val(id);
						$("#dropdown_category").val(res.data.dropdown);
						$(`#${modal} .modal-title`).text("Edit Category");
					} else {
						toggleCategory(true);
						$("#dropdown_id").val(id);
						$("#dropdown").val(res.data.dropdown);
						$(select2Dropdown).val(res.data.parent_id).trigger("change");
					}
				}
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Delete record */
function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(router.dropdown.delete, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					notifMsgSwal(res.status, message, res.status);

					if (res.status === STATUS.SUCCESS) {
						refreshDataTable();
						dropdownInit(select2Dropdown);
						dropdownTypesInit();
					}
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}
