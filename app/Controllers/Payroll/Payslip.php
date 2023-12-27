<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\EmployeeViewModel;
use App\Models\PayrollModel;
use App\Models\PayrollEarningModel;
use App\Models\PayrollDeductionModel;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use App\Traits\PayrollSettingTrait;
use monken\TablesIgniter;

class Payslip extends BaseController
{
    /* Declare trait here to use */
    use GeneralInfoTrait, HRTrait, PayrollSettingTrait;

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
     * Use to check if can add
     * @var bool
     */
    private $_can_view_all;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model           = new PayrollModel(); // Current model
        $this->_module_code     = MODULE_CODES['payslip']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, ACTION_ADD);
        $this->_can_view_all    = $this->checkPermissions($this->_permissions, ACTION_VIEW_ALL);
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
        
        $data['title']          = 'Payslip List';
        $data['page_title']     = 'Payslip List';
        $data['can_view_all']   = $this->_can_view_all;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        $data['custom_js']      = ['payroll/payslip/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'payroll' => [
                'payslip' => [
                    'list'      => url_to('payroll.payslip.list'),
                    'fetch'     => url_to('payroll.payslip.fetch'),
                    'delete'    => url_to('payroll.payslip.delete'),
                ],
            ],
        ]);

        return view('payroll/payslip/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $empVModel  = new EmployeeViewModel();
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request, $this->_permissions);
        $fields     = [            
            'id',
            'employee_id',
            'employee_name',
            'position',
            'cutoff_period',
            'cutoff_pay',
            'gross_pay',
            'net_pay',
            'salary_type',
            'working_days',
            'notes',
            'processed_by',
            'processed_at'
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.employee_id",
                "{$empVModel->table}.employee_name",
            ])
            ->setDefaultOrder("id",'desc')
            ->setOrder(array_merge([null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(), 
                        $this->_model->buttons($this->_permissions, $this->_can_view_all)
                    ],
                    $fields
                )
            );
        
        return $table->getDatatable();

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
            'message'   => res_lang('success.retrieved', 'Payslip')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $record         = $this->_model->select($this->_model->allowedFields)
                    ->where('id', $id)->first();
                    

                $data['data']   = $record;
                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Deleting record
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Payslip')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);

                if (! $this->_model->delete($this->request->getVar('id'))) {
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
     * Printing record
     *
     * @return view
     */
    public function print($id) 
    {
        $model      = $this->_model;
        $empVModel  = new EmployeeViewModel();
        $start      = dt_sql_date_format("{$model->table}.cutoff_start");
        $end        = dt_sql_date_format("{$model->table}.cutoff_end");
        $columns    = "
            {$model->table}.id,
            {$model->table}.employee_id,
            {$empVModel->table}.employee_name,
            {$empVModel->table}.position,
            {$empVModel->table}.employment_status,
            {$empVModel->table}.sss_no,
            {$empVModel->table}.tin_no,
            {$empVModel->table}.philhealth_no,
            {$empVModel->table}.pag_ibig_no,
            CONCAT_WS(' - ', {$start}, {$end}) AS cutoff_period,
            ".dt_sql_number_format("{$model->table}.gross_pay")." AS gross_pay,
            ".dt_sql_number_format("{$model->table}.net_pay")." AS net_pay,
            {$model->table}.cutoff_pay,
            {$model->table}.salary_type,
            ".dt_sql_number_format("{$model->table}.basic_salary")." AS basic_salary,
            {$model->table}.daily_rate,
            {$model->table}.hourly_rate,
            CONCAT({$model->table}.working_days, ' Days') AS working_days,
            {$model->table}.notes
        ";

        $this->traitJoinEmployees($model, 'employee_id', "{$model->table}.employee_id", '', 'left', true);

        // Get payroll
        $payroll    = $model->fetch($id, $columns);

        // For restriction
        if (empty($payroll)) {
            return $this->redirectTo404Page();
        }

        // Get payroll earnings and deductions
        $earnModel  = new PayrollEarningModel();
        $deduModel  = new PayrollDeductionModel();
        $earnings   = $earnModel->fetch($id);
        $deductions = $deduModel->fetch($id);
        
        $data['payroll']        = $payroll;
        $data['earnings']       = $earnings;
        $data['deductions']     = $deductions;
        $data['general_info']   = $this->getCompanyInfo();
        $data['settings']       = $this->getOvertimeHolidayRates(true);
        $data['title']          = 'Print Payslip';

        return view('payroll/payslip/print', $data);
    }
}
