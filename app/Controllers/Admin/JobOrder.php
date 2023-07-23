<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobOrderModel;
use monken\TablesIgniter;

class JobOrder extends BaseController
{
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
        $data['custom_js']      = 'admin/job_order/index.js';
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
                    'quotations' => url_to('admin.common.quotations'),
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

        $table->setTable($this->_model->noticeTable($request))
            ->setSearch([
                'quotation',
                'type',
                'client',
                'manager',
                'work_type',
            ])
            ->setOrder([
                null,
                null,
                'id',
                'tasklead_id',
                'quotation',
                'type',
                'client',
                'manager',
                'work_type',
                'date_requested',
                'date_committed',
                'date_reported',
                'warranty',
                'comments',
                'remarks',
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                $this->_model->dtJOStatusFormat(),
                'id',
                'tasklead_id',
                'quotation',
                'type',
                'client',
                'manager',
                'work_type',
                'date_requested',
                'date_committed',
                'date_reported',
                'warranty',
                'comments',
                'remarks',
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of job order (inserting and updating job order)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Job Order has been added successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id         = $this->request->getVar('id');
            $inputs = [
                'tasklead_id'       => $this->request->getVar('tasklead_id'),
                'quotation'         => $this->request->getVar('quotation'),
                'work_type'         => $this->request->getVar('work_type'),
                'comments'          => $this->request->getVar('comments'),
                'date_requested'    => $this->request->getVar('date_requested'),
                'date_reported'     => $this->request->getVar('date_reported'),
                'date_committed'    => $this->request->getVar('date_committed'),
                'warranty'          => $this->request->getVar('warranty'),
                'status'            => 'pending',
                'created_by'        => session('username'),
            ];

            if (! empty($id)) {
                $inputs['id']       = $id;
                $data['message']    = 'Job Order has been updated successfully!';

                unset($inputs['status']);
                unset($inputs['created_by']);
            } 

            if (! $this->_model->save($inputs)) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
    
    /**
     * For getting the job order data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Job Order has been retrieved!'
        ];

        try {
            $id = $this->request->getVar('id');

            if ($this->request->getVar('status')) {
                $columns    = '
                    job_orders.remarks,
                    job_orders.date_committed,
                    task_lead_booked.tasklead_type AS type,
                    task_lead_booked.employee_name AS manager,
                ';                
                $record     = $this->_model->getJobOrders($id, $columns);
            } else {
                $record     = $this->_model->getJobOrders($id);
            }            

            $data['data']       = $record;
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Deleting job order
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Job Order has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id = $this->request->getVar('id');

            if (! $this->_model->delete($id)) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            } else {
                log_message('error', 'Deleted by {username}', ['username' => session('username')]);
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Changing status of job order
     *
     * @return json
     */
    public function change() 
    {
        $data = [];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id     = $this->request->getVar('id');
            $status = set_jo_status($this->request->getVar('status'));
            $inputs = ['status' => $status];

            if ($this->request->getVar('is_form')) { 
                $inputs['date_committed']   = $this->request->getVar('date_committed');
                $inputs['remarks']          = $this->request->getVar('remarks');
            }

            if (! $this->_model->update($id, $inputs)) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            } else {
                $data['status']     = STATUS_SUCCESS;
                $data['message']    = 'Job Order has been '. strtoupper($status) .' successfully!';
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
}
