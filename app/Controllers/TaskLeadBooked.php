<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskleadHistorViewModel;
use App\Models\TaskLeadView;
use monken\TablesIgniter;

class TaskLeadBooked extends BaseController
{
    /**
     * Use to initialize PermissionModel class
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
        $this->_model       = new TaskLeadView(); // Current model
        $this->_module_code = MODULE_CODES['task_lead']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
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

        $data['title']          = 'Task Lead | Booked';
        $data['page_title']     = 'Task Lead | Booked';
        $data['custom_js']      = 'tasklead/booked.js';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;

        return view('task_lead/booked', $data);
    }

    /**
     * Get list of items
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table = new TablesIgniter();

        $table->setTable($this->_model->noticeTable())
            ->setSearch([
                'id',
                'employee_id',
                'employee_name',
                'customer_name',
                'branch_name',
                'project',
                'contact_number',
            ])
            ->setOrder([
                'id',
                'employee_name',
                'customer_name', // As details
                'status_percent',
                'quarter', // As details
                null,
            ])
            ->setOutput([
                'id',
                'employee_name',
                $this->_model->customerDetails(),
                'status_percent',
                $this->_model->dtDetails(),
                $this->_model->buttons(),
            ]);

        return $table->getDatatable();
    }

    public function get_booked_details() {
        $id = $this->request->getVar('tasklead_id');
        $data = $this->_model->noticeTable()->where('id',$id)->get()->getResult();

        return $this->response->setJSON($data);
    }

    public function get_tasklead_history() {

        $historyModel = new TaskleadHistorViewModel();

        $id = $this->request->getVar('tasklead_id');
        $data = $historyModel->where('tasklead_id',$id)->find();

        return $this->response->setJSON($data);
    }
}
