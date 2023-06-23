var formDd, modalDd;

$(document).ready(function () {

    formDd = "form_add_supplierDd";
    modalDd = "modal_add_supplierDd";

    /* Form for saving item */
    formSubmit($("#" + formDd), "continue", function (res, self) {
        const message = res.errors ?? res.message;

        if (res.status !== STATUS.ERROR) {
            self[0].reset();
            //refreshDataTable();
            notifMsgSwal(res.status, message, res.status);

            if ($(`#${modalDd}`).hasClass("edit")) {
                $(`#${modalDd}`).modal("hide");
            }
        }

        showAlertInForm(elems, message, res.status);
    });

});


function showDropdownModal() {
    $(`#${modalDd}`).modal("show");
}


