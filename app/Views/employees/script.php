
<script>
    $('#form-employee-add').submit(function(e) {
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

                    $('#employee_id').removeClass("is-invalid").addClass('is-valid');
                    $('#firstname').removeClass("is-invalid").addClass('is-valid');
                    $('#middlename').removeClass("is-invalid").addClass('is-valid');
                    $('#lastname').removeClass("is-invalid").addClass('is-valid');
                    $('#gender').removeClass("is-invalid").addClass('is-valid');
                    $('#civil_status').removeClass("is-invalid").addClass('is-valid');
                    $('#date_of_birth').removeClass("is-invalid").addClass('is-valid');
                    $('#place_of_birth').removeClass("is-invalid").addClass('is-valid');
                    $('#postal_code').removeClass("is-invalid").addClass('is-valid');
                    $('#language').removeClass("is-invalid").addClass('is-valid');
                    $('#address_province').removeClass("is-invalid").addClass('is-valid');
                    $('#address_city').removeClass("is-invalid").addClass('is-valid');
                    $('#address_brgy').removeClass("is-invalid").addClass('is-valid');
                    $('#address_sub').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_number').removeClass("is-invalid").addClass('is-valid');
                    $('#email_address').removeClass("is-invalid").addClass('is-valid');
                    $('#sss_no').removeClass("is-invalid").addClass('is-valid');
                    $('#tin_no').removeClass("is-invalid").addClass('is-valid');
                    $('#philhealth_no').removeClass("is-invalid").addClass('is-valid');
                    $('#pag_ibig_no').removeClass("is-invalid").addClass('is-valid');
                    $('#educational_attainment').removeClass("is-invalid").addClass('is-valid');
                    $('#course').removeClass("is-invalid").addClass('is-valid');
                    $('#emergency_name').removeClass("is-invalid").addClass('is-valid');
                    $('#emergency_contact_no').removeClass("is-invalid").addClass('is-valid');
                    $('#emergency_address').removeClass("is-invalid").addClass('is-valid');
                    $('#name_of_spouse').removeClass("is-invalid").addClass('is-valid');
                    $('#spouse_contact_no').removeClass("is-invalid").addClass('is-valid');
                    $('#no_of_children').removeClass("is-invalid").addClass('is-valid');
                    $('#spouse_address').removeClass("is-invalid").addClass('is-valid');
                    $('#position').removeClass("is-invalid").addClass('is-valid');
                    $('#employment_status').removeClass("is-invalid").addClass('is-valid');
                    $('#date_hired').removeClass("is-invalid").addClass('is-valid');
                    $('#date_resigned').removeClass("is-invalid").addClass('is-valid');

                    $("small_employee_id").html('');
                    $("small_firstname").html('');
                    $("small_middlename").html('');
                    $("small_lastname").html('');
                    $("small_gender").html('');
                    $("small_civil_status").html('');
                    $("small_date_of_birth").html('');
                    $("small_place_of_birth").html('');
                    $("small_postal_code").html('');
                    $("small_language").html('');
                    $("small_address_province").html('');
                    $("small_address_city").html('');
                    $("small_address_brgy").html('');
                    $("small_address_sub").html('');
                    $("small_contact_number").html('');
                    $("small_email_address").html('');
                    $("small_sss_no").html('');
                    $("small_tin_no").html('');
                    $("small_philhealth_no").html('');
                    $("small_pag_ibig_no").html('');
                    $("small_educational_attainment").html('');
                    $("small_course").html('');
                    $("small_emergency_name").html('');
                    $("small_emergency_contact_no").html('');
                    $("small_emergency_address").html('');
                    $("small_name_of_spouse").html('');
                    $("small_spouse_contact_no").html('');
                    $("small_no_of_children").html('');
                    $("small_spouse_address").html('');
                    $("small_position").html('');
                    $("small_employment_status").html('');
                    $("small_date_hired").html('');
                    $("small_date_resigned").html('');



                    me[0].reset();


                } else {

                    toastr.error("Errors Occured!");
                    $('#employee_id').removeClass("is-invalid").addClass('is-valid');
                    $('#firstname').removeClass("is-invalid").addClass('is-valid');
                    $('#middlename').removeClass("is-invalid").addClass('is-valid');
                    $('#lastname').removeClass("is-invalid").addClass('is-valid');
                    $('#gender').removeClass("is-invalid").addClass('is-valid');
                    $('#civil_status').removeClass("is-invalid").addClass('is-valid');
                    $('#date_of_birth').removeClass("is-invalid").addClass('is-valid');
                    $('#place_of_birth').removeClass("is-invalid").addClass('is-valid');
                    $('#postal_code').removeClass("is-invalid").addClass('is-valid');
                    $('#language').removeClass("is-invalid").addClass('is-valid');
                    $('#address_province').removeClass("is-invalid").addClass('is-valid');
                    $('#address_city').removeClass("is-invalid").addClass('is-valid');
                    $('#address_brgy').removeClass("is-invalid").addClass('is-valid');
                    $('#address_sub').removeClass("is-invalid").addClass('is-valid');
                    $('#contact_number').removeClass("is-invalid").addClass('is-valid');
                    $('#email_address').removeClass("is-invalid").addClass('is-valid');
                    $('#sss_no').removeClass("is-invalid").addClass('is-valid');
                    $('#tin_no').removeClass("is-invalid").addClass('is-valid');
                    $('#philhealth_no').removeClass("is-invalid").addClass('is-valid');
                    $('#pag_ibig_no').removeClass("is-invalid").addClass('is-valid');
                    $('#educational_attainment').removeClass("is-invalid").addClass('is-valid');
                    $('#course').removeClass("is-invalid").addClass('is-valid');
                    $('#emergency_name').removeClass("is-invalid").addClass('is-valid');
                    $('#emergency_contact_no').removeClass("is-invalid").addClass('is-valid');
                    $('#emergency_address').removeClass("is-invalid").addClass('is-valid');
                    $('#name_of_spouse').removeClass("is-invalid").addClass('is-valid');
                    $('#spouse_contact_no').removeClass("is-invalid").addClass('is-valid');
                    $('#no_of_children').removeClass("is-invalid").addClass('is-valid');
                    $('#spouse_address').removeClass("is-invalid").addClass('is-valid');
                    $('#position').removeClass("is-invalid").addClass('is-valid');
                    $('#employment_status').removeClass("is-invalid").addClass('is-valid');
                    $('#date_hired').removeClass("is-invalid").addClass('is-valid');
                    $('#date_resigned').removeClass("is-invalid").addClass('is-valid');

                    $("small_employee_id").html('');
                    $("small_firstname").html('');
                    $("small_middlename").html('');
                    $("small_lastname").html('');
                    $("small_gender").html('');
                    $("small_civil_status").html('');
                    $("small_date_of_birth").html('');
                    $("small_place_of_birth").html('');
                    $("small_postal_code").html('');
                    $("small_language").html('');
                    $("small_address_province").html('');
                    $("small_address_city").html('');
                    $("small_address_brgy").html('');
                    $("small_address_sub").html('');
                    $("small_contact_number").html('');
                    $("small_email_address").html('');
                    $("small_sss_no").html('');
                    $("small_tin_no").html('');
                    $("small_philhealth_no").html('');
                    $("small_pag_ibig_no").html('');
                    $("small_educational_attainment").html('');
                    $("small_course").html('');
                    $("small_emergency_name").html('');
                    $("small_emergency_contact_no").html('');
                    $("small_emergency_address").html('');
                    $("small_name_of_spouse").html('');
                    $("small_spouse_contact_no").html('');
                    $("small_no_of_children").html('');
                    $("small_spouse_address").html('');
                    $("small_position").html('');
                    $("small_employment_status").html('');
                    $("small_date_hired").html('');
                    $("small_date_resigned").html('');


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


</body>
</html>