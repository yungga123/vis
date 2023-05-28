<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>


<!-- Main DashBoard -->
<div class="container-fluid">
    <input type="hidden" id="tasklead_url" value="<?= url_to('sales_manager.taskleads') ?>">
    <input type="hidden" id="tasklead_stats_url" value="<?= url_to('sales_manager.taskleads_stats') ?>">
    <input type="hidden" id="tasklead_quarterly_url" value="<?= url_to('sales_manager.taskleads_quarterly') ?>">
    <input type="hidden" id="employees_url" value="<?= url_to('sales_target.employees') ?>">
    <input type="hidden" id="employee_url" value="<?= url_to('sales_target.employee') ?>">
    <input type="hidden" id="target_sales_url" value="<?= url_to('sales_target.target_sales') ?>">
    <input type="hidden" id="delete_salestarget" value="<?= url_to('sales_target.delete') ?>">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
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
                                                <td class="booked_count">0</td>
                                                <td class="booked_amt">0.00</td>

                                            </tr>
                                            <tr>
                                                <td>Negotiation</td>
                                                <td class="negotiation_count">0</td>
                                                <td class="negotiation_amt">0.00</td>
                                            </tr>
                                            <tr>
                                                <td>Evaluation</td>
                                                <td class="evaluation_count">0</td>
                                                <td class="evaluation_amt">0.00</td>
                                            </tr>
                                            <tr>
                                                <td>Developed Solution</td>
                                                <td class="dev_sol_count">0</td>
                                                <td>0.00</td>
                                            </tr>
                                            <tr>
                                                <td>Qualified</td>
                                                <td class="qualified_count">0</td>
                                                <td class="qualified_amt">0.00</td>
                                            </tr>
                                            <tr>
                                                <td>Identified</td>
                                                <td class="identified_count">0</td>
                                                <td class="identified_amt">0.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Quarterly Stats</h3>
                                    <span class="float-right"><button type="button" class="btn btn-success text-bold modal_salestarget" data-toggle="modal" data-target="#model_salesTarget"><i class="fas fa-bullseye"></i> SET SALES TARGET</button></span>
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
                                                <td class="q1_booked">0</td>
                                                <td class="q1_hit">0</td>
                                                <td class="q1_miss">0</td>
                                                <td class="q1_booked_amt">0.00</td>
                                                <td class="q1_target_sales">0</td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>Q2</td>
                                                <td class="q2_booked">0</td>
                                                <td class="q2_hit">0</td>
                                                <td class="q2_miss">0</td>
                                                <td class="q2_booked_amt">0.00</td>
                                                <td class="q2_target_sales">0</td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>Q3</td>
                                                <td class="q3_booked">0</td>
                                                <td class="q3_hit">0</td>
                                                <td class="q3_miss">0</td>
                                                <td class="q3_booked_amt">0.00</td>
                                                <td class="q3_target_sales">0</td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>Q4</td>
                                                <td class="q4_booked">0</td>
                                                <td class="q4_hit">0</td>
                                                <td class="q4_miss">0</td>
                                                <td class="q4_booked_amt">0.00</td>
                                                <td class="q4_target_sales">0</td>
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
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <figure class="highcharts-figure">
                        <div id="chart_q1"></div>
                        <p class="highcharts-description">
                            Displaying Quarter 1 Stats this year.
                        </p>
                    </figure>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <figure class="highcharts-figure">
                        <div id="chart_q2"></div>
                        <p class="highcharts-description">
                            Displaying Quarter 2 Stats this year.
                        </p>
                    </figure>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <figure class="highcharts-figure">
                        <div id="chart_q3"></div>
                        <p class="highcharts-description">
                            Displaying Quarter 3 Stats this year.
                        </p>
                    </figure>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <figure class="highcharts-figure">
                        <div id="chart_q4"></div>
                        <p class="highcharts-description">
                            Displaying Quarter 4 Stats this year.
                        </p>
                    </figure>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- Modal for Sales Target -->


<!-- Modal -->
<div class="modal fade" id="model_salesTarget" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Sales Target</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="card">
                    <div class="card-body">
                        <table id="sales_target_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('sales_target.list'); ?>">
                            <thead class="nowrap">
                                <tr>
                                    <th>Action</th>
                                    <th>ID</th>
                                    <th>Employee Name</th>
                                    <th>Q1 Target</th>
                                    <th>Q2 Target</th>
                                    <th>Q3 Target</th>
                                    <th>Q4 Target</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="form_salestarget" class="with-label-indicator" action="<?= url_to('sales_target.save'); ?>" method="post" autocomplete="off">
                            <?= csrf_field(); ?>
                            <input type="hidden" id="id" name="id" readonly>
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="required" for="sales_id">Sales Personnel</label>
                                        <select name="sales_id" id="sales_id" class="form-control" aria-describedby="alert_sales_id">
                                            <option value="">--- PLEASE SELECT ---</option>
                                        </select>
                                        <small id="alert_sales_id" class="text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label class="required" for="q1_target">Q1 Target Amount</label>
                                        <input type="text" name="q1_target" id="q1_target" class="form-control" placeholder="Enter here..." aria-describedby="alert_q1_target">
                                        <small id="alert_q1_target" class="text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label class="required" for="q2_target">Q2 Target Amount</label>
                                        <input type="text" name="q2_target" id="q2_target" class="form-control" placeholder="Enter here..." aria-describedby="alert_q2_target">
                                        <small id="alert_q2_target" class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="required" for="q3_target">Q3 Target Amount</label>
                                        <input type="text" name="q3_target" id="q3_target" class="form-control" placeholder="Enter here..." aria-describedby="alert_q3_target">
                                        <small id="alert_q3_target" class="text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label class="required" for="q4_target">Q4 Target Amount</label>
                                        <input type="text" name="q4_target" id="q4_target" class="form-control" placeholder="Enter here..." aria-describedby="alert_q4_target">
                                        <small id="alert_q4_target" class="text-muted"></small>
                                    </div>
                                </div>

                            </div>

                    </div>

                    <div class="card-footer">
                        <div class="float-right">
                            <button type="submit" class="btn btn-success text-bold"><i class="fas fa-check"></i> SAVE</button>
                        </div>

                    </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger text-bold" data-dismiss="modal"><i class="fas fa-times"></i> CLOSE</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>