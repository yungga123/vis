var form, elems, options;

$(document).ready(function () {
	form = "form_export";
	elems = ["module", "start_date", "end_date"];
	options = $pjOptions;

	select2Init("#module");
	select2Init("#status");

	$("#module").on("change", function (e) {
		const val = $(this).val();

		_defaultStatusParams();

		if (!isEmpty(val) && inObject(options, val)) {
			const params = options[val];
			const _options =
				val === "INVENTORY"
					? params.options
					: formatOptionsForSelect2(params.options);

			_multipleOrSingle(params.type);
			_setStatusParams(params.name, _options);

			if (inObject(params.options, "mine")) {
				setSelect2Selection("#status", "mine");
			}
		}
	});
});

function _defaultStatusParams() {
	$("#wrapper_status label").text("Status");
	$("#wrapper_status select").empty();
	$("#status").attr("disabled", true);
}

function _setStatusParams(label, options) {
	$("#wrapper_status label").text(label || "Status");
	select2Reinit("#status", "", options);
	$("#status").removeAttr("disabled");
}

function _multipleOrSingle(type) {
	$("#status").removeAttr("multiple");
	$("#status").attr("name", "status");

	if (type === "multiple") {
		$("#status").attr("multiple", "true");
		$("#status").attr("name", "status[]");
	}
}
