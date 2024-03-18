function onChangeCustomerType(callaback) {
	$('input[name="customer_type"]').on("change", function () {
		// Clear selection first
		clearSelect2Selection("#customer_id");

		/* Change customers display based on 
		checked type (commerical or residential) */
		initSelect2Customers($(this).val(), callaback);
	});
}

/* Customers select2 via ajax data source */
function initSelect2Customers(customer_type, withBranches) {
	const options = {
		customer_type: customer_type || "commercial",
	};
	const callback = withBranches ? withBranches : null;

	select2AjaxInit(
		"#customer_id",
		"Select a client",
		router.admin.common.customers,
		"text",
		callback,
		options
	);

	if (isEmpty(customer_type)) clearSelect2Selection("#customer_id");
}
