var table,
	modal,
	form,
	elems,
	selectedCategory,
	subCategory = {},
	otherCategoryType = null,
	otherCategoryTypeSelector = null;

$(document).ready(function () {
	table = "inventory_table";
	modal = "modal_inventory";
	form = "form_inventory";
	elems = [
		"category",
		"sub_category",
		"item_brand",
		"item_model",
		"item_description",
		"item_size",
		"item_sdp",
		"item_srp",
		"project_price",
		"stocks",
		"stock_unit",
		"date_of_purchase",
		"supplier",
		"location",
	];

	select2Init();
	$("#filter_category").on("select2:select", function (e) {
		let selector = "#filter_sub_category";
		dropdownInit(selector, $(this).val());
	});

	$("#category").on("select2:select", function (e) {
		selectedCategory = $(this).val();
		let isNotEmptyNotTheSameType =
			!isEmpty(subCategory) && subCategory.type != selectedCategory;

		if (isEmpty(subCategory) || isNotEmptyNotTheSameType) {
			dropdownInit("#sub_category", selectedCategory);
		}
	});

	dropdownInit("#item_brand", "BRAND");
	dropdownInit("#item_size", "SIZE", "", true);
	dropdownInit("#stock_unit", "UNIT", "", true);

	/* Load dataTable */
	loadDataTable(table, router.inventory.list, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Item");
		$(`#${form}`)[0].reset();
		$("#inventory_id").val("");

		clearSelectionSelect2();
		clearAlertInForm(elems);
	});

	/* Form for saving item */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable();
			notifMsgSwal(res.status, message, res.status);
			clearSelectionSelect2();
			$("#inventory_id").val("");

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});

	/* Form for saving dropdown */
	formSubmit($("#form_dropdown"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#dropdown_id").val("");
			$("#other_category_type").val("");
			$("#modal_dropdown").modal("hide");
			dropdownInit(otherCategoryTypeSelector, otherCategoryType, "", true);
		}

		showAlertInForm(["dropdown"], message, res.status);
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	let category = getSelect2Selection("#filter_category");

	showLoading();
	if (!isEmpty(category)) {
		let options = {
			params: {
				category: category,
				sub_dropdown: getSelect2Selection("#filter_sub_category"),
			},
		};

		if (reset) {
			options.params = null;
			clearSelect2Selection("#filter_category");
			clearSelect2Selection("#filter_sub_category");
			$("#filter_sub_category").html("");
		}

		loadDataTable(table, router.inventory.list, METHOD.POST, options, true);
	} else {
		closeLoading();
		if (reset) return;
		notifMsgSwal(TITLE.WARNING, "Please select a category first!", STATUS.INFO);
	}
	closeLoading();
}

/* Dropdown initialization */
function dropdownInit(select, type, val = "", optional = false) {
	type = type || "category";

	$.post(router.dropdown.show, { dropdown_type: type })
		.then((res) => {
			if (res.status === STATUS.SUCCESS) {
				let dropdowns = "";
				subCategory.data = res.data;
				subCategory.type = res.type;

				if (inObject(res, "data") && !isEmpty(res.data)) {
					dropdowns = $.map(res.data, function (val, key) {
						return `<option value="${val.dropdown_id}">${val.dropdown}</option>`;
					}).join("");
				}

				if (isSelect2Initialized(select)) $(select).select2("destroy");
				if (optional) dropdowns += '<option value="na">Not Applicable</option>';
				$(select).html("").append(dropdowns);
				$(select).select2().val(val).trigger("change");
			} else {
				console.log(res.message);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function openDropdownModal(title, val, selector) {
	$("#modal_dropdown").modal("show");
	$("#modal_dropdown .modal-title").text("Add " + title);
	$("#other_category_type").val(val);
	otherCategoryType = val;
	otherCategoryTypeSelector = selector;
}

function clearSelectionSelect2() {
	$("#category").val("").trigger("change");
	$("#sub_category").val("").trigger("change");
	$("#item_brand").val("").trigger("change");
	$("#item_size").val("").trigger("change");
	$("#stock_unit").val("").trigger("change");
}

/* Get item details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#inventory_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.inventory.edit, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$.each(res.data, (key, value) => $(`input[name="${key}"]`).val(value));
				$("#category").val(res.data.category).trigger("change");
				dropdownInit("#sub_category", res.data.category, res.data.sub_category);
				dropdownInit("#item_brand", "BRAND", res.data.item_brand);
				dropdownInit("#item_size", "SIZE", res.data.item_size, true);
				dropdownInit("#stock_unit", "UNIT", res.data.stock_unit, true);
				$("#encoder").val(res.data.encoder_name);
				$(`#${modal}`).modal("show");
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
			$.post(router.inventory.delete, { id: id })
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
