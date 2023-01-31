$(document).ready(function() {
    const elems = [
        'employee_id',
        'username',
        'password',
        'access_level',
    ], prefix = 'small', modal_loading = $('#modal_loading');

    if (! isEmpty($('#employee_id').data('value'))) {
        $('#employee_id').val($('#employee_id').data('value')).change();
        $('#access_level').val($('#access_level').data('value')).change();
        $("#employee_id").attr('readonly', true);
        $("#small_password").text("Leave it blank if you will not update the password.");
    }

    $('.btn-trigger').click(function() {
        notifMsg('This is info!', 'info');
    })

    $('#form-post-add-account').submit(function(e) {
        e.preventDefault();
        showLoading();
        var self = $(this);
        
        $.ajax({
            url: self.attr('action'),
            type: METHOD.POST,
            data: self.serialize(),
            dataType: 'json',
            success: function(res) {
                const message = res.errors ?? res.message;

                if (res.status !== STATUS.ERROR) {
                    notifMsg(message, res.status);
                    self[0].reset();
                }

                closeLoading();
                showAlertInForm(elems, message, res.status, prefix);
            }
        });
    });

    $('#form-post-edit-account').submit(function(e) {
        e.preventDefault();
        showLoading();
        var self = $(this);
        
        $.ajax({
            url: self.attr('action'),
            type: METHOD.POST,
            data: self.serialize(),
            dataType: 'json',
            success: function(res) {
                const message = res.errors ?? res.message;
                let msg = " This window will close in 10 seconds..";

                if (res.status !== STATUS.ERROR) {
                    self[0].reset();
                    notifMsg(message + msg, res.status);
                    setTimeout(function() { window.close() }, 10000);
                }

                closeLoading();
                showAlertInForm(elems, message, res.status, prefix);
            }
        });
    });
});