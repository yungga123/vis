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

       $('#form-post-edit-account').submit(function(e) {
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
                       toastr.success("Successfully Updated! This window will close in 3 seconds");

                       $('#employee_id').removeClass("is-invalid").addClass('is-valid');
                       $('#username').removeClass("is-invalid").addClass('is-valid');
                       $('#password').removeClass("is-invalid").addClass('is-valid');
                       $('#access_level').removeClass("is-invalid").addClass('is-valid');

                       $("#small_employee_id").html('');
                       $("#small_username").html('');
                       $("#small_password").html('');
                       $("#small_access_level").html('');

                       setTimeout(function() {
                           window.close()
                       }, 3000);


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
            const route = '<?= site_url('ajax-account') ?>';
            var table = loadDataTable('accounts_table', route);
       });

       <?php if ($uri->getSegment(1) == 'edit-account') : ?>
            $("#employee_id").val("<?= $account_data['employee_id'] ?>");
            $("#username").val("<?= $account_data['username'] ?>");
            $("#password").val("<?= $account_data['password'] ?>");
            $("#access_level").val("<?= $account_data['access_level'] ?>");

            $("#employee_id").attr('disabled', true);
       <?php endif ?>

       $(document).on("click", ".delete-account", function() {
        var id = $(this).data('id');
        $(".href-account").prop("href", "<?= site_url('delete-account') ?>/" + id);
    });
   </script>

   </body>

   </html>