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

    public function upload() {

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
            //helper('filesystem');
            $data['errors'] = $this->validator->getErrors();
            $data['test_id'] = $id;
            
            // $taskleadView = new TaskLeadView();
            // $data['title'] = 'Project Booked Details';
            // $data['uri'] = service('uri');
            // $data['project_detail'] = $taskleadView->find($id);

            // $path = '../public/uploads/project-booked/' . $id;

            // $data['map'] = directory_map($path);

            return $this->response->setJSON($data);
        }


        $img = $this->request->getFile('project_file');

        if (!$img->hasMoved()) {
            //$filepath = WRITEPATH . 'uploads/' . $img->store($id,$img->getClientName());
            $filename = $img->getClientName();
            $filepath = '../public/uploads/project-booked/'.$id;
            $img->move($filepath,$filename);

            $data['message'] = 'File has been uploaded.';

            // $data = ['uploaded_flleinfo' => new File($filepath)];
            // $data['title'] = 'Upload File Success';
            // $data['page_title'] = 'File upload success!';
            // $data['href'] = site_url('project-list-booked');
            // $data['uri'] = service('uri');
            // $data['id'] = $id;

            //  return view('templates/header', $data)
            //  .view('task_lead/header')
            //  .view('templates/navbar')
            //  .view('templates/sidebar')
            //  .view('templates/file-successpage')
            //  .view('templates/footer')
            //  .view('task_lead/script');
        }
        $data['success'] = true;
        return $this->response->setJSON($data);
    }

    public function getTaskleadFiles() {
        helper('filesystem');
        $id = $this->request->getVar('id');

        $path = '../public/uploads/project-booked/' . $id;

        $data['link'] = base_url('assets/uploads/project-booked/');
        $data['map'] = directory_map($path);

        return $this->response->setJSON($data);
    }
}
