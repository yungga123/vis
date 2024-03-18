<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\EmployeeViewModel;
use App\Models\OvertimeModel;
use App\Traits\CommonTrait;
use DateTime;
use monken\TablesIgniter;

class Overtime extends BaseController
{
    /* Declare trait here to use */
    use CommonTrait;

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
        $this->_model           = new OvertimeModel(); // Current model
        $this->_module_code     = MODULE_CODES['overtime']; // Current module
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

        $data['title']          = 'Payroll | Manage Overtime';
        $data['page_title']     = 'Payroll | Manage Overtime';
        $data['btn_add_lbl']    = 'File an Overtime';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = ['payroll/overtime/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'overtime' => [
                'list'      => url_to('payroll.overtime.list'),
                'fetch'     => url_to('payroll.overtime.fetch'),
                'delete'    => url_to('payroll.overtime.delete'),
            ],
        ]);

        return view('payroll/overtime/index', $data);
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
            'date',
            'time_start',
            'time_end',
            'total_hours',
            'reason',
            'remark',
            'created_at',
            'processed_by',
            'processed_at',
            'approved_by',
            'approved_at',
            'discarded_by',
            'discarded_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.employee_id",
                "{$empModel->table}.employee_name",
            ])
            ->setOrder(array_merge([null, null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(), 
                        $this->_model->buttons($this->_permissions),
                        $this->_model->dtStatusFormat(),
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
            'message'   => res_lang('success.added', 'Overtime')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $request        = $this->request->getVar();
                $inputs         = [
                    'id'            => $id,
                    'employee_id'   => session('employee_id'),
                    'date'          => $request['date'],
                    'time_start'    => $request['time_start'],
                    'time_end'      => $request['time_end'],
                    'total_hours'   => get_time_diff($request['time_start'], $request['time_end'], '%H:%i'),
                    'reason'        => $request['reason'],
                ];
                $action         = empty($id) ? ACTION_ADD : ACTION_EDIT;

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model);
    
                if ($id) {
                    $data['message']    = res_lang('success.updated', 'Overtime');
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
            'message'   => res_lang('success.retrieved', 'Overtime')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $record     = $this->_model->fetch($id);

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
            'message'   => res_lang('success.deleted', 'Overtime')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

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
     * Changing status of the record
     *
     * @return json
     */
    public function change() 
    {
        $data       = [];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $request    = $this->request->getVar();
                $id         = $request['id'];
                $_status    = $request['status'];

                $this->checkRoleActionPermissions($this->_module_code, $_status, true);

                $status     = set_leave_status($_status);
                $inputs     = [
                    'status' => $status,
                    'remark' => $request['remark'],
                ];
                $check      = $this->_model->fetch($id, 'employee_id');

                if ($_status === 'approve' && $check['employee_id'] === session('employee_id')) {
                    throw new \Exception("You can't <strong>APPROVE</strong> your own overtime request!", 1);
                }

                // Is overtime with pay
                if ($request['with_pay'] ?? null) {
                    $inputs['with_pay'] = true;
                }

                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $data['status']     = res_lang('status.success');
                    $data['message']    = res_lang('success.changed', ['Overtime', strtoupper($status)]);
                }

                return $data;
            }
        );

        return $response;
    }
}
