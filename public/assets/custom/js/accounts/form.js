$(document).ready(function() {
    const elems = [
        'employee_id',
        'username',
        'password',
        'access_level',
    ], prefix = 'small';

    if (! isEmpty($('#employee_id').data('value'))) {
        $('#employee_id').val($('#employee_id').data('value')).change();
        $('#access_level').val($('#access_level').data('value')).change();
        $("#employee_id").attr('readonly', true);
        $("#small_password").text("Leave it blank if you will not update the password.");
    }

    $('#form-post-add-account').submit(function(e) {
        e.preventDefault();
        var self = $(this);

        $.ajax({
            url: self.attr('action'),
            type: METHOD.POST,
            data: self.serialize(),
            dataType: 'json',
            success: function(response) {
                let status = STATUS.SUCCESS,
                    message = "Successfully Added!";

                if (response.success == true) {
                    notifMsg(message, status);
                    self[0].reset();
                } else {
                    status = STATUS.ERROR;
                    message = "Errors Occured!";
                }

                showAlertInForm(elems, response.messages, status, prefix);
            }
        });
    });

    $('#form-post-edit-account').submit(function(e) {
        e.preventDefault();
        var self = $(this);

        $.ajax({
            url: self.attr('action'),
            type: METHOD.POST,
            data: self.serialize(),
            dataType: 'json',
            success: function(response) {
                let status = STATUS.SUCCESS,
                    message = "Successfully Updated! This window will close in 3 seconds";

                if (response.success == true) {
                    notifMsg(message, status);
                    self[0].reset();
                    setTimeout(function() { window.close() }, 3000);
                } else {
                    status = STATUS.ERROR;
                    message = "Errors Occured!";
                }

                showAlertInForm(elems, response.messages, status, prefix);
            }
        });
    });
});