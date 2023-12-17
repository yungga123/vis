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
     * @var string
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
        $this->_model           = new PayrollSettingModel(); // Current model
        $this->_module_code     = MODULE_CODES['payroll_settings']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, ACTION_ADD);
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
        
        $data['title']          = 'Payroll Settings';
        $data['page_title']     = 'Payroll Settings';
        $data['can_submit']     = $this->_can_add;
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

                if (! $this->_model->singleSave($inputs)) {
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
                'default_vacation_leave' => [
                    'label' => 'vacation leave',
                    'rules' => 'required|numeric'
                ],
                'default_sick_leave' => [
                    'label' => 'sick leave',
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
                'sss_contri_rate_employeer' => [
                    'label' => 'contri rate employeer',
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
                'sss_starting_msc' => [
                    'label' => 'starting MSC',
                    'rules' => 'required|numeric'
                ],
                'sss_last_msc' => [
                    'label' => 'last MSC',
                    'rules' => 'required|numeric'
                ],
                'sss_next_diff_amount' => [
                    'label' => 'next diff amount',
                    'rules' => 'required|numeric'
                ],
                'pagibig_contri_rate_employeer' => [
                    'label' => 'contri rate employeer',
                    'rules' => 'required|numeric'
                ],
                'pagibig_contri_rate_employee' => [
                    'label' => 'contri rate employee',
                    'rules' => 'required|numeric'
                ],
                'philhealth_contri_rate' => [
                    'label' => 'contriution rate',
                    'rules' => 'required|numeric'
                ],
            ],
        ];

        return $param ? $rules[$param] : [];
    }
}