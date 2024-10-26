var dropdownParent = "";

/**
 * Toggle job order label to either required or optional
 *
 * @returns {void}
 */
function toggleJobOrderLabel(isRequired = true) {
	if (isRequired) {
		$(".form-group.job_order label").addClass("required");

		return;
	}

	$(".form-group.job_order label").removeClass("required");
}

/**
 * Job Orders select2 via ajax data source
 *
 * @param {string} route 		Route/link
 * @param {string} elem 		Element name with id/class
 * @param {string} text 		Text or key
 * @param {callable} callback 	Callable function
 * @param {string} modal 		Modal name
 * @returns {void}
 */
function initSelect2JobOrders(route, elem, text, callback, modal) {
	const placeholder = "Search and select a job order";
	const options = {
		dropdownParent: modal ? `#${modal} .modal-content` : dropdownParent,
	};

	// Set default value
	elem = elem || "#job_order_id";
	text = text || "text";

	if (callback) {
		select2AjaxInit(elem, placeholder, route, text, callback, options);

		return;
	}

	select2AjaxInit(elem, placeholder, route, text, null, options);
}
