<div class="modal fade" id="modal-booked-details">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Project Booked Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Projects Detail</h3>
                        <input type="hidden" id="upload_id">

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-center text-muted">Project Cost</span>
                                                <span class="info-box-number text-center text-muted mb-0 project_amount"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-center text-muted">Project Start</span>
                                                <span class="info-box-number text-center text-muted mb-0 project_start_date"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-center text-muted">Project Expected End</span>
                                                <span class="info-box-number text-center text-muted mb-0 project_finish_date"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h4>Activity</h4>
                                        <div class="post">
                                            
                                        </div>
                                        <!-- <div class="post">
                                            <h3>Status: IDENTIFIED (10.00%)</h3>
                                            <p>
                                            </p>
                                            <p class="history_created_at"></p>
                                            <p class="rns_10"></p>
                                            <p></p>
                                        </div>

                                        <div class="post">
                                            <h3>Status: QUALIFED (30.00%)</h3>
                                            <p class="history_project"></p>
                                            <p class="rns_30"></p>
                                        </div>

                                        <div class="post">
                                            <h3>Status: DEVELOPED SOLUTION (50.00%)</h3>
                                            <p class="rns_50"></p>
                                            <p class="history_quotation_num"></p>
                                            <p class="history_project_amount"></p>
                                            <p class="history_forecast_close_date"></p>
                                            <p class="history_min_forecast_date"></p>
                                            <p class="history_max_forecast_date"></p>
                                        </div>

                                        <div class="post">
                                            <h3>Status: EVALUATION (70.00%)</h3>
                                            <p class="rns_70"></p>
                                        </div>

                                        <div class="post">
                                            <h3>Status: NEGOTIATION (90.00%)</h3>
                                            <p class="history_project_amount2"></p>
                                            <p class="rns_90"></p>
                                        </div>

                                        <div class="post">
                                            <h3>Status: BOOKED (100.00%)</h3>
                                            <p class="rns_100"></p>
                                            <p class="history_close_deal_date"></p>
                                            <p class="history_project_start_date"></p>
                                            <p class="history_project_finish_date"></p>
                                            <p class="history_project_duration"></p>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">

                                <br>
                                <div class="text-muted">
                                    <p class="text-sm">Project
                                        <b class="d-block project"></b>
                                    </p>

                                    <p class="text-sm">Client
                                        <b class="d-block customer_name"></b>
                                    </p>

                                    <p class="text-sm">Branch
                                        <b class="d-block branch_name"></b>
                                    </p>

                                    <p class="text-sm">Contact Number
                                        <b class="d-block contact_number"></b>
                                    </p>
                                    <p class="text-sm">Quotation Number
                                        <b class="d-block quotation_num"></b>
                                    </p>
                                    <p class="text-sm">Forecast Close date
                                        <b class="d-block forecast_close_date"></b>
                                    </p>
                                    <p class="text-sm">Min Forecast Date
                                        <b class="d-block min_forecast_date"></b>
                                    </p>
                                    <p class="text-sm">Max Forecast Date
                                        <b class="d-block max_forecast_date"></b>
                                    </p>
                                    <p class="text-sm">Hit?
                                        <b class="d-block status1"></b>
                                    </p>



                                    <p class="text-sm">Account Manager
                                        <b class="d-block employee_name"></b>
                                    </p>

                                </div>

                                <h5 class="mt-5 text-muted">Project files</h5>
                                <ul class="list-unstyled">
                                        <div class="files">

                                        </div>
                                        

                                </ul>
                                <div class="text-center mt-5 mb-3">
                                    <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-addfile">Add files</a>
                                    <!-- </?php foreach ($errors as $error) : ?>
                                        <li></?= esc($error) ?></li>
                                    </?php endforeach ?> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<?= $this->include('task_lead/upload_form') ?>



<!-- <div class="modal fade" id="modal-addfile">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add File</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                </?= form_upload('project_file', '', ["id" => "project_file"]); ?>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </div>
</div> -->