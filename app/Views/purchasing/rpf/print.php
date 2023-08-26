<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<div class="container-fluid">
	<div class="row">		
		<div class="col-3 text-center mx-auto">
			<img src="<?= base_url('assets/images/vinculumnew.jpg') ?>" alt="Vinculum Logo" class="img-thumbnail mb-5" style="height: 80px;width: 200px">
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<table class="table table-bordered table-sm mb-0" style="font-size: 15px">
				<tbody>
					<tr>
						<td width="25%" style="font-weight: bold">RF No:</td>
						<td width="40%"><?= $rpf['id'] ?></td>
						<td width="35%"><strong>PRF No:</strong> </td>
					</tr>
					<tr>
						<td width="25%" style="font-weight: bold">Requestor:</td>
						<td width="40%"><?= $rpf['created_by_name'] ?></td>
						<td width="35%"><strong>Created At: </strong> <?= $rpf['created_at_formatted'] ?></td>
					</tr>
					<tr>
						<td width="25%" style="font-weight: bold">Date Needed: </td>
						<td width="40%"><?= $rpf['date_needed_formatted'] ?></td>
						<td width="35%"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row pt-0">
		<div class="col-12 pt-0">
			<table class="table table-bordered table-sm mb-0" style="font-size: 15px">
				<thead>
					<tr>
						<th>No.</th>
						<th>Description</th>
						<th>Model</th>
						<th>Qty</th>
						<th>Received Qty</th>
						<th>Unit</th>
						<th>Cost</th>
						<th>Total Cost</th>
						<th>Supplier</th>
						<th>Stocks</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if (! empty($rpf_items)): 
						foreach ($rpf_items as $item): ?>
							<tr>
								<td><?= $item['inventory_id'] ?></td>
								<td><?= $item['item_description'] ?></td>
								<td><?= $item['item_model'] ?></td>
								<td><?= $item['quantity_in'] ?></td>
								<td><?= $item['received_q'] ?></td>
								<td><?= $item['unit'] ?></td>
								<td><?= $item['item_sdp'] ?></td>
								<td><?= floatval($item['item_sdp'] * $item['quantity_in']) ?></td>
								<td><?= $item['supplier_name'] ?></td>
								<td><?= $item['stocks'] ?></td>
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
							<div class="text-bold">Accepted by:</div>
							<div class="text-center"><?= $rpf['accepted_by_name'] ?></div>
						</td>
						<td width="50%">
							<div class="text-bold">Received by:</div>
							<div class="text-center"><?= $rpf['received_by_name'] ?></div>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<div class="text-bold">Reviewed by:</div>
							<div class="text-center"><?= $rpf['reviewed_by_name'] ?></div>
						</td>
						<td width="50%">
							<div class="text-bold"></div>
							<div class="text-center"></div>
						</td>
					</tr>
				</tbody>
			</table>
			<div><i>REMINDER: ITEMS REQUEST CUT-OFF IS UNTIL 2:00PM ONLY!</i></div>
		</div>
	</div>
</div>
<?=$this->endSection();?>
