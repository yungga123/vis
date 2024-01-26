<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<?php
$form_code 			= isset($general_info['purchase_order_form_code']) && !empty($general_info['purchase_order_form_code'])
	? $general_info['purchase_order_form_code'] : COMPANY_PO_FORM_CODE;
$total_amount		= 0;
$net_of_vat_amount	= 0;
$vat_amount			= 0;
$with_vat 			= $purchase_order['with_vat'] != '0';
?>
<div class="container-fluid">
	<div class="row">		
		<div class="col-6">
			<img src="<?= $general_info['company_logo'] ?? '' ?>" alt="Vinculum Logo" class="img-thumbnail mb-4" style="height: 120px; width: 380px">
		</div>
        <div class="col-3"></div>
        <div class="col-3">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr class="text-bold text-center">
                        <td>
							PURCHASE ORDER NO.
                            <br>
							<span class="text-danger">
								<?php 
									$code_format 	= "{$form_code}-{$purchase_order['supplier_id']}-". date('y') .'-'. date('md') .'-'. $purchase_order['id'];
									echo $code_format;
								?>
							</span>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>
							<span class="text-bold">PURCHASE ORDER DATE</span>
                            <br>
                            <?php echo date('F d, Y'); ?>
                        </td>
                    </tr>
                    <br>
                </tbody>
            </table>
        </div>
	</div>
    <div class="row">
        <div class="col-sm-5">			
			<label>SUPPLIER:</label>
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr class="text-center text-uppercase">
                        <th><?php echo $supplier['supplier_name'] ?? '' ?></th>
                    </tr>
                    <tr class="text-center text-uppercase">
                        <td><?php echo $supplier['address'] ?? '' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-2"></div>
        <div class="col-5">			
			<label>SHIP TO: </label>
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr class="text-center text-uppercase text-bold">
                        <td><?= $general_info['company_name'] ?></td>
                    </tr>
                    <tr class="text-center">
                        <td><?= $general_info['company_address'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <label>ATTENTION TO:</label> 
			<span id="attention_to_text"><?= $purchase_order['attention_to'] ?? '' ?></span>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
                    <tr>
                        <td class="text-bold text-center" colspan="3">REQUESTOR</td>
                        <td class="text-bold text-center" colspan="4" rowspan="2"></td>
                        <td class="text-bold text-center" colspan="4">REQUISITION NUMBER</td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="3">
							<span><?= $rpf['requested_by'] ?? '' ?></span>
						</td>
                        <td class="text-bold text-center text-danger" colspan="4">
							<?= 'RF No. '. $purchase_order['rpf_id'] ?? '' ?>
						</td>
                    </tr>
					<tr></tr>
                    <tr>
                        <td class="text-bold text-center">ITEM NO.</td>
                        <td class="text-bold text-center">MODEL</td>
                        <td class="text-bold text-center">BRAND</td>
                        <td class="text-bold text-center">DESCRIPTION</td>
                        <td class="text-bold text-center">SIZE</td>
                        <td class="text-bold text-center">UNIT</td>
                        <td class="text-bold text-center">QTY</td>
                        <td class="text-bold text-center">UNIT PRICE</td>
                        <td class="text-bold text-center">DISCOUNT</td>
                        <td class="text-bold text-center">TOTAL PRICE</td>
                        <td class="text-bold text-center">DELIVERY DATE</td>
                    </tr>
					<?php 
					$sub_total = 0;

					if (! empty($items)) {
						$count = 1;

						foreach ($items as $val) {
							$discount 		= $val['discount'] ?? 0;
							$total_price 	= ($val['quantity_in'] ?? 0) * $val['item_sdp'];
							$total_price 	-= $discount;
					?>
							<tr>
								<td class="text-center"><?= $count; ?></td>
								<td class="text-center"><?= $val['item_model'] ?? 'N/A' ?></td>
								<td class="text-center"><?= $val['brand'] ?? 'N/A' ?></td>
								<td class="text-center"><?= $val['item_description'] ?? 'N/A' ?></td>
								<td class="text-center"><?= $val['size'] ?? 'N/A' ?></td>
								<td class="text-center"><?= ($val['unit'] ?? 'N/A') ?></td>
								<td class="text-center"><?= $val['quantity_in'] ?? 'N/A' ?></td>
								<td class="text-right"><?= $val['item_sdp'] ?? 'N/A' ?></td>
								<td class="text-right"><?= number_format($discount, 2) ?></td>
								<td class="text-right"><?= number_format($total_price, 2) ?></td>
								<td class="text-center"><?= $rpf['date_needed'] ?? 'N/A' ?></td>
							</tr>
					<?php 
							$count 		+= 1;
							$sub_total 	+= $total_price;
						}

						// Computation of initial total amount, net of vat and vat amount
						$total_amount		= $sub_total;
						$net_of_vat_amount	= $sub_total / 1.12;
						$vat_amount			= $net_of_vat_amount * .12; // 12% vat
					}
					?>
					<tr>
                        <td class="text-bold text-center text-danger" colspan="11">***Nothing Follows***</td>
                    </tr>
                    <tr>
                        <td class="text-bold text-center" colspan="7" rowspan="2"></td>
                        <td class="text-right" colspan="2">
							<div>SUB TOTAL AMOUNT</div>
							<div>NET OF VAT</div>
							<div>PLUS 12% VAT</div>
                        </td>
                        <td class="text-right">
							<div>PHP <?= number_format($sub_total, 2) ?></div>
							<div id="net_of_vat_amount">
								<?= number_format($with_vat ? $net_of_vat_amount : 0, 2) ?>
							</div>
							<div id="vat_amount">
								<?= number_format($with_vat ? $vat_amount : 0, 2) ?>
							</div>
                        </td>
						<td rowspan="2"></td>
                    </tr>
                    <tr>
                        <td class="text-bold text-right" colspan="2">
							TOTAL AMOUNT<span id="vat_text"></span>
						</td>
                        <td class="text-bold text-right">
							PHP <span id="total_amount"><?= number_format($total_amount, 2) ?></span>
						</td>
                    </tr>
				</tbody>
            </table>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <h6>
                <strong class="text-underline">Terms and Conditions: </strong>
                <span class="text-italic">
					This Purchase Order (PO) becomes the exclusive agreement between
					<strong><?= $general_info['company_name'] ?></strong>
					and Supplier/s for the good subject to the standard Terms and Conditions contained herein.
				</span>
            </h6>
        </div>
        <div class="col-12">
            <h6>
				<ol>
					<li>Supplier must provide the Delivery Receipt (DR), Warranty Slip (WS), or Sales Invoice (SI).</li>
					<li>All Delivered or purchased items will be checked based on the stated information above.</li>
					<li>Testing and commisioning must be conducted by unit supplier unless otherwise the defective items must replaced.</li>
					<li>Supplier must deliver all purchased items based on the committed delivery schedule</li>
					<li><strong><?= $general_info['company_name'] ?></strong> will penalize supplier without prior notice in terms of problem deliver such as late delivery, move delivery date, etc.</li>
					<li>Incase of purchased item(s) / unit(s) is/are under warranty and subject for repair, Supplier must provide Service Unit upon acceptance of the returned item(s) / unit(s).</li>
					<li>
						Mode of Payment: 
						<span class="text-bold text-uppercase text-underline">
							<?php 
								if (isset($supplier['payment_mode'])) {
									echo $supplier['payment_mode'] === 'Others' 
										? $supplier['others_payment_mode'] : ($supplier['payment_mode'] ?? 'N/A');
								}
							?>
						</span>
					</li>
					<li>
						Terms of Payment: 
						<span class="text-bold text-uppercase text-underline">
							<?= $supplier['payment_terms'] ? get_payment_terms($supplier['payment_terms']) : '' ?>
						</span>
					</li>
				</ol>
            </h6>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-7">
			<div class="mb-5">
				<h6>PREPARED BY:</h6>
				<h6 class="text-bold ml-5 mt-4">
					<?= $purchase_order['prepared_by_name'] ? strtoupper($purchase_order['prepared_by_name']) : '' ?>
				</h6>
			</div>
			<div>
				<h6>CHECKED BY:</h6>
				<h6 class="text-bold ml-5 mt-4">
					<?= $purchase_order['prepared_by_name'] ? strtoupper($purchase_order['prepared_by_name']) : '' ?>
				</h6>
			</div>
        </div>
        <div class="col-5">
			<div class="mb-5">
				<h6>APPROVED BY:</h6>
				<h6 class="text-bold ml-5 mt-4">
					<?= $purchase_order['approved_by_name'] ? strtoupper($purchase_order['approved_by_name']) : '' ?>
				</h6>
			</div>
			<div>
				<h6>ACKNOWLEDGED/RECEIVED BY:</h6>
				<h6 class="text-bold ml-5 mt-5">
					__________________________________<br> 
					Customer Signature over Printed Name
				</h6>
			</div>
        </div>
		<div class="col-12">
			<div class="mt-4">
				<p class="text-center text-italic text-bold">
					*It is computer generated valid documents and therefore does not require any signature in any commercial transaction*
				</p>
			</div>
		</div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="print_po_modal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate PO</h5>
            </div>
			<form id="print_po_form" class="with-label-indicator" action="<?= url_to('purchase_order.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-9">
							<div class="form-group">
								<label class="required" for="attention_to">Attention to:</label>
								<input type="text" class="form-control" id="attention_to" name="attention_to" placeholder="Ex: Mr. / Ms. / Mrs. Antonette" value="<?= $purchase_order['attention_to'] ?? '' ?>" required>
								<small id="alert_attention_to" class="text-danger"></small>
							</div>
						</div>
						<div class="col-3">
							<label for="form_with_vat">With Vat?</label>
							<div class="form-check">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" id="form_with_vat" name="with_vat" value="yes" <?= $with_vat ? 'checked' : '' ?>>
									Vat 12%
								</label>
							</div>
							<input type="hidden" name="po_id" value="<?= $purchase_order['id'] ?>">
							<input type="hidden" name="net_of_vat_amount" value="<?= number_format($net_of_vat_amount, 2) ?>">
							<input type="hidden" name="vat_amount" value="<?= number_format($vat_amount, 2) ?>">
							<input type="hidden" name="sub_total" value="<?= number_format($sub_total, 2) ?>">
							<input type="hidden" name="total_amount" value="<?= number_format($total_amount, 2) ?>">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success text-bold btn-proceed"><i class="fas fa-check"></i> PROCEED</button>
				</div>
            </form>
        </div>
    </div>
</div>
<?=$this->endSection();?>
