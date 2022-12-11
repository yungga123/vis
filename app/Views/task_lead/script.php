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
                extend: 'copy',
                exportOptions: {
                    columns: ':visible'
                }
                
            },{
                extend: "csv",
                exportOptions: {
                    columns: ':visible'
                }
            },{
                extend: "excel",
                exportOptions: {
                    columns: ":visible"
                }
            },{
                extend: "pdf",
                exportOptions: {
                    columns: ":visible"
                }
            },{
                extend: "print",
                exportOptions: {
                    columns: ":visible"
                }
            }, "colvis"
        ],
            "serverSide": true,
            "ajax": '<?= site_url('project-table') ?>',
            "initComplete": function(settings, json) {
                project_list_table.buttons().container().appendTo('#project_list_table_wrapper .col-md-6:eq(0)');
            }
        });


    });
</script>



</body>

</html>