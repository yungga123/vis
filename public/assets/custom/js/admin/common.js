function onChangeCustomerType() {
	$('input[name="customer_type"]').on("change", function () {
		/* Change customers display based on 
		checked type (commerical or residential) */
		initSelect2Customers($(this).val());
	});
}

/* Customers select2 via ajax data source */
function initSelect2Customers(customer_type) {
	const options = {
		customer_type: customer_type || "commercial",
	};

	select2AjaxInit(
		"#customer_id",
		"Select a client",
		router.admin.common.customers,
		"text",
		null,
		options
	);
}
