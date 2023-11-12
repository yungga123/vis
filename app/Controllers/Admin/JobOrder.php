<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobOrderModel;
use App\Models\EmployeeModel;
use App\Models\TaskLeadView;
use App\Models\CustomerModel;
use App\Models\CustomerBranchModel;
use App\Traits\ExportTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class JobOrder extends BaseController
{
    /* Declare trait here to use */
    use ExportTrait, HRTrait;

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
        $this->_model       = new JobOrderModel(); // Current model
        $this->_module_code = MODULE_CODES['job_order']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    /**
     * Display the job order view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);

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
        $tlViewModel            = new TaskLeadView();
        $customerModel          = new CustomerModel();
        $employeeModel          = new EmployeeModel();
        $customerBranchModel    = new CustomerBranchModel();
        $table                  = new TablesIgniter();
        $request                = $this->request->getVar();
        $builder                = $this->_model->noticeTable($request);
        $fields                 = [
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
                "{$employeeModel->table}.firstname",
                "{$employeeModel->table}.lastname",
                "{$tlViewModel->table}.customer_name",
                "{$customerModel->table}.name",
                "{$tlViewModel->table}.quotation_num",
                "{$this->_model->table}.manual_quotation",
                "{$customerBranchModel->table}.branch_name",
                "{$tlViewModel->table}.branch_name",
            ])
            ->setOrder(array_merge([null, null], $fields))
            ->setOutput(
                array_merge(
                    [
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Job Order has been added successfully!'
        ];

        $response   = $this->customTryCatch(
            $data,
            function($data) {
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
                    'customer_id'       => $is_manual ? $this->request->getVar('customer_id') : null,
                    'customer_branch_id' => $is_manual ?$this->request->getVar('customer_branch_id') : null,
                    'created_by'        => session('username'),
                ];
    
                if (! empty($id)) {
                    $inputs['id']           = $id;
                    $inputs['employee_id']  = $employee_id;
                    $data['message']        = 'Job Order has been updated successfully!';
    
                    unset($inputs['status']);
                    unset($inputs['created_by']);
                } 
    
                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                }

                return $data;
            },
            true
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Job Order has been retrieved!'
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
                        IF({$this->_model->table}.is_manual = 0, {$tlViewModel->table}.tasklead_type, {$this->_model->table}.manual_quotation_type) AS type,
                        {$tlViewModel->table}.employee_id,
                    ";                
                    $record     = $this->_model->getJobOrders($id, $columns);
                } else {
                    $record     = $this->_model->getJobOrders($id);
                }   

                $data['data']   = $record;

                return $data;
            }
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Job Order has been deleted successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    log_message('error', "Job Order #: {$id} \n Deleted by {username}", ['username' => session('username')]);
                }

                return $data;
            },
            true
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
                $id     = $this->request->getVar('id');
                $status = set_jo_status($this->request->getVar('status'));
                $inputs = ['status' => $status];
    
                if ($this->request->getVar('is_form')) { 
                    $inputs['employee_id']      = $this->request->getVar('employee_id');
                    $inputs['date_committed']   = $this->request->getVar('date_committed');
                    $inputs['remarks']          = $this->request->getVar('remarks');
                    $inputs['manual_quotation_type'] = $this->request->getVar('manual_quotation_type') ?? null;
                }
    
                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    $data['status']     = STATUS_SUCCESS;
                    $data['message']    = 'Job Order has been '. strtoupper($status) .' successfully!';
                }

                return $data;
            },
            true
        );

        return $response;
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        $builder        = $this->_model->select($this->_model->dtColumns());

        $this->_model->joinWithOtherTables($builder, true);
        $builder->orderBy("{$this->_model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'JO #',
            'Task Lead #',
            'Is Manual Quotation',
            'Quotation',
            'Quotation Type',
            'Client Type',
            'Client',
            'Client Branch',
            'Manager',
            'Work Type',
            'Date Requested',
            'Date Committed',
            'Date Reported',
            'Warranty',
            'Comments',
            'Remarks',
            'Requested By',
            'Requested At',
            'Accepted By',
            'Accepted At',
            'Filed By',
            'Filed At',
            'Discarded By',
            'Discarded At',
            'Reverted By',
            'Reverted At'
        ];
        $filename   = 'Job Orders Masterlist';

        $this->exportToCsv($data, $header, $filename);
    }
}
