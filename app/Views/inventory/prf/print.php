<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<div class="container-fluid">
	<div class="row">		
		<div class="col-3 text-center mx-auto">
			<img src="<?= base_url($company_logo) ?>" alt="Vinculum Logo" class="img-thumbnail mb-5" style="height: 80px;width: 200px">
		</div>
	</div>
	<div class="row">
		<div class="col-8 pr-0">
			<table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
					<tr>
						<td width="50%" style="font-weight: bold">PRF No:</td>
						<td width="50%"><?= $prf['id'] ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Project/Client Name:</td>
						<td width="50%"><?= $prf['client'] ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Quotation No:</td>
						<td width="50%"><?= $prf['quotation'] ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Work Type:</td>
						<td width="50%"><?= $prf['work_type'] ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Person In-Charge:</td>
						<td width="50%"><?= $prf['manager'] ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-4 pl-0">
			<table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
					<tr>
						<td width="50%" style="font-weight: bold">Job Order No.</td>
						<td width="50%"><?= $prf['job_order_id'] ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Date Requested</td>
						<td width="50%"><?= format_date($prf['date_requested']) ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Date Needed</td>
						<td width="50%"><?= format_date($prf['date_committed']) ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Date Issued</td>
						<td width="50%"><?= format_date($prf['jo_created_at']) ?></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Date Returned</td>
						<td width="50%"></td>
					</tr>
					<tr>
						<td width="50%" style="font-weight: bold">Time Returned</td>
						<td width="50%"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row pt-0">
		<div class="col-12 pt-0">
			<table class="table table-bordered table-sm" style="font-size: 15px">
				<thead>
					<tr>
						<th>Item #</th>
						<th>Category</th>
						<th>Description</th>
						<th>Quantity</th>
						<th>Available</th>
						<th>Consumed</th>
						<th>Returned</th>
						<th>Returned Date</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if (! empty($prf_items)): 
						foreach ($prf_items as $item): ?>
							<tr>
								<td><?= $item['inventory_id'] ?></td>
								<td><?= $item['category_name'] ?></td>
								<td><?= $item['item_description'] ?></td>
								<td><?= number_format($item['quantity_out'], 2) ?></td>
								<td><?= number_format($item['stocks'], 2) ?></td>
								<td><?= number_format($item['consumed'], 2) ?></td>
								<td><?= $item['returned_q'] ? number_format($item['returned_q'], 2) : $item['returned_q'] ?></td>
								<td><?= $item['returned_date_formatted'] ?></td>
							</tr>
					<?php
						endforeach;
					endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<table class="table table-bordered table-sm mb-0" style="font-size: 15px">
				<tbody>
					<tr>
						<td width="50%">
							<div class="text-bold">Requested by:</div>
							<div class="text-center"><?= $prf['created_by_name'] ?></div>
						</td>
						<td width="50%">
							<div class="text-bold">Received by: (Person In-Charge)</div>
							<div class="text-center"></div>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<div class="text-bold">Prepared by:</div>
							<div class="text-center"><?= $prf['accepted_by_name'] ?></div>
						</td>
						<td width="50%">
							<div class="text-bold">Checked By: (Project In-Charge)</div>
							<div class="text-center"></div>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<div class="text-bold">Returned by:</div>
							<div class="text-center"><?= $prf['filed_by_name'] ?></div>
						</td>
						<td width="50%">
							<div class="text-bold">Approved By: (Engineer In-charge)</div>
							<div class="text-center"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-12">
			<table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
					<tr>
						<td style="height: 80px; min-height: 80px;">
							<div class="text-bold">Remarks:</div>
							<div><?= $prf['remarks'] ?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?=$this->endSection();?>
