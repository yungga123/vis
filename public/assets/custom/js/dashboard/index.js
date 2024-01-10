$(document).ready(function () {
	getCurrentClockAttendance();
});

function toggleMoreInfo(param) {
	toggleElem($("#" + param + "_MORE_INFO"));
	toggleElem($("#" + param + "_LINK"));
}

function toggleElem(elem) {
	if (elem.hasClass("d-none")) elem.removeClass("d-none");
	else elem.addClass("d-none");
}
