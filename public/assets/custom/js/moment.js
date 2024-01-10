/**
 * Get the sundays between weeks/date range
 *
 * @param {object|string} startDate
 * @param {object|string} endDate
 *
 * @return {string|object}
 */
function getSundaysBetweenDates(startDate, endDate, format = true) {
	const sundays = [];

	// Set the start date to the first Sunday on or before the given startDate
	const _startSunday = moment(startDate).startOf("week");

	// Iterate through the weeks
	while (_startSunday.isBefore(endDate)) {
		// Check if start sunday is same or after the start date, then
		if (_startSunday.isSameOrAfter(startDate)) {
			// Clone the moment object to avoid reference issues
			sundays.push(_startSunday.clone());
		}

		_startSunday.add(1, "week"); // Move to the next week
	}

	return format ? sundays.map((date) => date.format("YYYY-MM-DD")) : sundays;
}

/**
 * Get the count days between date range
 *
 * @param {object|string} startDate
 * @param {object|string} endDate
 * @param {string} unit     Like days, weeks, months and years
 *
 * @return {string|object}
 */
function getDateDiffCount(startDate, endDate, unit = "days") {
	const _startDate = moment(startDate);
	const _endDate = moment(endDate);

	return Math.floor(_endDate.diff(_startDate, unit) + 1);
}
