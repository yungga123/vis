var clientRoute = "",
	branchRoute = "";

/* Customers select2 via ajax data source */
function initSelect2Customers(route, customer_type, withBranches, selector) {
	const options = {
		customer_type: customer_type || "commercial",
	};
	const callback = withBranches ? withBranches : null;
	selector = selector || "#customer_id";
	clientRoute = route;

	select2AjaxInit(
		selector,
		"Select a client",
		route,
		"text",
		callback,
		options
	);

	if (isEmpty(customer_type)) clearSelect2Selection(selector);
}

/* Initialize select2 customer branches */
function initSelect2CustomerBranches(route, customer_id, branch_id, selector) {
	const options = {
		options: {
			not_select2_ajax: true,
			customer_id: customer_id,
		},
	};
	selector = selector || "#customer_branch_id";
	branchRoute = route;

	/* Get customer branches via ajax post */
	$.post(route, options)
		.then((res) => {
			select2Reinit(selector, "Please select a branch", res.data);

			if (branch_id) {
				setSelect2Selection(selector, branch_id);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Customer type on change */
function onChangeCustomerType(
	callback,
	customer_elem,
	cutomer_type_elem,
	branch_wrapper
) {
	customer_elem = customer_elem || "#customer_id";
	cutomer_type_elem = cutomer_type_elem || 'input[name="customer_type"]';
	branch_wrapper = branch_wrapper || "#client_branch_wrapper";

	$(cutomer_type_elem).on("change", function () {
		// Clear selection first
		clearSelect2Selection(customer_elem);

		if ($(this).val() === "residential") {
			$(branch_wrapper).addClass("d-none");
		}

		/**
		 * Change customers display based on
		 * checked type (commerical or residential)
		 * */
		initSelect2Customers(clientRoute, $(this).val(), callback);
	});
}

/* Customer on clear event */
function onSelectCustomer(selector, branch_wrapper, cutomer_type_elem) {
	_onEvent("select", selector, branch_wrapper, cutomer_type_elem);
}

/* Customer on clear event */
function onClearCustomer(selector, branch_wrapper, cutomer_type_elem) {
	_onEvent("clear", selector, branch_wrapper, cutomer_type_elem);
}

/** Customer on event */
function _onEvent(event, selector, branch_wrapper, cutomer_type_elem) {
	event = event || "select";
	selector = selector || "#customer_id";
	branch_wrapper = branch_wrapper || "#client_branch_wrapper";
	cutomer_type_elem =
		cutomer_type_elem || 'input[name="customer_type"]:checked';

	$(selector).on("select2:" + event, function () {
		const clientId = $(this).val();
		const _branchWrapper = $(branch_wrapper).length;

		if (_branchWrapper) {
			$(branch_wrapper).addClass("d-none");
		}

		let customer_type = $(cutomer_type_elem).val();

		customer_type = isEmpty(customer_type) ? "commercial" : customer_type;

		if (customer_type === "commercial" && clientId) {
			initSelect2CustomerBranches(branchRoute, clientId);

			if (_branchWrapper) {
				$(branch_wrapper).removeClass("d-none");
			}
		}
	});
}
