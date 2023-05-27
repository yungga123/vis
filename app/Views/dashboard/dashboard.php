<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
	<div class="container-fluid mb-3">
		<?php if (is_array($modules)): ?>
		<div class="row">
			<div class="col-4">
				<?php if (! empty($modules['hr_modules'])): ?>
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h5 class="card-title">Human Resource</h5>
					</div>
					<div class="card-body">
						<?php echo $modules['hr_modules']; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			
			<div class="col-4">
				<?php if (! empty($modules['sales_modules'])): ?>
				<div class="card card-success card-outline">
					<div class="card-header">
						<h5 class="card-title">Sales</h5>
					</div>
					<div class="card-body">
						<?php echo $modules['sales_modules']; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<div class="col-4">
				<?php if (! empty($modules['settings_modules'])): ?>
				<div class="card card-warning card-outline">
					<div class="card-header">
						<h5 class="card-title">Settings</h5>
					</div>
					<div class="card-body">
						<?php echo $modules['settings_modules']; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php else: echo $modules; endif; ?>
		</div>
	</div>
<?= $this->endSection(); ?>