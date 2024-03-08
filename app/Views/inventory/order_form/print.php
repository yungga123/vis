<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<?php 
$branch_label 	= empty($order_form['client_branch_id']) 
	? '' : '(Branch)';
$branch_name 	= empty($order_form['client_branch_id']) 
	? '' : $order_form['client_branch_name'];
$contact_person = empty($order_form['client_branch_id']) 
	? $order_form['client_contact_person'] : $order_form['client_branch_contact_person'];
$contact_number = empty($order_form['client_branch_id']) 
	? $order_form['client_contact_number'] : $order_form['client_branch_contact_number'];
$address		= empty($order_form['client_branch_id']) 
	? $order_form['client_address'] : $order_form['client_branch_address'];
?>
<div class="container-fluid">
	<div class="row">		
		<div class="col-3 text-center mx-auto">
			<img src="<?= $company_logo ?>" alt="Vinculum Logo" class="img-thumbnail mb-5" style="height: 80px;width: 200px">
		</div>
	</div>
	<div class="row">
		<div class="col-12 pr-0">
			<table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
					<tr>
						<td width="20%" style="font-weight: bold">Order Form (OF) No:</td>
						<td width="80%" class="text-danger text-bold">
							<?=  $order_form['id'] ? 'OF'. format_date($order_form['created_at'], 'Ymd') . '-' .  $order_form['id'] : '' ?>
						</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold">Purchased At:</td>
						<td width="80%" class="text-bold">
							<?= format_datetime($order_form['purchase_at']) ?>
						</td>
					</tr>
					<tr>
						<td width="20%" style="font-weight: bold">Client Name</td>
						<td width="80%">
							<?= $order_form['client_name'] ?? '' ?>
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
								<?= empty($order_form) || ! empty($branch_name) ? 'N/A' : $order_form['client_telephone'] ?? 'N/A' ?>
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
	<div class="row pt-0">
		<div class="col-12 pt-0">
			<table class="table table-bordered table-sm" style="font-size: 15px">
				<thead>
					<tr>
						<td class="text-bold">Item #</td>
						<td class="text-bold">Category</td>
						<td class="text-bold">Model</td>
						<td class="text-bold">Description</td>
						<td class="text-bold">Size</td>
						<td class="text-bold">Unit</td>
						<td class="text-bold">Stocks</td>
						<td class="text-bold">Price</td>
						<td class="text-bold">Quantity</td>
						<td class="text-bold">Discount</td>
						<td class="text-bold">Total Price</td>
					</tr>
				</thead>
				<tbody>
					<?php 
					$grand_item_stocks	= 0;
					$grand_item_price 	= 0;
					$grand_quantity 	= 0;
					$grand_discount 	= 0;
					$grand_total_price 	= 0;

					if (! empty($items)):
						foreach ($items as $item):
							$item_stocks	= $item['stocks'] ?? 0;
							$item_price		= $item['item_price'] ?? 0;
							$quantity		= $item['quantity'] ?? 0;
							$discount		= $item['discount'] ?? 0;
							$total_price 	= $item['total_price'] ?? 0;

							if (empty($total_price)) {
								$total_price = $item_price * $quantity;
								$total_price = $total_price - $discount;
							}

							$grand_item_stocks 	+= $item_stocks;
							$grand_item_price 	+= $item_price;
							$grand_quantity 	+= $quantity;
							$grand_discount 	+= $discount;
							$grand_total_price 	+= $total_price;
					?>
							<tr>
								<td><?= $item['inventory_id'] ?? 'N/A' ?></td>
								<td><?= $item['category_name'] ?? 'N/A' ?></td>
								<td><?= $item['item_model'] ?? 'N/A' ?></td>
								<td><?= $item['item_description'] ?? 'N/A' ?></td>
								<td><?= $item['size'] ?? 'N/A' ?></td>
								<td><?= $item['unit'] ?? 'N/A' ?></td>
								<td><?= number_format($item_stocks, 2) ?></td>
								<td><?= number_format($item_price, 2) ?></td>
								<td><?= number_format($quantity, 2) ?></td>
								<td><?= number_format($discount, 2) ?></td>
								<td><?= number_format($total_price, 2) ?></td>
							</tr>
					<?php
						endforeach;
					endif; ?>
				</tbody>
				<tfoot>
					<tr class="text-bold">
						<td colspan="6" class="text-right">Grand Totals</td>
						<td class="text-danger"><?= number_format($grand_item_stocks, 2) ?></td>
						<td class="text-danger"><?= number_format($grand_item_price, 2) ?></td>
						<td class="text-danger"><?= number_format($grand_quantity, 2) ?></td>
						<td class="text-danger"><?= number_format($grand_discount, 2) ?></td>
						<td class="text-danger"><?= number_format($grand_total_price, 2) ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<table class="table table-bordered table-sm mb-0" style="font-size: 15px">
				<tbody>
					<tr>
						<td width="50%">
							<div class="text-bold">Created by:</div>
							<div class="text-center"><?=  $order_form['created_by'] ?? '' ?></div>
						</td>
						<td width="50%">
							<div class="text-bold">Received by: (Person In-Charge)</div>
							<div class="text-center"><?=  $order_form['received_by'] ?? '' ?></div>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<div class="text-bold">Prepared by:</div>
							<div class="text-center"><?=  $order_form['accepted_by'] ?? '' ?></div>
						</td>
						<td width="50%">
							<div class="text-bold">Checked By: (Project In-Charge)</div>
							<div class="text-center"></div>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<div class="text-bold">Returned by:</div>
							<div class="text-center" <?=  $order_form['filed_by'] ? '' : 'style="padding: 0.7rem 0;"' ?>>
								<?=  $order_form['filed_by'] ?? '' ?>
							</div>
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
							<div><?=  $order_form['remarks'] ?? '' ?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?=$this->endSection();?>
