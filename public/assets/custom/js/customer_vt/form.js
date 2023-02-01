$('#form-addcustomervt').submit(function(e) {
    e.preventDefault();
    var me = $(this);
    $.ajax({
        url: me.attr('action'),
        type: 'post',
        data: me.serialize(),
        dataType: 'json',
        success: function(response) {
            $('.form :input').each(
                function(){
                    var input = $(this);
                    input.removeClass("is-invalid").addClass("is-valid");
                }
            );
            $('.form small').each(
                function(){
                    var input = $(this);
                    input.html('');
                }
            );

            if (response.success == true) {
                toastr.success("Successfully Added!");
                me[0].reset();

            } else {

                toastr.error("Errors Occured!");

                $.each(response.messages, function(key, value) {
                    if (value != '') {
                        $('#' + key).removeClass("is-valid").addClass("is-invalid");
                        $('#small_' + key).html(value);
                    }
                });
            }

        }
    });
});

