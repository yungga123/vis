<?php

namespace App\Controllers\Sales;

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
     * @var array
     */
    private $_permissions;

    /**
     * Use to check if can add
     * @var bool
     */
    private $_can_add;

    /**
     * File path
     * @var string
     */
     private $_path_file;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new TaskLeadView(); // Current model
        $this->_module_code = MODULE_CODES['task_lead']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
        $this->_path_file   = WRITEPATH . '/project-booked/';
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

        $data['title']          = 'Task Lead | Booked';
        $data['page_title']     = 'Task Lead | Booked';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['dropzone']       = true;
        $data['custom_js']      = ['sales/tasklead/booked.js', 'dropzone.js'];
        $data['routes']         = json_encode([
            'tasklead' => [
                'booked_list'       => url_to('tasklead.booked.list'),
                'booked_details'    => url_to('tasklead.booked.details'),
                'booked_history'    => url_to('tasklead.booked.history'),
                'booked_files'      => url_to('tasklead.booked.files'),
                'booked_download'   => url_to('tasklead.booked.download'),
                'booked' => [
                    'files'         => site_url('sales/tasklead/booked/files'),
                    'files_remove'  => url_to('tasklead.booked.files.remove'),
                ],
            ],
        ]);

        return view('sales/task_lead/booked', $data);
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
                'customer_name',
                'status_percent',
                'quarter',
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

    public function get_booked_details() 
    {
        $id = $this->request->getVar('tasklead_id');
        $data = $this->_model->noticeTable()->where('id',$id)->get()->getResult();

        return $this->response->setJSON($data);
    }

    public function get_tasklead_history() 
    {

        $historyModel = new TaskleadHistorViewModel();

        $id = $this->request->getVar('tasklead_id');
        $data = $historyModel->where('tasklead_id',$id)->find();

        return $this->response->setJSON($data);
    }

    public function upload() 
    {

        $data['success'] = false;
        $data['errors'] = '';
        $id = $this->request->getVar('upload_id');

        $validationRule = [
            'project_file' => [
                'label' => 'File',
                'rules' => 'uploaded[project_file]'
                    . '|max_size[project_file,5000]'
                    . '|ext_in[project_file,xlsx,jpg,jpeg,csv,docx,pdf]'
            ],
        ];
        if (!$this->validate($validationRule)) {
            $data['errors'] = $this->validator->getErrors();
            $data['test_id'] = $id;

            return $this->response->setJSON($data);
        }


        $img = $this->request->getFile('project_file');

        if (!$img->hasMoved()) {
            $filename = $img->getClientName();
            $filepath = $this->_path_file . $id;
            $img->move($filepath,$filename);

            $data['message'] = 'File has been uploaded.';
        }

        $data['success'] = true;
        return $this->response->setJSON($data);
    }

    public function getTaskleadFiles() 
    {
        helper('filesystem');
        $id = $this->request->getVar('id');

        $path = $this->_path_file . $id;

        $data['map'] = directory_map($path);

        return $this->response->setJSON($data);
    }

    public function downloadFile()
    {
        $id = $this->request->getVar('id');
        $file = $this->request->getVar('file');
        $path = $this->_path_file . $id . '/' . $file;
        return $this->response->download($path,null);
    }
}
