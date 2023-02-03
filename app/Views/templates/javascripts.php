<!-- jQuery -->
<script src="<?=base_url('assets')?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=base_url('assets')?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<?php if (isset($toastr) && $toastr): ?>
<!-- Toastr -->
<script src="<?=base_url('assets')?>/plugins/toastr/toastr.min.js"></script>
<?php endif;?>

<?php if (isset($sweetalert2) && $sweetalert2): ?>
<!-- Sweetalert2 -->
<script src="<?=base_url('assets')?>/plugins/sweetalert2/sweetalert2.min.js"></script>
<?php endif;?>

<!-- AdminLTE App -->
<script src="<?=base_url('assets')?>/dist/js/adminlte.js"></script>

<?php if (isset($with_dtTable) && $with_dtTable): ?>
<!-- DataTables  & Plugins -->
<script src="<?=base_url('assets')?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<?php endif;?>

<?php if (isset($with_jszip) && $with_jszip): ?>
<script src="<?=base_url('assets')?>/plugins/jszip/jszip.min.js"></script>
<?php endif;?>

<?php if (isset($with_pdfmake) && $with_pdfmake): ?>
<script src="<?=base_url('assets')?>/plugins/jszip/jszip.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/pdfmake/vfs_fonts.js"></script>
<?php endif;?>

<!-- Load custom js -->
<script src="<?=base_url('assets')?>/custom/js/functions.js"></script>
<?php
if (isset($custom_js) && !empty($custom_js)):
    $custom_js = is_string($custom_js) ? [$custom_js] : $custom_js;
    foreach ($custom_js as $val):
    ?>
					<script src="<?=base_url('assets/custom/js/' . $val)?>"></script>
				<?php endforeach;
endif;?>
<?=$this->renderSection('CustomScript');?>