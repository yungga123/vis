<!-- DataTables  & Plugins -->
<script src="<?= base_url('assets') ?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url('assets') ?>/dist/js/moment.js"></script>

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
                    $('#project').removeClass("is-invalid").addClass('is-valid');
                    $('#quotation_num').removeClass("is-invalid").addClass('is-valid');
                    $('#project_amount').removeClass("is-invalid").addClass('is-valid');
                    $('#remark_next_step').removeClass("is-invalid").addClass('is-valid');
                    $('#forecast_close_date').removeClass("is-invalid").addClass('is-valid');
                    $('#min_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#max_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

                    $('#small_quarter').html('');
                    $('#small_status').html('');
                    $('#small_customer_id').html('');
                    $('#small_project').html('');
                    $('#small_project_amount').html('');
                    $('#small_quotation_num').html('');
                    $('#small_remark_next_step').html('');
                    $('#small_forecast_close_date').html('');
                    $('#small_min_forecast_date').html('');
                    $('#small_max_forecast_date').html('');
                    $('#small_close_deal_date').html('');
                    $('#small_project_start_date').html('');
                    $('#small_project_finish_date').html('');


                    me[0].reset();


                } else {

                    toastr.error("Errors Occured!");
                    $('#quarter').removeClass("is-invalid").addClass('is-valid');
                    $('#status').removeClass("is-invalid").addClass('is-valid');
                    $('#customer_id').removeClass("is-invalid").addClass('is-valid');
                    $('#project').removeClass("is-invalid").addClass('is-valid');
                    $('#quotation_num').removeClass("is-invalid").addClass('is-valid');
                    $('#project_amount').removeClass("is-invalid").addClass('is-valid');
                    $('#remark_next_step').removeClass("is-invalid").addClass('is-valid');
                    $('#forecast_close_date').removeClass("is-invalid").addClass('is-valid');
                    $('#min_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#max_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

                    $('#small_quarter').html('');
                    $('#small_status').html('');
                    $('#small_customer_id').html('');
                    $('#small_project').html('');
                    $('#small_project_amount').html('');
                    $('#small_quotation_num').html('');
                    $('#small_remark_next_step').html('');
                    $('#small_forecast_close_date').html('');
                    $('#small_min_forecast_date').html('');
                    $('#small_max_forecast_date').html('');
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

    $('#post-editproject').submit(function(e) {
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
            "timeOut": "3000",
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
                    toastr.success("Successfully Edited! This window will close in 3 seconds.");

                    $('#quarter').removeClass("is-invalid").addClass('is-valid');
                    $('#status').removeClass("is-invalid").addClass('is-valid');
                    $('#customer_id').removeClass("is-invalid").addClass('is-valid');
                    $('#project').removeClass("is-invalid").addClass('is-valid');
                    $('#quotation_num').removeClass("is-invalid").addClass('is-valid');
                    $('#project_amount').removeClass("is-invalid").addClass('is-valid');
                    $('#remark_next_step').removeClass("is-invalid").addClass('is-valid');
                    $('#forecast_close_date').removeClass("is-invalid").addClass('is-valid');
                    $('#min_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#max_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

                    $('#small_quarter').html('');
                    $('#small_status').html('');
                    $('#small_customer_id').html('');
                    $('#small_project').html('');
                    $('#small_project_amount').html('');
                    $('#small_quotation_num').html('');
                    $('#small_remark_next_step').html('');
                    $('#small_forecast_close_date').html('');
                    $('#small_min_forecast_date').html('');
                    $('#small_max_forecast_date').html('');
                    $('#small_close_deal_date').html('');
                    $('#small_project_start_date').html('');
                    $('#small_project_finish_date').html('');

                    setTimeout(function() {
                        window.close()
                    }, 3000);

                } else {

                    toastr.error("Errors Occured!");
                    $('#quarter').removeClass("is-invalid").addClass('is-valid');
                    $('#status').removeClass("is-invalid").addClass('is-valid');
                    $('#customer_id').removeClass("is-invalid").addClass('is-valid');
                    $('#project').removeClass("is-invalid").addClass('is-valid');
                    $('#quotation_num').removeClass("is-invalid").addClass('is-valid');
                    $('#project_amount').removeClass("is-invalid").addClass('is-valid');
                    $('#remark_next_step').removeClass("is-invalid").addClass('is-valid');
                    $('#forecast_close_date').removeClass("is-invalid").addClass('is-valid');
                    $('#min_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#max_forecast_date').removeClass("is-invalid").addClass('is-valid');
                    $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

                    $('#small_quarter').html('');
                    $('#small_status').html('');
                    $('#small_customer_id').html('');
                    $('#small_project').html('');
                    $('#small_project_amount').html('');
                    $('#small_quotation_num').html('');
                    $('#small_remark_next_step').html('');
                    $('#small_forecast_close_date').html('');
                    $('#small_min_forecast_date').html('');
                    $('#small_max_forecast_date').html('');
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

    $('#post-booked-status').submit(function(e) {
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
            "timeOut": "3000",
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
                    toastr.success("Successfully BOOKED! You will return to project leads table.");

                    $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

                    $('#small_close_deal_date').html('');
                    $('#small_project_start_date').html('');
                    $('#small_project_finish_date').html('');

                    setTimeout(function() {
                        window.location.href = "<?= site_url('project-list') ?>";
                    }, 3000);

                } else {

                    toastr.error("Errors Occured!");
                    $('#close_deal_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_start_date').removeClass("is-invalid").addClass('is-valid');
                    $('#project_finish_date').removeClass("is-invalid").addClass('is-valid');

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


    $(function() {
        var project_list_table = $('#project_list_table').DataTable({
            "processing": true,
            "scrollX": true,
            "autoWidth": false,
            "columnDefs": [{
                "targets": '_all',
                "defaultContent": "<i>Not set</i>"
            }],
            "buttons": [{
                extend: "excel",
                exportOptions: {
                    columns: ":visible"
                },
                text: "Export to Excel"
            }, "colvis"],
            "serverSide": true,
            "ajax": '<?= ($uri->getSegment(1)=='manager-project-list') ? site_url('manager-project-table') : site_url('project-table') ?>',
            "initComplete": function(settings, json) {
                project_list_table.buttons().container().appendTo('#project_list_table_wrapper .col-md-6:eq(0)');
            }
        });



    });

    $(function() {
        var project_list_table = $('#project_list_table_booked').DataTable({
            "processing": true,
            "scrollX": true,
            "autoWidth": false,
            "columnDefs": [{
                "targets": '_all',
                "defaultContent": "<i>Not set</i>"
            }],
            "buttons": [{
                extend: "excel",
                exportOptions: {
                    columns: ":visible"
                },
                text: "Export to Excel"
            }, "colvis"],
            "serverSide": true,
            "ajax": '<?= ($uri->getSegment(1)=='manager-project-list-booked') ? site_url('manager-project-table-booked') : site_url('project-table-booked') ?>',
            "initComplete": function(settings, json) {
                project_list_table.buttons().container().appendTo('#project_list_table_wrapper .col-md-6:eq(0)');
            }
        });



    });

    <?php if ($uri->getSegment(1) == 'tasklead-editproject') { ?>
        $('#quarter').val("<?= $project_details['quarter'] ?>");
        $('#customer_id').val('<?= $project_details['customer_id'] ?>');
        $('#status').val("<?= $project_details['status'] ?>");
        $('#project').val("<?= $project_details['project'] ?>");
        $('#project_amount').val("<?= $project_details['project_amount'] ?>");
        $('#quotation_num').val("<?= $project_details['quotation_num'] ?>");
        $('#forecast_close_date').val("<?= $project_details['forecast_close_date'] ?>");
        $('#min_forecast_date').val("<?= $project_details['min_forecast_date'] ?>");
        $('#max_forecast_date').val("<?= $project_details['max_forecast_date'] ?>");
        $('#remark_next_step').val("<?= $project_details['remark_next_step'] ?>");
        $('#close_deal_date').val("<?= $project_details['close_deal_date'] ?>");
        $('#project_start_date').val("<?= $project_details['project_start_date'] ?>");
        $('#project_finish_date').val("<?= $project_details['project_finish_date'] ?>");

        $('#status option[value="10.00"]').attr('disabled',true);
        $('#status option[value="30.00"]').attr('disabled',true);
        $('#status option[value="50.00"]').attr('disabled',true);
        $('#status option[value="70.00"]').attr('disabled',true);
        $('#status option[value="90.00"]').attr('disabled',true);
        $('#status option[value="100.00"]').attr('disabled',true);

        $('#status option[value="<?= $project_details['status'] ?>"]').removeAttr('disabled');

        $('#min_forecast_date').attr('readonly',true);
        $('#max_forecast_date').attr('readonly',true);

        $("#forecast_close_date").change(function(){
            min_forecast = moment($('#forecast_close_date').val()).subtract(6,'days').format('YYYY-MM-DD');
            max_forecast = moment($('#forecast_close_date').val()).add(6,'days').format('YYYY-MM-DD');
            $('#min_forecast_date').val(min_forecast);
            $('#max_forecast_date').val(max_forecast);
        });

        $('#quarter option').attr('disabled',true);
        $('#quarter option[value="<?= $project_details['quarter'] ?>"]').attr('disabled',false);

        $('#close_deal_date').attr('readonly',true);
        $('#project_start_date').attr('readonly',true);
        $('#project_finish_date').attr('readonly',true);


    <?php } ?>

    

    <?php if ($uri->getSegment(1) == 'tasklead-addproject') { ?>
        $('#status option[value="30.00"]').attr('disabled',true);
        $('#status option[value="50.00"]').attr('disabled',true);
        $('#status option[value="70.00"]').attr('disabled',true);
        $('#status option[value="90.00"]').attr('disabled',true);
        $('#status option[value="100.00"]').attr('disabled',true);

        $('#quarter').val("<?= $date_quarter ?>");
        $('#quarter option').attr('disabled',true);
        $('#quarter option[value="<?= $date_quarter ?>"]').attr('disabled',false);

        $('#min_forecast_date').attr('readonly',true);
        $('#max_forecast_date').attr('readonly',true);

        $('#close_deal_date').attr('readonly',true);
        $('#project_start_date').attr('readonly',true);
        $('#project_finish_date').attr('readonly',true);

        $("#forecast_close_date").change(function(){
            min_forecast = moment($('#forecast_close_date').val()).subtract(6,'days').format('YYYY-MM-DD');
            max_forecast = moment($('#forecast_close_date').val()).add(6,'days').format('YYYY-MM-DD');
            $('#min_forecast_date').val(min_forecast);
            $('#max_forecast_date').val(max_forecast);
        });
    <?php } ?>

    

    $(document).on("click", ".delete-tasklead", function() {
        var id = $(this).data('id');
        $(".href-tasklead").prop("href", "<?= site_url('delete-tasklead') ?>/" + id);
    });

    $(document).on("click", ".update-tasklead", function() {
        var id = $(this).data('id');
        $(".href-identified").prop("href", "<?= site_url('update-tasklead') ?>/" + id + "/10.00");
        $(".href-qualified").prop("href", "<?= site_url('update-tasklead') ?>/" + id + "/30.00");
        $(".href-developed").prop("href", "<?= site_url('update-tasklead') ?>/" + id + "/50.00");
        $(".href-evaluation").prop("href", "<?= site_url('update-tasklead') ?>/" + id + "/70.00");
        $(".href-negotiation").prop("href", "<?= site_url('update-tasklead') ?>/" + id + "/90.00");
        $(".href-booked").prop("href", "<?= site_url('booked-status') ?>/" + id);
    });
</script>



</body>

</html>