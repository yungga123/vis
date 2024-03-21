<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\EmployeeViewModel;
use App\Models\SalaryRateModel;
use monken\TablesIgniter;

class SalaryRate extends BaseController
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
        $this->_model           = new SalaryRateModel(); // Current model
        $this->_module_code     = MODULE_CODES['salary_rates']; // Current module
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
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $data['title']          = 'Payroll | Employee Salary Rates';
        $data['page_title']     = 'Payroll | Employee Salary Rates';
        $data['btn_add_lbl']    = 'Add Salary Rate';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = ['payroll/salary_rate/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'salary_rate' => [
                'list'      => url_to('payroll.salary_rate.list'),
                'fetch'     => url_to('payroll.salary_rate.fetch'),
                'delete'    => url_to('payroll.salary_rate.delete'),
            ],
            'employee' => [
                'common' => [
                    'search' => url_to('employee.common.search'),
                ]
            ],
        ]);

        return view('payroll/salary_rate/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $empModel   = new EmployeeViewModel();
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);
        $fields     = [            
            'employee_id',
            'employee_name',
            'position',
            'employment_status',
            'rate_type',
            'salary_rate',
            'payout',
            'created_by',
            'created_at'
        ];
        $fields[6]  = null;
        $fields1    = $fields;
        $fields1[6] = $this->_model->dtPayout();

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.employee_id",
                "{$empModel->table}.employee_name",
                "{$this->_model->table}.salary_rate",
            ])
            ->setOrder(array_merge([null, null], $fields))
            ->setOutput(
                array_merge(
                    [dt_empty_col(), $this->_model->buttons($this->_permissions)], 
                    $fields1
                )
            );
        
        return $table->getDatatable();

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
            'message'   => res_lang('success.added', 'Salary Rate')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $request        = $this->request->getVar();
                $employee_id    = $request['employee_id'] ?? '';
                $inputs         = [
                    'employee_id'   => $employee_id,
                    'rate_type'     => $request['rate_type'],
                    'salary_rate'   => $request['salary_rate'],
                    'payout'        => $request['payout'],
                    'is_current'    => 1,
                ];
                $action         = empty($id) ? ACTION_ADD : ACTION_EDIT;

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
    
                if ($id) {
                    unset($inputs['employee_id']);

                    $save   = $this->_model->modify($id, $inputs);

                    $data['message']    = res_lang('success.updated', 'Salary Rate');
                } else {
                    $inputs[] = $inputs;
                    if (! empty($employee_id) && is_array($employee_id)) {
                        $inputs = [];

                        foreach ($employee_id as $empId) {
                            $inputs[] = [
                                'employee_id'   => $empId,
                                'rate_type'     => $request['rate_type'],
                                'salary_rate'   => $request['salary_rate'],
                                'payout'        => $request['payout'],
                                'is_current'    => 1,
                                'created_by'    => session('username'),
                            ];
                        }
                    }

                    $save   = $this->_model->insertBatch($inputs);
                }

                if (! $save) {
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
            'message'   => res_lang('success.retrieved', 'Salary Rate')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $empVModel  = new EmployeeViewModel();
                $model      = $this->_model;
                $table      = $model->table;
                $columns    = "
                    {$table}.employee_id,
                    {$empVModel->table}.employee_name,
                    {$table}.rate_type,
                    {$table}.salary_rate,
                    {$table}.payout
                ";
                $record     = $model->joinEmployeesView()->fetch($id, $columns);

                $data['data'] = $record;
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
            'message'   => res_lang('success.deleted', 'Salary Rate')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);

                $id = $this->request->getVar('id');

                if (! $this->_model->remove($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }
                return $data;
            }
        );

        return $response;
    }
}
