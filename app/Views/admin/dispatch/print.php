<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<?php 
$branch_label 	= empty($client['client_branch_id']) 
	? '' : '(Branch)';
$branch_name 	= empty($client['client_branch_id']) 
	? '' : $client['client_branch_id'].' --- '.$client['client_branch_name'];
$contact_person = empty($client['client_branch_id']) 
	? $client['client_contact_person'] : $client['client_branch_contact_person'];
$contact_number = empty($client['client_branch_id']) 
	? $client['client_contact_number'] : $client['client_branch_contact_number'];
$address		= empty($client['client_branch_id']) 
	? $client['client_address'] : $client['client_branch_address'];
?>
<div class="container-fluid">
<div class="row">
		<div class="col-12">
			<div class="row">
				<div class="col-3">
					<img src="<?= $company_logo ?>" alt="Vinculum Logo" class="img-thumbnail mb-4" style="height: 80px; width: 200px">
				</div>
				<div class="col-6">
					<p class="text-center mx-auto" style="font-size: 23px; font-weight: bold;">DISPATCH FORM</p>
					<p class="text-center mx-auto text-danger text-bold">ID No. <?= $dispatch['id'] ?></p>
				</div>
			</div>
			<div class="row">
				<div class="col-12 mb-4">
					<table class="table table-bordered table-sm" style="font-size: 15px">
						<tbody>
							<tr>
								<td width="20%" style="font-weight: bold">Client Name</td>
								<td width="80%">
									<?= empty($client) ? '' : $client['client_id'].' --- '.$client['client_name'] ?>
								</td>
							</tr>
							<?php if (! empty($branch_name)): ?>
								<tr>
									<td width="20%" style="font-weight: bold">Client Branch Name</td>
									<td width="80%"><?= $branch_name ?></td>
								</tr>
							<?php endif; ?>
							<tr>
								<td width="20%" style="font-weight: bold">Contact Person <?= $branch_label ?></td>
								<td width="80%"><?= $contact_person ?></td>
							</tr>
							<tr>
								<td width="20%" style="font-weight: bold">
									<?= empty($branch_name) ? 'Mobile ' : 'Contact ' ?> Number <?= $branch_label ?>
								</td>
								<td width="80%"><?= $contact_number ?></td>
							</tr>
							<?php if (empty($branch_name)): ?>
								<tr>
									<td width="20%" style="font-weight: bold">Telephone Number <?= $branch_label ?></td>
									<td width="80%">
										<?= empty($client) || ! empty($branch_name) ? 'N/A' : $client['client_telephone'] ?? 'N/A' ?>
									</td>
								</tr>
							<?php endif; ?>
							<tr>
								<td width="20%" style="font-weight: bold">Address <?= $branch_label ?></td>
								<td width="80%"><?= $address ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-8">
					<table class="table table-bordered table-sm" style="font-size: 15px">
						<tbody>
							<tr>
								<td width="100%" class="text-center" colspan="2" style="font-weight: bold">Personnel/s</td>
							</tr>
                            <?php 
								if (! empty($dispatch['technicians'])):
									$technicians = explode(',', $dispatch['technicians']);

									foreach ($technicians as $tech):
                            ?>
									<tr>
										<td width="50%"><?= $tech ?></td>
									</tr>
                            	<?php endforeach; ?>
                            <?php else: ?>
								<tr>
									<td width="50%"></td>
								</tr>
                            <?php endif; ?>
							<tr>
								<td width="100%" class="text-center" colspan="2" style="font-weight: bold;">Service Report No. : <?= $dispatch['sr_number'] ?? '' ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-4">
					<table class="table table-bordered table-sm mb-4" style="font-size: 15px">
						<tbody>
							<tr>
								<td class="text-bold" width="30%">Work Permit</td>
								<td width="70%"><?= $dispatch['with_permit'] ?? '' ?></td>
							</tr>
							<tr>
								<td width="30%" style="font-weight: bold;">Dispatch Date</td>
								<td width="70%"><?= $dispatch['dispatch_date'] ? format_date($dispatch['dispatch_date'], 'l - F j, Y') : '' ?></td>
							</tr>
							<tr>
								<td width="30%" style="font-weight: bold;">Time In</td>
								<td width="70%"><?= $dispatch['time_in'] ? format_time($dispatch['time_in'], '', true) : '' ?></td>
							</tr>
							<tr>
								<td width="30%" style="font-weight: bold;">Time Out</td>
								<td width="70%"><?= $dispatch['time_out'] ? format_time($dispatch['time_out'], '', true) : '' ?></td>
							</tr>
							<tr>
								<td width="30%" style="font-weight: bold;">Dispatch Out</td>
								<td width="70%"><?= $dispatch['dispatch_out'] ? format_time($dispatch['dispatch_out'], '', true) : '' ?></td>
							</tr>
							<tr>
								<td width="30%" style="font-weight: bold;">Service Type</td>
								<td width="70%">
									<?= $dispatch['service_type'] ? get_dispatch_services($dispatch['service_type']) : '' ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<table class="table table-bordered table-sm" style="font-size: 15px">
						<tbody>
							<tr>
								<td width="15%" style="font-weight: bold">Description</td>
								<td width="85%"><?= $dispatch['comments'] ?? '' ?></td>
							</tr>
							<tr>
								<td width="15%" style="font-weight: bold">Remarks</td>
								<td width="85%"><?= $dispatch['remarks'] ?? '' ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-4">
					<p style="font-weight: bold">Customer Acceptance:</p>
					<p>________________________________ <br> 
					Customer Signature over Printed Name</p>
				</div>
				<div class="col-4 text-center">
					<p style="font-weight: bold">Dispatched By:</p>
					<p>
                        <u><?= $dispatch['dispatched_by'] ?></u><br> 
					    <?= $dispatch['dispatched_by_position'] ? ucwords($dispatch['dispatched_by_position']) : '' ?>
                    </p>
				</div>
				<div class="col-2">
				</div>
				<div class="col-2 text-center">
					<p style="font-weight: bold">Checked By:</p>
					<p>
                        <u><?= $dispatch['checked_by_name'] ?></u><br> 
					    <?= $dispatch['checked_by_position'] ? ucwords($dispatch['checked_by_position']) : '' ?>
                    </p>
				</div>

			</div>
		</div>
	</div>
</div>
<?=$this->endSection();?>
