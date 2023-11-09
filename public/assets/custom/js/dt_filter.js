/**
 * For filtering params like status
 *
 * @param {String} route        - the route/url for datatable list
 * @param {String} table        - the table name
 * @param {Object} params       - the params to be sent to request
 * @param {Bool} condition      - the condition to proceed to filter
 * @param {Function} callback   - the callback function to clear/reset filter selection
 * @param {Bool} reset          - whether to reset filter
 *
 * @return void
 */
function filterParam(route, table, params, condition, callback, reset = false) {
	showLoading();

	if (condition) {
		let options = { params: params };

		if (reset) {
			// Clear and reset selection of select2
			options.params = null;
			callback();
		}

		loadDataTable(table, route, METHOD.POST, options, true);
		closeLoading();
	} else {
		closeLoading();
		if (reset) return;

		notifMsgSwal(TITLE.WARNING, "Please select a filter first!", STATUS.INFO);
	}
}
