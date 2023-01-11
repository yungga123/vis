<script>
    $('#form-addcustomervt').submit(function(e) {
        e.preventDefault();
        var me = $(this);
        $.ajax({
            url: me.attr('action'),
            type: 'post',
            data: me.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    toastr.success("Successfully Added!");

                    $('#customer_name').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_person').removeClass("is-invalid").addClass('is-valid');
                    $('#notes').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_number').removeClass("is-invalid").addClass('is-valid');
                    $('#email_address').removeClass("is-invalid").addClass('is-valid');
                    $('#source').removeClass("is-invalid").addClass('is-valid');
                    $('#address_province').removeClass("is-invalid").addClass('is-valid');
                    $('#address_city').removeClass("is-invalid").addClass('is-valid');
                    $('#address_brgy').removeClass("is-invalid").addClass('is-valid');
                    $('#address_sub').removeClass("is-invalid").addClass('is-valid');

                    $('#small_customer_name').html('');
                    $('#small_contact_person').html('');
                    $('#small_notes').html('');
                    $('#small_contact_number').html('');
                    $('#small_email_address').html('');
                    $('#small_source').html('');
                    $('#small_address_province').html('');
                    $('#small_address_city').html('');
                    $('#small_address_brgy').html('');
                    $('#small_address_sub').html('');


                    me[0].reset();


                } else {

                    toastr.error("Errors Occured!");
                    $('#customer_name').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_person').removeClass("is-invalid").addClass('is-valid');
                    $('#notes').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_number').removeClass("is-invalid").addClass('is-valid');
                    $('#email_address').removeClass("is-invalid").addClass('is-valid');
                    $('#source').removeClass("is-invalid").addClass('is-valid');
                    $('#address_province').removeClass("is-invalid").addClass('is-valid');
                    $('#address_city').removeClass("is-invalid").addClass('is-valid');
                    $('#address_brgy').removeClass("is-invalid").addClass('is-valid');
                    $('#address_sub').removeClass("is-invalid").addClass('is-valid');

                    $('#small_customer_name').html('');
                    $('#small_contact_person').html('');
                    $('#small_notes').html('');
                    $('#small_contact_number').html('');
                    $('#small_email_address').html('');
                    $('#small_source').html('');
                    $('#small_address_province').html('');
                    $('#small_address_city').html('');
                    $('#small_address_brgy').html('');
                    $('#small_address_sub').html('');

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

    
</script>