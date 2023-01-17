<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
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
                                                <td><?= count($bookedNumber) ;?></td>
                                                
                                                <td>
                                                    <?php $sumBooked = 0 ;?>
                                                    <?php foreach ($bookedNumber as $item) : ?>
                                                        <?php $sumBooked += $item['project_amount'] ?>
                                                    <?php endforeach ?>
                                                    <?= number_format($sumBooked,2) ;?>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td>Negotiation</td>
                                                <td><?= count($negotiationNumber) ;?></td>
                                                <td>
                                                    <?php $sumNegotiation = 0 ;?>
                                                    <?php foreach ($negotiationNumber as $item) : ?>
                                                        <?php $sumNegotiation += $item['project_amount'] ?>
                                                    <?php endforeach ?>
                                                    <?= number_format($sumNegotiation,2) ;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Evaluation</td>
                                                <td><?= count($evalNumber) ;?></td>
                                                <td>
                                                    <?php $sumEval = 0 ;?>
                                                    <?php foreach ($evalNumber as $item) : ?>
                                                        <?php $sumEval += $item['project_amount'] ?>
                                                    <?php endforeach ?>
                                                    <?= number_format($sumEval,2) ;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Developed Solution</td>
                                                <td><?= count($devsolNumber) ;?></td>
                                                <td>
                                                    <?php $sumDevsol = 0 ;?>
                                                    <?php foreach ($devsolNumber as $item) : ?>
                                                        <?php $sumDevsol += $item['project_amount'] ?>
                                                    <?php endforeach ?>
                                                    <?= number_format($sumDevsol,2) ;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Qualified</td>
                                                <td><?= count($qualifiedNumber) ;?></td>
                                                <td>
                                                    <?php $sumQualified = 0 ;?>
                                                    <?php foreach ($qualifiedNumber as $item) : ?>
                                                        <?php $sumQualified += $item['project_amount'] ?>
                                                    <?php endforeach ?>
                                                    <?= number_format($sumQualified,2) ;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Identified</td>
                                                <td><?= count($identifiedNumber) ;?></td>
                                                <td>
                                                    <?php $sumIdentified = 0 ;?>
                                                    <?php foreach ($identifiedNumber as $item) : ?>
                                                        <?php $sumIdentified += $item['project_amount'] ?>
                                                    <?php endforeach ?>
                                                    <?= number_format($sumIdentified,2) ;?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>