<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobOrderModel;
use App\Models\EmployeeModel;
use App\Models\TaskLeadView;
use App\Models\CustomerModel;
use App\Models\CustomerBranchModel;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class JobOrder extends BaseController
{
    /* Declare trait here to use */
    use HRTrait;

    /**
     * Use to initialize JobOrderModel class
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
        $this->_model       = new JobOrderModel(); // Current model
        $this->_module_code = MODULE_CODES['job_orders']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    /**
     * Display the job order view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $data['title']          = 'Job Order List';
        $data['page_title']     = 'Job Order List';
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = $this->_can_add ? 'Add Job Order' : '';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['admin/job_order/index.js', 'admin/common.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'job_order' => [
                'list'      => url_to('job_order.list'),
                'save'      => url_to('job_order.save'),
                'fetch'     => url_to('job_order.fetch'),
                'delete'    => url_to('job_order.delete'),
                'status'    => url_to('job_order.status'),
            ],
            'admin' => [
                'common' => [
                    'quotations'        => url_to('admin.common.quotations'),
                    'customers'         => url_to('admin.common.customers'),
                    'customer_branches' => url_to('admin.common.customer.branches'),
                ]
            ]
        ]);
        $data['php_to_js_options'] = json_encode([
            'status'    => get_jo_status('', true),
            'qtype'     => get_tasklead_type(),
            'worktype'  => get_work_type(),
        ]);

        return view('admin/job_order/index', $data);
    }

    /**
     * Get list of job orders
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);
        $fields     = [
            'id',
            'tasklead_id',
            'is_manual',
            'quotation',
            'tasklead_type',
            'client',
            'customer_branch_name',
            'manager',
            'work_type',
            'date_requested',
            'date_committed',
            'date_reported',
            'warranty',
            'comments',
            'remarks',
            'created_by',
            'created_at',
            'accepted_by',
            'accepted_at',
            'filed_by',
            'filed_at',
            'discarded_by',
            'discarded_at',
            'reverted_by',
            'reverted_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->view}.quotation",
                "{$this->_model->view}.client_name",
                "{$this->_model->view}.manager",
            ])
            ->setOrder(array_merge([null, null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(),
                        $this->_model->buttons($this->_permissions),
                        $this->_model->dtJOStatusFormat(),
                    ], 
                    $fields
                )
            );

        return $table->getDatatable();
    }

    /**
     * Saving process of job order (inserting and updating job order)
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Job Order')
        ];

        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $action         = ACTION_ADD;
                $id             = $this->request->getVar('id');
                $is_manual      = $this->request->getVar('is_manual');
                $employee_id    = $this->request->getVar('employee_id');
                $inputs         = [
                    'tasklead_id'       => isset($is_manual) ? 0 : ($this->request->getVar('tasklead_id') ?? 0),
                    'employee_id'       => isset($is_manual) ? 0 : $employee_id,
                    'quotation'         => $this->request->getVar('quotation'),
                    'work_type'         => $this->request->getVar('work_type'),
                    'comments'          => $this->request->getVar('comments'),
                    'date_requested'    => $this->request->getVar('date_requested'),
                    'date_reported'     => $this->request->getVar('date_reported'),
                    'date_committed'    => $this->request->getVar('date_committed'),
                    'warranty'          => $this->request->getVar('warranty'),
                    'status'            => 'pending',
                    'is_manual'         => isset($is_manual),
                    'manual_quotation'  => isset($is_manual) ? $this->request->getVar('manual_quotation') : null,
                    'customer_id'       => isset($is_manual) ? $this->request->getVar('customer_id') : null,
                    'customer_branch_id' => isset($is_manual) ? $this->request->getVar('customer_branch_id') : null,
                    'created_by'        => session('username'),
                ];
    
                if (! empty($id)) {
                    $action                 = ACTION_EDIT;
                    $inputs['id']           = $id;
                    $inputs['employee_id']  = $employee_id;
                    $data['message']        = res_lang('success.updated', 'Job Order');
    
                    unset($inputs['status']);
                    unset($inputs['created_by']);
                } 

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
    
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
     * For getting the job order data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Job Order')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                if ($this->request->getVar('status')) {
                    $tlViewModel    = new TaskLeadView();
                    $columns        = "
                        {$this->_model->table}.remarks,
                        {$this->_model->table}.date_committed,
                        {$this->_model->table}.is_manual,
                        {$this->_model->view}.quotation_type AS type,
                        IF({$this->_model->table}.employee_id IS NOT NULL, {$this->_model->table}.employee_id,{$tlViewModel->table}.employee_id) AS employee_id
                    ";

                    $this->_model->joinTaskleadBooked($this->_model, $tlViewModel);
                    
                    $record     = $this->_model->getJobOrders($id, $columns);
                } else {
                    $record     = $this->_model->getJobOrders($id);
                }   

                $data['data']   = $record;

                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Deleting job order
     *
     * @return json
     */
    public function delete() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Job Order')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
                
                $id = $this->request->getVar('id');

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    log_msg(
                        $data['message']. " Job Order #: {$id} \nDeleted by: {username}",
                        ['username' => session('username')]
                    );
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * Changing status of job order
     *
     * @return json
     */
    public function change() 
    {
        $data       = [];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $_status    = $this->request->getVar('status');
                $status     = set_jo_status($_status);
                $inputs     = ['status' => $status];

                $this->checkRoleActionPermissions($this->_module_code, $_status, true);
    
                if ($this->request->getVar('is_form')) { 
                    $inputs['employee_id']      = $this->request->getVar('employee_id');
                    $inputs['date_committed']   = $this->request->getVar('date_committed');
                    $inputs['remarks']          = $this->request->getVar('remarks');
                    $inputs['manual_quotation_type'] = $this->request->getVar('manual_quotation_type') ?? null;
                }
    
                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $data['status']     = res_lang('status.success');
                    $data['message']    = res_lang('success.changed', ['Job Order', strtoupper($status)]);
                }

                return $data;
            },
            true
        );

        return $response;
    }
}
