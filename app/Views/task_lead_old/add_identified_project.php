
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <?= $page_title ?>
                    </h1>
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
                
                <div class="card-header">
                    Fill-up Fields
                </div>
                <div class="card-body">
                    <?= form_open('add-project'); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quarter">Quarter</label>
                                <select class="form-control" name="quarter" id="quarter" aria-describedby="small_quarter" placeholder="">
                                    <option value="">--Please Select--</option>
                                    <option value="1" <?= set_select('quarter','1') ?>>1st Quarter</option>
                                    <option value="2" <?= set_select('quarter','2') ?>>2nd Quarter</option>
                                    <option value="3" <?= set_select('quarter','3') ?>>3rd Quarter</option>
                                    <option value="4" <?= set_select('quarter','4') ?>>4th Quarter</option>
                                </select>
                                <small id="small_quarter" class="form-text text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status" id="status" aria-describedby="small_status" placeholder="">
                                    <option value="10.00" <?= set_select('status','10.00') ?>>10% - Identified</option>
                                    <option value="30.00" <?= set_select('status','30.00') ?>>30% - Qualified</option>
                                    <option value="50.00" <?= set_select('status','50.00') ?>>50% - Developed Solution</option>
                                    <option value="70.00" <?= set_select('status','70.00') ?>>70% - Evaluation</option>
                                    <option value="90.00" <?= set_select('status','90.00') ?>>90% - Negotiation</option>
                                </select>
                                <small id="small_status" class="form-text text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="customer_id">Customer Name</label>
                                <select type="text" class="form-control" name="customer_id" id="customer_id" aria-describedby="small_customer_id" placeholder="" <?= set_value('customer_id') ?>>
                                    <option value=""></option>
                                    <?php foreach ($customers as $item) : ?>
                                        <option value="<?= $item['id'] ?>" <?= set_select('customer_id',$item['id']) ;?>><?= $item['customer_name'] ;?></option>
                                    <?php endforeach ?>
                                </select>
                                <small id="small_customer_id" class="form-text text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="remark_next_step">Remark Next Step</label>
                                <textarea class="form-control" name="remark_next_step" id="remark_next_step" aria-describedby="small_remark_next_step" placeholder=""><?= set_value('remark_next_step') ?></textarea>
                                <small id="small_remark_next_step" class="form-text text-muted"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="forecast_close_date">Forecast Close Date</label>
                                <input type="date" class="form-control" name="forecast_close_date" id="forecast_close_date" aria-describedby="small_forecast_close_date" placeholder="" value="<?= set_value('forecast_close_date') ?>">
                                <small id="small_forecast_close_date" class="form-text text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="min_forecast_date">Min Forecast Date</label>
                                <input type="date" class="form-control" name="min_forecast_date" id="min_forecast_date" aria-describedby="small_min_forecast_date" placeholder="" readonly value="<?= set_value('min_forecast_date') ?>">
                                <small id="small_min_forecast_date" class="form-text text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="max_forecast_date">Max Forecast Date</label>
                                <input type="date" class="form-control" name="max_forecast_date" id="max_forecast_date" aria-describedby="small_max_forecast_date" placeholder="" readonly value="<?= set_value('max_forecast_date') ?>">
                                <small id="small_max_forecast_date" class="form-text text-muted"></small>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right">SUBMIT</button>
                    <a href="<?= site_url('tasklead') ;?>" class="btn btn-primary" href="#" role="button">Tasklead Menu</a>
                    
                    <?= form_close(); ?>
                </div>
            </div>



        </div>
    </section>

</div>

