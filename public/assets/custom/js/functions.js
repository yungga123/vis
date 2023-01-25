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