<script>
    $('#customer_id').val('<?= $customerBranchModel['customer_id'] ?>');
    $('#branch_name').val('<?= $customerBranchModel['branch_name'] ?>');
    $('#address_province').val('<?= $customerBranchModel['address_province'] ?>');
    $('#address_city').val('<?= $customerBranchModel['address_city'] ?>');
    $('#address_brgy').val('<?= $customerBranchModel['address_brgy'] ?>');
    $('#address_sub').val('<?= $customerBranchModel['address_sub'] ?>');
    $('#contact_number').val('<?= $customerBranchModel['contact_number'] ?>');
    $('#contact_person').val('<?= $customerBranchModel['contact_person'] ?>');
    $('#email_address').val('<?= $customerBranchModel['email_address'] ?>');
    $('#notes').val('<?= $customerBranchModel['notes'] ?>');

    $('#customer_id option').attr('disabled',true);
    $('#customer_id option[value="<?= $customerBranchModel['customer_id'] ?>"]').attr('disabled',false);

    $('#form-editcustomerbranch').submit(function(e) {
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

                    $('#customer_id').removeClass("is-invalid").addClass('is-valid');
                    $('#branch_name').removeClass("is-invalid").addClass('is-valid');
                    $('#address_province').removeClass("is-invalid").addClass('is-valid');
                    $('#address_city').removeClass("is-invalid").addClass('is-valid');
                    $('#address_brgy').removeClass("is-invalid").addClass('is-valid');
                    $('#address_sub').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_number').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_person').removeClass("is-invalid").addClass('is-valid');
                    $('#email_address').removeClass("is-invalid").addClass('is-valid');
                    $('#notes').removeClass("is-invalid").addClass('is-valid');

                    $('#small_customer_id').html('');
                    $('#small_branch_name').html('');
                    $('#small_address_province').html('');
                    $('#small_address_city').html('');
                    $('#small_address_brgy').html('');
                    $('#small_address_sub').html('');
                    $('#small_contact_number').html('');
                    $('#small_contact_person').html('');
                    $('#small_email_address').html('');
                    $('#small_notes').html('');

                    setTimeout(function() {
                        javascript: history.back();
                    }, 2000);


                } else {

                    toastr.error("Errors Occured!");
                    $('#customer_id').removeClass("is-invalid").addClass('is-valid');
                    $('#branch_name').removeClass("is-invalid").addClass('is-valid');
                    $('#address_province').removeClass("is-invalid").addClass('is-valid');
                    $('#address_city').removeClass("is-invalid").addClass('is-valid');
                    $('#address_brgy').removeClass("is-invalid").addClass('is-valid');
                    $('#address_sub').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_number').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_person').removeClass("is-invalid").addClass('is-valid');
                    $('#email_address').removeClass("is-invalid").addClass('is-valid');
                    $('#notes').removeClass("is-invalid").addClass('is-valid');

                    $('#small_customer_id').html('');
                    $('#small_branch_name').html('');
                    $('#small_address_province').html('');
                    $('#small_address_city').html('');
                    $('#small_address_brgy').html('');
                    $('#small_address_sub').html('');
                    $('#small_contact_number').html('');
                    $('#small_contact_person').html('');
                    $('#small_email_address').html('');
                    $('#small_notes').html('');

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