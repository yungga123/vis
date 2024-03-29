<?php if (isset($with_dtTable) && $with_dtTable): ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?=base_url('assets')?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url('assets')?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url('assets')?>/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<?php endif;?>

<?php if (isset($exclude_toastr) && $exclude_toastr): else: ?>
<!-- Toastr -->
<link rel="stylesheet" href="<?=base_url('assets')?>/plugins/toastr/toastr.min.css">
<?php endif;?>

<?php if (isset($sweetalert2) && $sweetalert2): ?>
<!-- Sweetalert2 -->
<link rel="stylesheet" href="<?=base_url('assets')?>/plugins/sweetalert2/sweetalert2.min.css">
<?php endif;?>

<?php if (isset($select2) && $select2): ?>
<!-- Select2 -->
<link rel="stylesheet" href="<?=base_url('assets')?>/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?=base_url('assets')?>/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?php endif;?>

<!-- Theme style -->
<link rel="stylesheet" href="<?=base_url('assets')?>/dist/css/adminlte.min.css">

<!-- Load custom css -->
<link rel="stylesheet" href="<?=base_url('assets')?>/custom/css/style.css">
<?php
if (isset($custom_css) && !empty($custom_css)):
    $custom_css = is_string($custom_css) ? [$custom_css] : $custom_css;
    foreach ($custom_css as $val):
    ?>
		    <link rel="stylesheet" href="<?=base_url('assets/custom/css/' . $val);?>">
		<?php endforeach;
endif;?>
<?=$this->renderSection('CustomCSS');?>