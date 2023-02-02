/* Declaration for global variable */
var STATUS = {
    SUCCESS: 'success',
    ERROR: 'error',
    INFO: 'info',
}, METHOD = {
    GET: 'GET',
    POST: 'POST'
}, dtTable;

$(function() {
    // Initialize toastr
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "4000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
});

/* Show loading overlay - need to include the templates/loading view to work */
function showLoading(id = 'modal_loading') {
    $('#'+ id).modal('show');
}

/* Close loading overlay - need to include the templates/loading view to work */
function closeLoading(id = 'modal_loading') {
    $('#'+ id).modal('hide');
}

/* General notif message */
function notifMsg(message, status = STATUS.SUCCESS, objMsg = false) {
    if (objMsg || (! isEmpty(message) && isObject(message))) {
        $.each(message, function (key, value){
            notifMsg(value, status);
        });
    } else {
        switch (status) {
            case STATUS.SUCCESS:
                toastr.success(message);
                break;
            case STATUS.ERROR:
                toastr.error(message);
                break;
            case STATUS.INFO:
                toastr.info(message);
                break;        
            default:
                break;
        }
    }
}

/* Catch error message */
function catchErrMsg(err) {
    if ($('modal_loading').length > 0) closeLoading();

    notifMsg('Error while process data! Please contact your system administrator.', STATUS.ERROR);
    console.log(err);
}

/* Show alert message in form or small elem */
function showAlertInForm(elems, errors, status = STATUS.ERROR, prefix = 'alert') {
    /* Clear elements first */
    clearAlertInForm(elems, status, prefix);

    if (isObject(errors) && ! isEmpty(errors)) {
        $.each(errors, (key, value) => {
            $('#' + key).removeClass('is-valid').addClass('is-invalid');
            $(`#${prefix}_${key}`).html(value);
        });
    }

    if (isString(errors) && status === STATUS.ERROR) notifMsg(errors, status);
}

/* Clear alert message in form or small elem */
function clearAlertInForm(elems, status, prefix = 'alert') {
    if (Array.isArray(elems) && ! isEmpty(elems)) {
        for (let i = 0; i < elems.length; i++) {
            const elem = elems[i];
            $('#'+ elem).removeClass('is-invalid');
            $(`#${prefix}_${elem}`).html('');

            if (status === STATUS.SUCCESS) $('#'+ elem).addClass('is-valid');
        }
    }
}

/* Load dataTable data */
function loadDataTable(table, route, type = METHOD.GET, options = {}) {
    let columnDefs = [
        {
            "targets": '_all',
            "defaultContent": "<i>No records found...</i>"
        }
    ], order = inObject(options, 'order') ? [options.order] : [];

    columnDefs.push(
        inObject(options, 'columnDefs') 
        ? options.columnDefs
        : {
            "targets": 0,
            "orderable": false
        } 
    );

    dtTable = $('#'+ table).DataTable({
        "processing": true,
        "scrollX": true,
        "autoWidth": false,
        "columnDefs": columnDefs,
        "order": order,
        "buttons": [{
            extend: "excel",
            exportOptions: {
                columns: ":visible"
            },
            text: "Export to Excel"
        }, "colvis"],
        "serverSide": true,
        "ajax": {
            url: route,
            type: type.toUpperCase() !== METHOD.POST ? METHOD.GET : METHOD.POST
        },
        "initComplete": function(settings, json) {
            dtTable.buttons().container().appendTo(`#${table}_wrapper .col-md-6:eq(0)`);
        }
    });
}

/* Refresh dataTable data */
function refreshDataTable(table = null) {
    if (! isEmpty(table)) table.DataTable().ajax.reload();
    else dtTable.ajax.reload();
}

/* 
 * Use for showing and hiding a password - dependent on input group password 
 * You can see reference from Views/settings/send_mail.php for the input group
 * passId = name of the password id selector
 * showPassId = name of the show password button id selector
*/
function passwordShowHideInit(passId = 'password', showPassId = 'show_password') {
    $('#'+ showPassId).on('click', function() {
        if ($(this).hasClass('show')) {
            $(this).removeClass('show').attr('title', 'Click here to show password!');
            $(this).children('i').removeClass().addClass('fas fa-eye');
            $('#'+ passId).attr('type', 'password');
        } else {
            $(this).addClass('show').attr('title', 'Click here to hide password!');
            $(this).children('i').removeClass().addClass('fas fa-eye-slash');
            $('#'+ passId).attr('type', 'text');
        }
    });
}

/* 
 * Small functions 
*/

/* Check if value is empty - from stackoverflow */
function isEmpty(value) {
    return (
      // null or undefined
      (value == null) ||
  
      // has length and it's zero
      (value.hasOwnProperty('length') && value.length === 0) ||
  
      // is an Object and has no keys
      (value.constructor === Object && Object.keys(value).length === 0)
    )
}

/* Check if param is Object or not - from stackoverflow */
function isObject(obj) {
    return (typeof obj === 'object' && obj !== null && !Array.isArray(obj));

    /* Another approach */
    // return (Object.prototype.toString.call(obj) === '[object Object]');
}

/* Check if param/value is string */
function isString(param) {
    return (Object.prototype.toString.call(param) === '[object String]');
}

/* Check if Object key exist */
function inObject(obj, key) {
    return (isObject(obj) 
        ? Object.prototype.hasOwnProperty.call(obj, key)
        : false);
        
    /* Another methods */
    // return (key in obj); // Using 'in'
    // return obj.hasOwnProperty(key); // Same as above
}

/* Check if param is Object or not - from stackoverflow */
function countObject(obj) {
    return Object.keys(obj).length;
}
