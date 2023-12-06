<!-- jQuery -->
<script src="<?=base_url('assets')?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=base_url('assets')?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

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
<script src="<?=base_url('assets')?>/plugins/datatables-colreorder/js/colReorder.bootstrap4.min.js"></script>
<?php endif;?>

<?php if (isset($with_jszip) && $with_jszip): ?>
<script src="<?=base_url('assets')?>/plugins/jszip/jszip.min.js"></script>
<?php endif;?>

<?php if (isset($with_pdfmake) && $with_pdfmake): ?>
<script src="<?=base_url('assets')?>/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=base_url('assets')?>/plugins/pdfmake/vfs_fonts.js"></script>
<?php endif;?>

<?php if (isset($exclude_toastr) && $exclude_toastr): else: ?>
<!-- Toastr -->
	<?php if (isset($toastr) && $toastr): ?>
		<script src="<?=base_url('assets')?>/plugins/toastr/toastr.min.js"></script>
	<?php endif;?>
<?php endif;?>

<?php if (isset($sweetalert2) && $sweetalert2): ?>
<!-- Sweetalert2 -->
<script src="<?=base_url('assets')?>/plugins/sweetalert2/sweetalert2.min.js"></script>
<?php endif;?>

<?php if (isset($select2) && $select2): ?>
<!-- Select2 -->
<script src="<?=base_url('assets')?>/plugins/select2/js/select2.full.min.js"></script>
<?php endif;?>

<?php if (isset($highcharts) && $highcharts): ?>
<!-- HighCharts -->
<script src="<?=base_url('assets')?>/plugins/highcharts/highcharts.js"></script>
<script src="<?=base_url('assets')?>/plugins/highcharts/modules/exporting.js"></script>
<script src="<?=base_url('assets')?>/plugins/highcharts/modules/export-data.js"></script>
<script src="<?=base_url('assets')?>/plugins/highcharts/modules/accessibility.js"></script>
<?php endif;?>

<?php if (isset($moment) && $moment): ?>
<!-- Date Range Picker -->
<script src="<?=base_url('assets')?>/plugins/moment/moment.min.js"></script>
<?php endif;?>

<?php if (isset($date_range_picker) && $date_range_picker): ?>
<!-- Date Range Picker -->
<script src="<?=base_url('assets')?>/plugins/daterangepicker/daterangepicker.js"></script>
<?php endif;?>

<?php if (isset($full_calendar) && $full_calendar): ?>
<!-- Date Range Picker -->
<script src="<?=base_url('assets')?>/plugins/fullcalendar/main.min.js"></script>
<?php endif;?>

<?php if (isset($dropzone) && $dropzone): ?>
<!-- Dropzone for uploading files -->
<script src="<?=base_url('assets')?>/plugins/dropzone/min/dropzone.min.js"></script>
<script type="text/javascript">
	// Disable auto discover
	Dropzone.autoDiscover = false;
</script>
<?php endif;?>

<?php if (isset($bootstrap_switch) && $bootstrap_switch): ?>
<!-- Bootstrap Switch -->
<script src="<?=base_url('assets')?>/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<?php endif;?>

<!-- AdminLTE App -->
<script src="<?=base_url('assets')?>/dist/js/adminlte.js"></script>

<!-- Load custom js -->
<script src="<?=base_url('assets')?>/custom/js/initialize.js"></script>
<script src="<?=base_url('assets')?>/custom/js/functions.js"></script>
<!-- Load routes options -->
<?php if (isset($routes) && !empty($routes)): ?>
	<script type="text/javascript">
      	var router = JSON.parse('<?= $routes ?>');
    </script>
<?php endif; ?>
<!-- Load php to js options - data that need to pass from php to js -->
<?php if (isset($php_to_js_options) && !empty($php_to_js_options)): ?>
	<script type="text/javascript">
      	// php to js options
      	var $pjOptions = JSON.parse('<?= $php_to_js_options ?>');
    </script>
<?php endif; ?>
<?php
if (isset($custom_js) && !empty($custom_js)):
    $custom_js = is_string($custom_js) ? [$custom_js] : $custom_js;
    foreach ($custom_js as $val): ?>
		<script src="<?=base_url('assets/custom/js/' . $val)?>"></script>
	<?php endforeach;
endif; ?>
<?=$this->renderSection('CustomScript');?>