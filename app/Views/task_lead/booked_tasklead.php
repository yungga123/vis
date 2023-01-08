<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $page_title ?></h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="error-page">
                    <h2 class="headline text-success">OK!</h2>

                    <div class="error-content">

                        <p>
                            You are updating your task lead to <label class="text-success"><?= $status_text ?></label>
                        </p>




                        <p><?= ($quotation_num != "")  ? "<h2>" . $quotation_num . "</h2> is the quotation number. Use this for your project proposal." : "" ?></p>
                    </div>
                </div>
                <!-- /.error-page -->
            </div>
        </div>


        <?= form_open('post-update-project-status', ["id" => "post-update-project-status"]); ?>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Please fill-in to complete the process.
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <input type="hidden" name="id" id="id" value="<?= $id; ?>">
                                <input type="hidden" name="status" id="status" value="<?= $status ?>">
                                <div class="form-group">
                                    <label for="quotation_num">Quotation Number</label>
                                    <input name="quotation_num" id="quotation_num" type="text" class="form-control" placeholder="Do not put anything here, this is system generated after developed solution" readonly>
                                    <small id="small_quotation_num" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label for="remark_next_step">Remark Next Step</label>
                                    <textarea name="remark_next_step" id="remark_next_step" type="text" class="form-control" placeholder="Enter here..."></textarea>
                                    <small id="small_remark_next_step" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label for="project">Project</label>
                                    <input name="project" id="project" type="text" class="form-control" placeholder="Enter Project">
                                    <small id="small_project" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label for="project_amount">Project Amount</label>
                                    <input name="project_amount" id="project_amount" type="text" class="form-control" placeholder="Input amount in numbers">
                                    <small id="small_project_amount" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label for="forecast_close_date">Forecast Close Date</label>
                                    <input name="forecast_close_date" id="forecast_close_date" type="date" class="form-control" placeholder="Input amount in numbers">
                                    <small id="small_forecast_close_date" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label for="min_forecast_date">Min. Forecast Date</label>
                                    <input name="min_forecast_date" id="min_forecast_date" type="date" class="form-control" placeholder="Input amount in numbers">
                                    <small id="small_min_forecast_date" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label for="max_forecast_date">Max Forecast Date</label>
                                    <input name="max_forecast_date" id="max_forecast_date" type="date" class="form-control" placeholder="Input amount in numbers">
                                    <small id="small_max_forecast_date" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <label for="close_deal_date">Close Deal Date</label>
                                    <input type="date" name="close_deal_date" id="close_deal_date" class="form-control">
                                    <small id="small_close_deal_date" class="text-muted"></small>
                                </div>

                                <div class="form-group">
                                    <label for="project_start_date">Project Start Date</label>
                                    <input type="date" name="project_start_date" id="project_start_date" class="form-control">
                                    <small id="small_project_start_date" class="text-muted"></small>
                                </div>

                                <div class="form-group">
                                    <label for="project_finish_date">Project Finish Date</label>
                                    <input type="date" name="project_finish_date" id="project_finish_date" class="form-control">
                                    <small id="small_project_finish_date" class="text-muted"></small>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a class="btn btn-danger" href="<?= site_url('project-list') ?>"><i class="fas fa-times"></i> Cancel</a>
                        <button type="submit" class="btn btn-success float-right"><i class="fas fa-check"></i> Submit</button>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
        <?= form_close() ?>


    </section>
    <!-- /.content -->
</div>