<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
	<div class="container-fluid mb-3">
		<?php if (is_array($modules)): ?>
			<div class="row">
				<?php
					echo $modules['hr_modules'];
					echo $modules['clients_modules'];
					echo $modules['sales_modules'];
					echo $modules['inventory_modules'];
					echo $modules['settings_modules'];
				?>
			</div>
		<?php else: echo $modules; endif; ?>
	</div>
<?= $this->endSection(); ?>