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
                        <h3><i class="far fa-check-circle text-success"></i> Project is ready to be BOOKED!!</h3>

                        <p>
                            You are updating your task lead to <label class="text-success">BOOKED!!!</label>
                        </p>



                        <p><?= ($quotation_num != "")  ? "<h2>" . $quotation_num . "</h2> is the quotation number. Use this for your project proposal." : "" ?></p>
                    </div>
                </div>
                <!-- /.error-page -->
            </div>
        </div>


        <?= form_open('post-booked-status',["id" => "post-booked-status"]) ;?>
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
                                <input type="hidden" name="id" id="id" value="<?= $id ;?>">
                                <input type="hidden" name="status" id="status" value="100.00">
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