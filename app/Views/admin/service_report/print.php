<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<?php 
$is_branch      = (! empty($service_report['client_branch_id']));
$client_id      = $service_report['client_id'];
$client_name    = $service_report['client_name'];
$contact_person = $service_report['client_contact_person'];
$contact_number = $service_report['client_contact_number'];
$address		= $service_report['client_address'];

if ($is_branch) {
    $client_id      = $service_report['client_branch_id'];
    $client_name    = $service_report['client_branch_name'];
    $contact_person = $service_report['client_branch_contact_person'];
    $contact_number = $service_report['client_branch_contact_number'];
    $address		= $service_report['client_branch_address'];
}

$branch_label 	= $is_branch ? '' : '(Branch)';
$report_number  = "{$form_code}-". format_date($service_report['created_at'], 'Ymd') ."-{$client_id}-{$service_report['id']}"
?>
<div class="container-fluid">
	<div class="row">		
		<div class="col-4">
			<img src="<?= $company_info['company_logo'] ?>" alt="Vinculum Logo" class="img-thumbnail mb-5" style="height: 80px;width: 200px">
		</div>
        <div class="8 text-center">
            <h5 class="mb-0"><strong><?= strtoupper($company_info['company_name']) ?></strong></h5>
            <p class="mb-0"><?= $company_info['company_address'] ?></p>
            <p>
                <span>Tel No.: <?= $company_info['company_telephone_number'] ?> </span>
                <span>TIN: <?= $company_info['company_tin'] ?></span>
            </p>
        </div>
	</div>
	<div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <h5><strong>Service Report</strong></h5>
                <div>
                    Report Number: 
                    <span class="text-danger">
                        <strong><?=  $report_number ?></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-7">
            <table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
					<tr>
						<td class="text-bold">Client Name</td>
						<td>
							<?= $service_report['client_name'] ?? '' ?>
						</td>
					</tr>
					<?php if (! empty($branch_name)): ?>
						<tr>
							<td class="text-bold">Client Branch Name</td>
							<td><?= $branch_name ?></td>
						</tr>
					<?php endif; ?>
					<tr>
						<td class="text-bold">Contact Person</td>
						<td><?= $contact_person ?></td>
					</tr>
					<tr>
						<td class="text-bold">
							<?= $is_branch ? 'Mobile ' : 'Contact ' ?> Number
						</td>
						<td><?= $contact_number ?></td>
					</tr>
					<?php if ($is_branch): ?>
						<tr>
							<td class="text-bold">Telephone Number</td>
							<td>
								<?= empty($service_report) || ! $is_branch ? 'N/A' : $service_report['client_telephone'] ?? 'N/A' ?>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td class="text-bold">Address</td>
						<td><?= $address ?></td>
					</tr>
				</tbody>
			</table>
        </div>
        <div class="col-1 p-0"></div>
        <div class="col-4">
            <table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
					<tr>
						<td class="text-bold">Arrival At</td>
						<td>
							<?= format_datetime($service_report['arrival_at']) ?>
						</td>
					</tr>
					<tr>
						<td class="text-bold">Server Type</td>
						<td>
							<?= $service_report['server_type'] ?? '' ?>
						</td>
					</tr>
					<tr>
						<td class="text-bold">Serial No.</td>
						<td><?= $service_report['serial_number'] ?? '' ?></td>
					</tr>
					<tr>
						<td class="text-bold">Site Area</td>
						<td><?= $service_report['area'] ?? '' ?></td>
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
                        <td class="text-bold">Error Description</td>
                        <td class="text-bold">Corrective Action</td>
                    </tr>
                    <tr>
                        <td><?= $service_report['error_description'] ?? 'N/A' ?></td>
                        <td><?= $service_report['corrective_action'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="text-bold">Remarks: </span>
                            <span><?= $service_report['remarks'] ?? 'N/A' ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <thead>
                    <tr>
                        <td colspan="3" class="text-bold">Unit Condition:</td>
                    </tr>
                    <tr>
                        <td class="text-bold">ITEMS</td>
                        <td class="text-bold">STATUS</td>
                        <td class="text-bold">If defective please specify:</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($units)): ?>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td><?= $unit['item'] ?? 'N/A' ?></td>
                                <td><?= $unit['status'] ?? 'N/A' ?></td>
                                <td><?= $unit['defective_description'] ?? 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-bold">
                                No data found...
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (! empty($items)): ?>
            <div class="col-12">
                <table class="table table-bordered table-sm" style="font-size: 15px">
                    <thead>
                        <tr>
                            <td class="text-bold">ITEM NO.</td>
                            <td class="text-bold">DESCRIPTION</td>
                            <td class="text-bold">QTY</td>
                            <td class="text-bold">UNIT PRICE</td>
                            <td class="text-bold">TOTAL PRICE</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $total_qty = 0;
                            $total_price = 0;
                            $grand_total = 0;

                            foreach ($items as $item): 
                                $item_qty           = $item['item_qty'] ?? 0;
                                $item_unit_price    = $item['item_unit_price'] ?? 0;
                                $item_total_price   = $item['item_total_price'] ?? 0;

                                $total_qty      += $item_qty;
                                $total_price    += $item_unit_price;
                                $grand_total    += $item_total_price;
                        ?>
                            <tr>
                                <td><?= $item['item_no'] ?? 'N/A' ?></td>
                                <td><?= $item['item_description'] ?? 'N/A' ?></td>
                                <td><?= number_format($item_qty, 2) ?></td>
                                <td><?= number_format($item_unit_price, 2) ?></td>
                                <td><?= number_format($item_total_price, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="text-bold">
                            <td colspan="2" class="text-right">GRAND TOTALS</td>
                            <td class="text-danger"><?= number_format($total_qty, 2) ?></td>
                            <td class="text-danger"><?= number_format($total_price, 2) ?></td>
                            <td class="text-danger"><?= number_format($grand_total, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div class="col-12">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr>
                        <td class="text-bold">Labor (Hours)</td>
                        <td class="text-bold">Travel Time (Hours)</td>
                        <td class="text-bold">Time Out</td>
                        <td class="text-bold">Service Type</td>
                    </tr>
                    <tr>
                        <td><?= number_format($service_report['labor'] ?? 0, 2) ?></td>
                        <td><?= number_format($service_report['travel_time'] ?? 0, 2) ?></td>
                        <td><?= format_time($service_report['time_out'] ?? '') ?></td>
                        <td><?= $service_report['service_type'] ?? 'N/A' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2 text-center">
        <div class="col-6">
			<div>
				<h6 class="text-bold ml-5 mt-5">
					__________________________________<br> 
					Customer's Signature over Printed Name
				</h6>
			</div>
        </div>
        <div class="col-6">
			<div>
				<h6 class="text-bold ml-5 mt-5">
					<span class="text-underline">
                        <?= $service_report['created_by'] ?>
                    </span><br> 
					Technician
				</h6>
			</div>
        </div>
    </div>
</div>
<?=$this->endSection();?>
