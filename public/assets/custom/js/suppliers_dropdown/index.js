var formDd, modalDd;

$(document).ready(function () {

    formDd = "form_add_supplierDd";
    modalDd = "modal_add_supplierDd";
    elemsDd = [
        "dropdown"
    ];

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
            fetchDropdowns();
        }

        showAlertInForm(elemsDd, message, res.status);
    });

});


function showDropdownModal() {
    $(`#${modalDd}`).modal("show");
    fetchDropdowns();
}

function fetchDropdowns() {
    let url = router.dropdown.show;
    $('#supplier_type').empty();
    $('#supplier_type').append("<option value=''>---Please Select---</option>");

    $.ajax({
        type: "POST",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {

            $.each(response.dropdowns, function (key, val) { 
                //console.log(val.dropdown);
                
                $('#supplier_type').append("<option>"+val.dropdown+"</option>");
            });
        }
    });
}


