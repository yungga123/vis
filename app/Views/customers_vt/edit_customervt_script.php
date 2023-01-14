<script>


    $("#customer_name").val("<?= $customerVt["customer_name"] ?>");
    $("#contact_person").val("<?= $customerVt["contact_person"] ?>");
    $("#address_province").val("<?= $customerVt["address_province"] ?>");
    $("#address_city").val("<?= $customerVt["address_city"] ?>");
    $("#address_brgy").val("<?= $customerVt["address_brgy"] ?>");
    $("#address_sub").val("<?= $customerVt["address_sub"] ?>");
    $("#contact_number").val("<?= $customerVt["contact_number"] ?>");
    $("#email_address").val("<?= $customerVt["email_address"] ?>");
    $("#source").val("<?= $customerVt["source"] ?>");
    $("#notes").val("<?= $customerVt["notes"] ?>");



    $('#form-editcustomervt').submit(function(e) {
        e.preventDefault();
        var me = $(this);
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
            "timeOut": "2000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        $.ajax({
            url: me.attr('action'),
            type: 'post',
            data: me.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    toastr.success("Successfully Edited! You will return to the table in 2 seconds.");

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

                    setTimeout(function() {
                        javascript:history.back();
                    }, 2000);


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