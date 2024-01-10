<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
	<div class="container-fluid mb-3">
		<div class="row">
			<div class="col-sm-12 col-md-8">
				<div class="card card-outline card-info">
					<div class="card-header">
						<h3 class="card-title">Schedules Today</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<?= $schedules ?>
						</div>
						<!-- <div class="row">
							<div class="col-md-3 col-sm-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Legend</h3>
									</div>
									<div class="card-body">
										<?= $type_legend ?>
									</div>
								</div>
							</div>
							<div class="col-md-9 col-sm-12">                    
								<div class="card">
									<div class="card-body p-0">
										<?= $schedules ?>
									</div>
								</div>
							</div>
						</div> -->
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-4">
				<?= $this->include('payroll/timesheet/attendance'); ?>
			</div>
			<div class="col-12">
				<div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">Your Modules</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">						
						<div class="row">
							<?php
								echo $modules;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?= $this->endSection(); ?>