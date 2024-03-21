<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<?php
$form_code 			= isset($general_info['billing_invoice_form_code']) && !empty($general_info['billing_invoice_form_code'])
	? $general_info['billing_invoice_form_code'] : COMPANY_BILLING_INVOICE_FORM_CODE;
$overdue_interest	= $billing_invoice['overdue_interest'] ?? 0;
$vat_percent 		= floatval($general_info['vat_percent'] ?? 12);
$vat_percent 		= $vat_percent / 100;
$subtotal_amount	= $billing_invoice['billing_amount'] ?? 0;
$with_vat 			= ($billing_invoice['with_vat'] ?? 0) != '0';
$vat_amount			= $with_vat ? $subtotal_amount * $vat_percent : 0;
$vat_amount1		= $subtotal_amount * $vat_percent;
$total_amount		= $subtotal_amount + $vat_amount + $overdue_interest;
?>
<style>
	.container-fluid { font-family: 'Courier New', Courier, monospace; }
</style>
<div class="container-fluid">
	<div class="row">		
		<div class="col-6">
			<img src="<?= $company_info['company_logo'] ?? '' ?>" alt="Vinculum Logo" class="img-thumbnail mb-4" style="height: 120px; width: 380px">
		</div>
        <div class="col-1"></div>
        <div class="col-5">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr class="text-bold text-center">
                        <td colspan="2">
							BILLING INVOICE - <?= strtoupper($billing_invoice['billing_status']) ?>
						</td>
                    </tr>
                    <tr class="text-bold text-center">
                        <td colspan="2">
							<span class="text-danger">
								<?php 
									$date 			= format_date($billing_invoice['created_at'], 'y-md');
									$initials		= get_acronymns($billing_invoice['client']);
									$code_format 	= "{$form_code}-{$billing_invoice['client_id']}-{$initials}-{$date}-{$billing_invoice['id']}";
									echo $code_format;
								?>
							</span>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>
							<span class="text-bold">Billing Date:</span>
                            <br>
                            <?= format_date($billing_invoice['created_at'], 'F d, Y'); ?>
                        </td>
                        <td>
							<span class="text-bold">Due Date:</span>
                            <br>
                            <?= format_date($billing_invoice['due_date'], 'F d, Y'); ?>
                        </td>
                    </tr>
                    <br>
                </tbody>
            </table>
        </div>
	</div>
    <div class="row mt-3">
        <div class="col-sm-6">			
			<label>BILLED TO:</label>
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr class="text-center text-uppercase text-bold">
                        <th><?= $billing_invoice['client'] ?? '' ?></th>
                    </tr>
                    <tr class="text-center text-uppercase">
                        <td><?= $billing_invoice['client_address'] ?? '' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">			
			<label>BILLED BY: </label>
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr class="text-center text-uppercase text-bold">
                        <td><?= $company_info['company_name'] ?></td>
                    </tr>
                    <tr class="text-center">
                        <td><?= $company_info['company_address'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
                    <tr>
                        <td colspan="5">
							<strong>Attention:</strong>
							<span class="text-center" id="attention_to_text">
								<?= $billing_invoice['attention_to'] ?? '' ?>
							</span>
						</td>
                    </tr>
                    <tr>
                        <td class="text-bold text-center" colspan="4">
							Project Name & Reference <br>
							Qtn Ref. No.: 
							<span class="text-danger">
								<?= $billing_invoice['quotation'] ?? 'N/A' ?>
							</span>
						</td>
                        <td class="text-bold text-center">
							Total Project Cost <br>
							<span class="text-danger">
								PHP <?= number_format($billing_invoice['project_amount'] ?? 0, 2) ?>
							</span>
						</td>
                    </tr>
                    <tr>
                        <td class="text-bold text-center">Terms</td>
                        <td class="text-bold text-center">Qty</td>
                        <td class="text-bold text-center">Unit</td>
                        <td class="text-bold text-center">Description</td>
                        <td class="text-bold text-center">Payable Amount</td>
                    </tr>
                    <tr>
                        <td class="text-center">
							<?= get_bill_types($billing_invoice['bill_type']) ?>
						</td>
                        <td class="text-center">1</td>
                        <td class="text-center">Lot</td>
                        <td class="text-center">
							<?= $billing_invoice['project'] ?? 'N/A' ?>
						</td>
                        <td class="text-right">
							₱ <?= number_format($billing_invoice['billing_amount'] ?? 0, 2) ?>
						</td>
                    </tr>
					<tr>
                        <td class="text-bold text-center text-danger" colspan="5">***Nothing Follows***</td>
                    </tr>
                    <tr>
                        <td class="text-bold text-right" colspan="4">Sub Total (Vat Exclusive)</td>
                        <td class="text-right subtotal_amount">
							₱ <?= number_format($subtotal_amount, 2) ?>
						</td>
                    </tr>
                    <tr class="text-danger">
                        <td class="text-bold text-right" colspan="4">Plus 12% VAT</td>
                        <td class="text-right vat_amount">
							₱ <span><?= number_format($vat_amount, 2) ?></span>
						</td>
                    </tr>
					<?php if (! empty(floatval($overdue_interest))): ?>
						<tr>
							<td class="text-bold text-right" colspan="4">Overdue Interest</td>
							<td class="text-right overdue_interest">
								₱ <span><?= number_format($overdue_interest, 2) ?></span>
							</td>
						</tr>
					<?php endif; ?>
                    <tr>
                        <td class="text-bold text-right" colspan="4">Amount Due</td>
                        <td class="text-right total_amount">
							₱ <span><?= number_format($total_amount, 2) ?></span>
						</td>
                    </tr>
					<?php if (! empty(floatval($billing_invoice['amount_paid']))): ?>
						<tr>
							<td class="text-bold text-right" colspan="4">Amount Less</td>
							<td class="text-right overdue_interest">
								₱ <span><?= number_format($billing_invoice['amount_paid'], 2) ?></span>
							</td>
						</tr>
					<?php endif; ?>
                    <tr style="font-size: 17px">
						<?php $vat_text = $with_vat ? 'Inclusive' : 'Exclusive' ?>
                        <td class="text-bold text-right" colspan="4">Grand Total Vat <?= $vat_text ?></td>
                        <td class="text-right total_amount">
							₱ <span><?= number_format($total_amount, 2) ?></span>
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
            </h6>
        </div>
        <div class="col-12">
            <h6>
				<ol>
					<li>
						Payment is <strong>Cash</strong> or <strong>Dated Company Check</strong> payable to the following:
						<table class="table table-bordered table-sm mt-3" style="width: 40%;">
							<tbody>
								<tr>
									<td>Bank Account Name:</td>
									<td class="text-bold">
										<?= $company_info['company_bank_account_name'] ?? COMPANY_NAME ?>
									</td>
								</tr>
								<tr>
									<td>Account Number:</td>
									<td class="text-bold">
										<?= $company_info['company_bank_account_number'] ?? '00000-3960-1591' ?>
									</td>
								</tr>
								<tr>
									<td>Bank Name:</td>
									<td class="text-bold">
										<?= $company_info['company_bank_name'] ?? 'Security Bank' ?>
									</td>
								</tr>
								<tr>
									<td>Branch:</td>
									<td class="text-bold">
										<?= $company_info['company_bank_branch'] ?? 'BF Paranaque-Aguirre Branch' ?>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				</ol>
            </h6>
        </div>
    </div>
    <div class="row mt-5">
		<div class="col-4">
			<div>
				<h6>PREPARED BY:</h6>
				<h6 class="ml-5 mt-4">
					<span class="text-underline text-bold">
						<?= $billing_invoice['created_by'] ?? '' ?>
					</span><br> 
					<span><?= $billing_invoice['created_by_position'] ?? '' ?></span>
				</h6>
			</div>
		</div>
		<div class="col-4">
			<div>
				<h6>APPROVED BY:</h6>
				<h6 class="ml-5 mt-4">
					<span class="text-underline text-bold">
						<?= $billing_invoice['approved_by'] ?? 'Engr. Ginelou Niño T. Garzon' ?>
					</span><br> 
					<span><?= $billing_invoice['approved_by_position'] ?? 'CEO/President' ?></span>
				</h6>
			</div>
		</div>
        <div class="col-4">
			<div>
				<h6>RECEIVED BY:</h6>
				<h6 class="text-bold ml-5 mt-4">
					___________________________<br> 
					Signature over Printed Name
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
<div class="modal fade" id="print_billing_modal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Billing Invoice</h5>
            </div>
			<form id="print_billing_form" class="with-label-indicator" action="<?= url_to('finance.billing_invoice.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
				<input type="hidden" id="id" name="id" value="<?= $billing_invoice['id'] ?>" readonly>
				<div class="modal-body">
					<div class="form-group">
						<label class="required" for="attention_to">Attention to:</label>
						<input type="text" class="form-control" id="attention_to" name="attention_to" placeholder="Ex: Mr. / Ms. / Mrs. Antonette" value="<?= $billing_invoice['attention_to'] ?? '' ?>" required>
						<small id="alert_attention_to" class="text-danger"></small>
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
