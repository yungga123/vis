<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
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
                                                <td class="q1_sales_target">0</td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>Q2</td>
                                                <td class="q2_booked">0</td>
                                                <td class="q2_hit">0</td>
                                                <td class="q2_miss">0</td>
                                                <td class="q2_booked_amt">0.00</td>
                                                <td class="q2_sales_target">0</td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>Q3</td>
                                                <td class="q3_booked">0</td>
                                                <td class="q3_hit">0</td>
                                                <td class="q3_miss">0</td>
                                                <td class="q3_booked_amt">0.00</td>
                                                <td class="q3_sales_target">0</td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>Q4</td>
                                                <td class="q4_booked">0</td>
                                                <td class="q4_hit">0</td>
                                                <td class="q4_miss">0</td>
                                                <td class="q4_booked_amt">0.00</td>
                                                <td class="q4_sales_target">0</td>
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
<?= $this->endSection(); ?>