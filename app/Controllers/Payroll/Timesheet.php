<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\TimesheetModel;
use App\Models\EmployeeViewModel;
use App\Traits\PayrollSettingTrait;
use monken\TablesIgniter;

class Timesheet extends BaseController
{
    /* Declare trait here to use */
    use PayrollSettingTrait;

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
        $this->_model           = new TimesheetModel(); // Current model
        $this->_module_code     = MODULE_CODES['timesheets']; // Current module
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

        $data['title']          = 'Payroll | Manage Timesheets / Attendace';
        $data['page_title']     = 'Payroll | Manage Timesheets / Attendace';
        $data['btn_add_lbl']    = 'Add Timesheet';
        $data['can_add']        = $this->_can_add;
        $data['can_view_all']   = check_permissions($this->_permissions, ACTION_VIEW_ALL);
        $data['office_hours']   = $this->getOfficeHours(true);
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = [
            'payroll/timesheet/index.js',
            'payroll/timesheet/common.js',
            'dt_filter.js'
        ];
        $data['routes']         = json_encode([
            'payroll' => [
                'timesheet' => [
                    'list'      => url_to('payroll.timesheet.list'),
                    'fetch'     => url_to('payroll.timesheet.fetch'),
                    'delete'    => url_to('payroll.timesheet.delete'),
                    'clock'     => url_to('payroll.timesheet.clock'),
                ],
            ],
        ]);

        return view('payroll/timesheet/index', $data);
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
        $builder    = $this->_model->noticeTable($request, $this->_permissions);
        $fields     = [
            'employee_id',
            'employee_name',
            'clock_date',
            'clock_in',
            'clock_out',
            'total_hours',
            'early_in',
            'late',
            'early_out',
            'overtime',
            'clock_type',
            'remark',
            'created_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.employee_id",
                "{$empModel->table}.employee_name",
            ])
            ->setOrder(array_merge([null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(), 
                        $this->_model->buttons($this->_permissions),
                    ], 
                    $fields
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
            'message'   => res_lang('success.added', 'Timesheet')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $request        = $this->request->getVar();
                $inputs         = [
                    'id'            => $id,
                    'employee_id'   => session('employee_id'),
                    'clock_date'    => $request['clock_date'],
                    'clock_in'      => $request['clock_in'],
                    'clock_out'     => $request['clock_out'],
                    'remark'        => clean_param($request['remark'] ?? ''),
                    'is_manual'     => 1,
                ];
                $action         = empty($id) ? ACTION_ADD : ACTION_EDIT;

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
    
                if ($id) {
                    unset($inputs['is_manual']);
                    $data['message']    = res_lang('success.updated', 'Timesheet');
                }

                if (! $this->_model->save($inputs)) {
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
            'message'   => res_lang('success.retrieved', 'Timesheet')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                if ($this->request->getVar('current')) {
                    $model  = $this->_model;
                    // Add where clause
                    $model->where('is_manual = 0');
                    $model->where("{$model->table}.clock_date", current_date());
                    $model->where('employee_id', session('employee_id'));

                    $record = $this->_model->fetch(0);

                    if (! empty($record)) {
                        $record['_clock_in'] = $record['clock_in'] ? format_time($record['clock_in']) : $record['clock_in'];
                        $record['_clock_out'] = $record['clock_out'] ? format_time($record['clock_out']) : $record['clock_out'];
                    }
                } else {
                    $record = $this->_model->fetch($id);
                }

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
            'message'   => res_lang('success.deleted', 'Timesheet')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);

                if (! $this->_model->delete($id)) {
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
    public function clock()
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.saved', 'Timesheet')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                // Check if office hours in payroll setting is empty
                // then, throw an exception
                if (empty($this->getOfficeHours())) {
                    $message = "You can't CLOCK IN/OUT - need to input the <strong>office hours (time in/out)</strong> in payroll settings!";
                    throw new \Exception($message, 1);    
                }

                $action = $this->request->getVar('action');
                $model  = $this->_model;
                // Add where clause
                $model->where("{$model->table}.clock_date", current_date());
                $model->where('employee_id', session('employee_id'));

                $record = $model->fetch('');
                $_model = $this->_model;
                $inputs = [
                    'id'            => $this->request->getVar('id') ?? null,
                    'clock_date'    => current_date(),
                    $action         => current_date('H:i'),
                    'is_manual'     => 0,
                ];
                
                if (! empty($record)) {
                    if (! empty($record['clock_out']) && $record['is_manual'] != 0) {
                        throw new \Exception("You can't CLOCK IN/OUT - already have a manual added timesheet!", 1);                        
                    }

                    $_model->where('clock_date', current_date());
                    $_model->where('employee_id', session('employee_id'));

                    $inputs['clock_in']     = $record['clock_in'];
                } else {
                    $inputs['employee_id']  = session('employee_id');
                }

                if (! $_model->save($inputs)) {
                    $data['errors']     = $_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }

                return $data;
            },
        );

        return $response;
    }
}
