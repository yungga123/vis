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
       $('#form-post-add-account').submit(function(e) {
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
                       $('#username').removeClass("is-invalid").addClass('is-valid');
                       $('#password').removeClass("is-invalid").addClass('is-valid');
                       $('#access_level').removeClass("is-invalid").addClass('is-valid');

                       $("#small_employee_id").html('');
                       $("#small_username").html('');
                       $("#small_password").html('');
                       $("#small_access_level").html('');

                       me[0].reset();


                   } else {

                       toastr.error("Errors Occured!");
                       $('#employee_id').removeClass("is-invalid").addClass('is-valid');
                       $('#username').removeClass("is-invalid").addClass('is-valid');
                       $('#password').removeClass("is-invalid").addClass('is-valid');
                       $('#access_level').removeClass("is-invalid").addClass('is-valid');

                       $("#small_employee_id").html('');
                       $("#small_username").html('');
                       $("#small_password").html('');
                       $("#small_access_level").html('');

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
           var table = $('#accounts_table').DataTable({
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

               }, {
                   extend: "csv",
                   exportOptions: {
                       columns: ':visible'
                   }
               }, {
                   extend: "excel",
                   exportOptions: {
                       columns: ":visible"
                   }
               }, {
                   extend: "pdf",
                   exportOptions: {
                       columns: ":visible"
                   }
               }, {
                   extend: "print",
                   exportOptions: {
                       columns: ":visible"
                   }
               }, "colvis"],
               "serverSide": true,
               "ajax": '<?= site_url('ajax-account') ?>',
               "initComplete": function(settings, json) {
                   table.buttons().container().appendTo('#employee_table_wrapper .col-md-6:eq(0)');
               }
           });

       });
   </script>

   </body>

   </html>