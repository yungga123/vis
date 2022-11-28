    <script>
        $('#post-addproject').submit(function(e) {
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

                        $('#quarter').removeClass("is-invalid").addClass('is-valid');
                        $('#status').removeClass("is-invalid").addClass('is-valid');
                        $('#customer_id').removeClass("is-invalid").addClass('is-valid');
                        $('#project_amount').removeClass("is-invalid").addClass('is-valid');
                        $('#remark_next_step').removeClass("is-invalid").addClass('is-valid');
                        $('#forecast_close_date').removeClass("is-invalid").addClass('is-valid');
                        $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                        $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                        $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

                        $('#small_quarter').html('');
                        $('#small_status').html('');
                        $('#small_customer_id').html('');
                        $('#small_project_amount').html('');
                        $('#small_remark_next_step').html('');
                        $('#small_forecast_close_date').html('');
                        $('#small_close_deal_date').html('');
                        $('#small_project_start_date').html('');
                        $('#small_project_finish_date').html('');


                        me[0].reset();


                    } else {

                        toastr.error("Errors Occured!");
                        $('#quarter').removeClass("is-invalid").addClass('is-valid');
                        $('#status').removeClass("is-invalid").addClass('is-valid');
                        $('#customer_id').removeClass("is-invalid").addClass('is-valid');
                        $('#project_amount').removeClass("is-invalid").addClass('is-valid');
                        $('#remark_next_step').removeClass("is-invalid").addClass('is-valid');
                        $('#forecast_close_date').removeClass("is-invalid").addClass('is-valid');
                        $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                        $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                        $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

                        $('#small_quarter').html('');
                        $('#small_status').html('');
                        $('#small_customer_id').html('');
                        $('#small_project_amount').html('');
                        $('#small_remark_next_step').html('');
                        $('#small_forecast_close_date').html('');
                        $('#small_close_deal_date').html('');
                        $('#small_project_start_date').html('');
                        $('#small_project_finish_date').html('');

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