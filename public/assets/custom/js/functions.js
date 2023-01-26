/* Declaration for global variable */
var STATUS = {
    SUCCESS: 'success',
    ERROR: 'error',
}

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

/* General notif message */
function notifMsg(message, type = 'success', objMsg = false) {
    if (objMsg || (! isEmpty(message) && isObject(message))) {
        $.each(message, function (key, value){
            notifMsg(value, type);
        });
    } else {
        if (type === STATUS.SUCCESS) toastr.success(message);
        else toastr.error(message);
    }
}

/* Catch error message */
function catchErrMsg(err) {
    notifMsg('Error while process data! Please contact your system administrator.', STATUS.ERROR);
    console.log(err);
}

/* Load dataTable data */
function loadDataTable(table, route, options = {}) {
    var dtTable =  $('#'+ table).DataTable({
        "processing": true,
        "scrollX": true,
        "autoWidth": false,
        "columnDefs": [{
            "targets": '_all',
            "defaultContent": "<i>No records found...</i>"
        }],
        "buttons": [{
            extend: "excel",
            exportOptions: {
                columns: ":visible"
            },
            text: "Export to Excel"
        }, "colvis"],
        "serverSide": true,
        "ajax": route,
        "initComplete": function(settings, json) {
            dtTable.buttons().container().appendTo(`#${table}_wrapper .col-md-6:eq(0)`);
        }
    });
}



/* 
 * Small functions 
*/

/* Check if param is Object or not - from stackoverflow */
function isObject(obj) {
    return (typeof obj === 'object' && obj !== null && !Array.isArray(obj));

    /* Another approach */
    // return (Object.prototype.toString.call(obj) === '[object Object]');
}

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