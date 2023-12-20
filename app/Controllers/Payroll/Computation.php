<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\PayrollModel;
use App\Models\PayrollEarningModel;
use App\Models\PayrollDeductionModel;

class Computation extends BaseController
{
    /**
     * Use to initialize model class
     * @var object
     */
    private $_model;

    /**
     * Use to get current module code
     * @var string
     */
    private $_module_code;
    
    /**
     * Use to get current permissions
     * @var array
     */
    private $_permissions;

    /**
     * Use to check if can add
     * @var bool
     */
    private $_can_add;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model           = new PayrollModel(); // Current model
        $this->_module_code     = MODULE_CODES['payroll_computation']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, 'SUBMIT');
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);
        
        $data['title']          = 'Payroll Computation';
        $data['page_title']     = 'Payroll Computation';
        $data['can_submit']     = $this->_can_add;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['moment']         = true;
        $data['custom_js']      = ['payroll/computation/index.js', 'moment.js'];
        $data['custom_css']     = 'payroll/computation/index.css';
        $data['routes']         = json_encode([
            'payroll'    => [
                'computation' => [
                    'save'      => url_to('payroll.computation.save'),
                ],
            ],
            'hr' => [
                'common'    => [
                    'employees' => url_to('hr.common.employees'),
                ],
            ],
        ]);

        return view('payroll/computation/index', $data);
    }

    /**
     * For saving data
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved', 'Payroll Computation')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $request        = $this->request->getVar();
                $payroll_id     = $request['employee_info']['id'];
                $action         = empty($payroll_id) ? ACTION_ADD : ACTION_EDIT;

                $this->checkRoleActionPermissions($this->_module_code, $action, true);

                $payroll            = [
                    'id'            => $payroll_id,
                    'employee_id'   => $request['employee_info']['employee_id'],
                    'cutoff_start'  => $request['cut_off']['start_date'],
                    'cutoff_end'    => $request['cut_off']['end_date'],
                    'gross_pay'     => $request['employee_info']['gross_pay'],
                    'net_pay'       => $request['employee_info']['net_pay'],
                    'salary_type'   => $request['employee_info']['rate_type'],
                    'basic_salary'  => $request['employee_info']['salary_rate'],
                    'cutoff_pay'    => $request['employee_info']['cut_off_pay'],
                    'daily_rate'    => $request['employee_info']['daily_rate'],
                    'hourly_rate'   => $request['employee_info']['hourly_rate'],
                    'working_days'  => $request['employee_info']['working_days'],
                    'notes'         => $request['employee_info']['notes'] ?? null,
                ];

                if (! $this->_model->save($payroll)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    // Get inserted id
                    $payroll_id = empty($payroll_id) ? $this->_model->getInsertID() : $payroll_id;

                    $payroll_earnings   = [
                        'payroll_id'            => $payroll_id,
                        'working_days_off'      => $request['payroll_earnings']['working_days_off'],
                        'working_days_off_amt'  => $request['payroll_earnings']['working_days_off_amt'],
                        'over_time'             => $request['payroll_earnings']['over_time'],
                        'over_time_amt'         => $request['payroll_earnings']['over_time_amt'],
                        'night_diff'            => $request['payroll_earnings']['night_diff'],
                        'night_diff_amt'        => $request['payroll_earnings']['night_diff_amt'],
                        'regular_holiday'       => $request['payroll_earnings']['regular_holiday'],
                        'regular_holiday_amt'   => $request['payroll_earnings']['regular_holiday_amt'],
                        'special_holiday'       => $request['payroll_earnings']['special_holiday'],
                        'special_holiday_amt'   => $request['payroll_earnings']['special_holiday_amt'],
                        'vacation_leave_amt'    => $request['payroll_earnings']['vacation_leave_amt'],
                        'sick_leave'            => $request['payroll_earnings']['sick_leave'],
                        'sick_leave_amt'        => $request['payroll_earnings']['sick_leave_amt'],
                        'incentives'            => $request['payroll_earnings']['incentives'],
                        'commission'            => $request['payroll_earnings']['commission'],
                        'thirteenth_month'      => $request['payroll_earnings']['thirteenth_month'],
                        'add_back'              => $request['payroll_earnings']['add_back'],
                    ];

                    $payroll_deductions   = [
                        'payroll_id'        => $payroll_id,
                        'days_absent'       => $request['payroll_deductions']['absent'],
                        'days_absent_amt'   => $request['payroll_deductions']['absent_amt'],
                        'hours_late'        => $request['payroll_deductions']['tardiness'],
                        'hours_late_amt'    => $request['payroll_deductions']['tardiness_amt'],
                        'addt_rest_days'    => $request['payroll_deductions']['additional_rest_day'],
                        'night_diff_amt'    => $request['payroll_deductions']['additional_rest_day_amt'],
                        'govt_sss'          => $request['payroll_deductions']['sss'],
                        'govt_pagibig'      => $request['payroll_deductions']['pagibig'],
                        'govt_philhealth'   => $request['payroll_deductions']['philhealth'],
                        'withholding_tax'   => $request['payroll_deductions']['withholding_tax'],
                        'cash_advance'      => $request['payroll_deductions']['cash_advance'],
                        'others'            => $request['payroll_deductions']['other_deductions'],
                    ];

                    $earningModel   = new PayrollEarningModel();
                    $deductionModel = new PayrollDeductionModel();

                    // Save data
                    $earningModel->save($payroll_earnings);
                    $deductionModel->save($payroll_deductions);
                }
    
                if ($payroll_id) {
                    $data['message']    = res_lang('success.updated', 'Payroll Computation');
                }

                $data['data']['id'] = $payroll_id;
                return $data;
            }
        );

        return $response;
    }
}
