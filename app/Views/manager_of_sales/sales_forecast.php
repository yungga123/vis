<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Over-all Stats</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>QTY</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Booked</td>
                                            <td><?= count($bookedNumber); ?></td>

                                            <td>
                                                <?php $sumBooked = 0; ?>
                                                <?php foreach ($bookedNumber as $item) : ?>
                                                    <?php $sumBooked += $item['project_amount'] ?>
                                                <?php endforeach ?>
                                                <?= number_format($sumBooked, 2); ?>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Negotiation</td>
                                            <td><?= count($negotiationNumber); ?></td>
                                            <td>
                                                <?php $sumNegotiation = 0; ?>
                                                <?php foreach ($negotiationNumber as $item) : ?>
                                                    <?php $sumNegotiation += $item['project_amount'] ?>
                                                <?php endforeach ?>
                                                <?= number_format($sumNegotiation, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Evaluation</td>
                                            <td><?= count($evalNumber); ?></td>
                                            <td>
                                                <?php $sumEval = 0; ?>
                                                <?php foreach ($evalNumber as $item) : ?>
                                                    <?php $sumEval += $item['project_amount'] ?>
                                                <?php endforeach ?>
                                                <?= number_format($sumEval, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Developed Solution</td>
                                            <td><?= count($devsolNumber); ?></td>
                                            <td>
                                                <?php $sumDevsol = 0; ?>
                                                <?php foreach ($devsolNumber as $item) : ?>
                                                    <?php $sumDevsol += $item['project_amount'] ?>
                                                <?php endforeach ?>
                                                <?= number_format($sumDevsol, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Qualified</td>
                                            <td><?= count($qualifiedNumber); ?></td>
                                            <td>
                                                <?php $sumQualified = 0; ?>
                                                <?php foreach ($qualifiedNumber as $item) : ?>
                                                    <?php $sumQualified += $item['project_amount'] ?>
                                                <?php endforeach ?>
                                                <?= number_format($sumQualified, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Identified</td>
                                            <td><?= count($identifiedNumber); ?></td>
                                            <td>
                                                <?php $sumIdentified = 0; ?>
                                                <?php foreach ($identifiedNumber as $item) : ?>
                                                    <?php $sumIdentified += $item['project_amount'] ?>
                                                <?php endforeach ?>
                                                <?= number_format($sumIdentified, 2); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Quarterly Stats</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Quarter</th>
                                            <th>Booked</th>
                                            <th>Hit</th>
                                            <th>Missed</th>
                                            <th>Total Amount</th>
                                            <th>Quarterly Target Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>Q1</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>1,500,000.00</td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>Q2</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>1,500,000.00</td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>Q3</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>3,000,000.00</td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>Q4</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>3,000,000.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        sdfsdf     
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ;?>
