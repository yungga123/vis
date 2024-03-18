<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">   
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-lg" id="custom-tabs-form-tab" data-toggle="pill" href="#custom-tabs-form" role="tab" aria-controls="custom-tabs-form" aria-selected="false">Current Funds</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-lg" id="custom-tabs-list-tab" data-toggle="pill" href="#custom-tabs-list" role="tab" aria-controls="custom-tabs-list" aria-selected="true">Fund Transactions History</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-form" role="tabpanel" aria-labelledby="custom-tabs-form-tab">
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h2 class="funds text-bold"><?= number_format($funds, 2) ?></h2>
                                            <p>Current Funds</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-piggy-bank"></i></div>
                                        <a href="javascript:void(0)" class="small-box-footer py-2" role="button" onclick="btnReleaseFund(<?= $funds ?>, <?= $can_release ?>)">
                                            Release Fund <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <?php if ($can_release): ?>
                                        <?= $this->include('finance/funds/form'); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-list" role="tabpanel" aria-labelledby="custom-tabs-list-tab">
                            <?= $this->include('finance/funds/list'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
