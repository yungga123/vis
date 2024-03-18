<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\PayrollSettingModel;

class Settings extends BaseController
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
    private $_can_save;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model           = new PayrollSettingModel(); // Current model
        $this->_module_code     = MODULE_CODES['payroll_settings']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_save         = $this->checkPermissions($this->_permissions, ACTION_SAVE);
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);
        
        $data['title']          = 'Payroll | Settings';
        $data['page_title']     = 'Payroll | Settings';
        $data['can_save']       = $this->_can_save;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['inputmask']      = true;
        $data['select2']        = true;
        $data['custom_js']      = ['payroll/settings/index.js'];
        $data['routes']         = json_encode([
            'payroll'    => [
                'settings' => [
                    'save'  => url_to('payroll.settings.save'),
                    'fetch' => url_to('payroll.settings.fetch'),
                    'tax'   => [
                        'fetch'     => url_to('payroll.settings.tax.fetch'),
                        'delete'    => url_to('payroll.settings.tax.delete'),
                    ]
                ],
            ],
        ]);

        return view('payroll/settings/index', $data);
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
            'message'   => res_lang('success.saved')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_SAVE, true);

                $inputs     = [];
                $request    = $this->request->getVar();
                $param      = $request['rules'];

                unset($request['csrf_test_name']);
                unset($request['rules']);

                $rules      = $this->_rules($param);
                $request    = clean_param($request, '', ' %');
                
                if (! $this->validateData($request, $rules)) {
                    $data['errors']     = $this->validator->getErrors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');

                    return $data;
                } 

                foreach ($request as $key => $value) {
                    $inputs[] = [
                        'key'   => $key,
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'updated_by' => session('username'),
                    ];
                }

                $save = $this->_model->singleSave($inputs);

                if (! $save && $save != 0) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * For getting the item data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $data['data'] = $this->_model->fetchAll();
                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Rules for validation
     *
     * @return array
     */
    private function _rules($param) 
    {
        $rules = [
            'working_days' => [
                'working_days' => [
                    'label' => 'working days',
                    'rules' => 'required'
                ],
                'working_time_in' => [
                    'label' => 'working time in',
                    'rules' => 'required'
                ],
                'working_time_out' => [
                    'label' => 'working time out',
                    'rules' => 'required'
                ],
                'default_service_incentive_leave' => [
                    'label' => 'vacation leave',
                    'rules' => 'required|numeric'
                ],
            ],
            'overtime' => [
                'overtime' => [
                    'label' => 'overtime',
                    'rules' => 'required|numeric'
                ],
                'night_diff' => [
                    'label' => 'night diff',
                    'rules' => 'required|numeric'
                ],
                'rest_day' => [
                    'label' => 'rest day',
                    'rules' => 'required|numeric'
                ],
                'rest_day_overtime' => [
                    'label' => 'RD overtime',
                    'rules' => 'required|numeric'
                ],
                'regular_holiday' => [
                    'label' => 'RH overtime',
                    'rules' => 'required|numeric'
                ],
                'regular_holiday_overtime' => [
                    'label' => 'RH overtime',
                    'rules' => 'required|numeric'
                ],
                'special_holiday' => [
                    'label' => 'RH overtime',
                    'rules' => 'required|numeric'
                ],
                'special_holiday_overtime' => [
                    'label' => 'SH overtime',
                    'rules' => 'required|numeric'
                ],
            ],
            'government' => [
                'sss_contri_rate_employer' => [
                    'label' => 'contri rate employer',
                    'rules' => 'required|numeric'
                ],
                'sss_contri_rate_employee' => [
                    'label' => 'contri rate employee',
                    'rules' => 'required|numeric'
                ],
                'sss_salary_range_min' => [
                    'label' => 'minimun salaray range',
                    'rules' => 'required|numeric'
                ],
                'sss_salary_range_max' => [
                    'label' => 'maximum salaray range',
                    'rules' => 'required|numeric'
                ],
                'sss_next_diff_range_start_amount' => [
                    'label' => 'next diff range start amount',
                    'rules' => 'required|numeric'
                ],
                'sss_starting_msc' => [
                    'label' => 'starting MSC',
                    'rules' => 'required|numeric'
                ],
                'sss_last_msc' => [
                    'label' => 'last MSC',
                    'rules' => 'required|numeric'
                ],
                'sss_next_diff_msc_total_amount' => [
                    'label' => 'next diff MSC total amount',
                    'rules' => 'required|numeric'
                ],
                'pagibig_contri_rate_employer' => [
                    'label' => 'contri rate employer',
                    'rules' => 'required|numeric'
                ],
                'pagibig_contri_rate_employee' => [
                    'label' => 'contri rate employee',
                    'rules' => 'required|numeric'
                ],
                'pagibig_max_monthly_contri' => [
                    'label' => 'max monthly contri',
                    'rules' => 'required|numeric'
                ],
                'philhealth_contri_rate' => [
                    'label' => 'contribution rate',
                    'rules' => 'required|numeric'
                ],
                'philhealth_income_floor' => [
                    'label' => 'income floor',
                    'rules' => 'required|numeric'
                ],
                'philhealth_if_monthly_premium' => [
                    'label' => 'monthly premium',
                    'rules' => 'required|numeric'
                ],
                'philhealth_income_ceiling' => [
                    'label' => 'income ceiling',
                    'rules' => 'required|numeric'
                ],
                'philhealth_ic_monthly_premium' => [
                    'label' => 'monthly premium',
                    'rules' => 'required|numeric'
                ],
            ],
        ];

        return $param ? $rules[$param] : [];
    }
}