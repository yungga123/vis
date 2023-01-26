var STATUS = {
    SUCCESS: 'success',
    ERROR: 'error',
}

$(function() {
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

function notifMsg(message, type = 'success', objMsg = false) {
    if (objMsg) {
        $.each(message, function (key, value){
            notifMsg(value, type);
        });
    } else {
        if (type === STATUS.SUCCESS) toastr.success(message);
        else toastr.error(message);
    }
}

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