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
    $('#form-addcustomer').submit(function(e) {
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


    $(function() {
        var customer_table = $('#myTable').DataTable({
            "processing": true,
            "responsive": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "serverSide": true,
            "ajax": '<?= site_url('customer-list') ?>',
            "initComplete": function(settings,json){
                customer_table.buttons().container().appendTo('#myTable_wrapper .col-md-6:eq(0)');
            }
        });

    });
</script>


</body>

</html>