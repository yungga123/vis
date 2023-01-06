<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersModel;
use App\Models\TaskLeadModel;
use App\Models\TaskLeadView;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\I18n\Time;
use monken\TablesIgniter;

class TaskLead extends BaseController
{

    use ResponseTrait;
    public function index()
    {
        if (session('logged_in') == true) {
            $data['title'] = 'Task/Leads Monitoring';
            $data['uri'] = service('uri');

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/task_lead_menu');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function add_project()
    {
        if (session('logged_in') == true) {
            $time = new Time('now');

            $customersModel = new CustomersModel();
            $data['title'] = 'Add Project';
            $data['page_title'] = 'Add Project';
            $data['customers'] = $customersModel->findAll();
            $data['uri'] = service('uri');
            $data['date_quarter'] = $time->getQuarter();


            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/add_project');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function add_project_validate()
    {
        $taskleadModel = new TaskLeadModel();
        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $data = [
            "employee_id" => $this->request->getPost('employee_id'),
            "quotation_num" => $this->request->getPost('quotation_num'),
            "quarter" => $this->request->getPost('quarter'),
            "status" => $this->request->getPost('status'),
            "customer_id" => $this->request->getPost('customer_id'),
            "project" => $this->request->getPost('project'),
            "project_amount" => $this->request->getPost('project_amount'),
            "remark_next_step" => $this->request->getPost('remark_next_step'),
            "forecast_close_date" => $this->request->getPost('forecast_close_date'),
            "min_forecast_date" => $this->request->getPost('min_forecast_date'),
            "max_forecast_date" => $this->request->getPost('max_forecast_date'),
        ];

        if (!$taskleadModel->insert($data)) {
            $validate['messages'] = $taskleadModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function edit_project_validate()
    {
        $taskleadModel = new TaskLeadModel();
        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $id = $this->request->getPost('id');
        $data = [
            "quotation_num" => $this->request->getPost('quotation_num'),
            "quarter" => $this->request->getPost('quarter'),
            "status" => $this->request->getPost('status'),
            "customer_id" => $this->request->getPost('customer_id'),
            "project" => $this->request->getPost('project'),
            "project_amount" => $this->request->getPost('project_amount'),
            "remark_next_step" => $this->request->getPost('remark_next_step'),
            "forecast_close_date" => $this->request->getPost('forecast_close_date'),
            "min_forecast_date" => $this->request->getPost('min_forecast_date'),
            "max_forecast_date" => $this->request->getPost('max_forecast_date'),
        ];

        if (!$taskleadModel->update($id, $data)) {
            $validate['messages'] = $taskleadModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function project_list()
    {
        if (session('logged_in') == true) {
            $data['title'] = 'Project List';
            $data['uri'] = service('uri');

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/project_list');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function manager_project_list()
    {

        if (session('logged_in') == true && (session('access') == 'manager' || session('access') == 'admin')) {
            $data['title'] = 'Managers Project List';
            $data['uri'] = service('uri');

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/project_list');
            echo view('templates/footer');
            echo view('task_lead/script');
        } elseif (session('logged_in') == true) {
            $data['title'] = 'Invalid Access';
            $data['page_title'] = 'Invalid Access';
            $data['href'] = site_url('tasklead');
            $data['uri'] = service('uri');

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('templates/offlimits');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function project_list_booked()
    {
        if (session('logged_in') == true) {
            // $taskleadModel = new TaskLeadModel();
            $taskleadView = new TaskLeadView();
            $data['title'] = 'Booked Project List';
            $data['uri'] = service('uri');
            $data['booked_projects'] = $taskleadView->paginate(10);
            $data['pager'] = $taskleadView->pager;

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/project_list_booked');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function manager_project_list_booked()
    {

        if (session('logged_in') == true && (session('access') == 'manager' || session('access') == 'admin')) {
            $data['title'] = 'Booked Managers Project List';
            $data['uri'] = service('uri');

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/project_list_booked');
            echo view('templates/footer');
            echo view('task_lead/script');
        } elseif (session('logged_in') == true) {
            $data['title'] = 'Invalid Access';
            $data['page_title'] = 'Invalid Access';
            $data['href'] = site_url('tasklead');
            $data['uri'] = service('uri');

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('templates/offlimits');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function getProjectListManager()
    {
        $taskleadModel = new TaskLeadModel();

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($taskleadModel->noticeTable())
            ->setDefaultOrder("id", "DESC")
            ->setOrder([
                "id",
                null,
                "quarter",
                "employee_name",
                "status_percent",
                "status",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_start_date"
            ])
            ->setSearch([
                "customer_name",
                "contact_number",
                "id",
                "project",
                "project_amount",
                "quotation_num",
                "remark_next_step",
                "status",
                "status1"
            ])
            ->setOutput([
                "id",
                $taskleadModel->buttonEdit(),
                "employee_name",
                "quarter",
                "status",
                "status_percent",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status1",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_duration"
            ]);
        return $taskleadTable->getDatatable();
    }

    public function getProjectList()
    {
        $taskleadModel = new TaskLeadModel();

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($taskleadModel->noticeTableWhere(session('employee_id')))
            ->setDefaultOrder("id", "DESC")
            ->setOrder([
                "id",
                null,
                "quarter",
                "employee_name",
                "status_percent",
                "status",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_start_date"
            ])
            ->setSearch([
                "customer_name",
                "contact_number",
                "id",
                "project",
                "project_amount",
                "quotation_num",
                "remark_next_step",
                "status",
                "status1"
            ])
            ->setOutput([
                "id",
                $taskleadModel->buttonEdit(),
                "employee_name",
                "quarter",
                "status",
                "status_percent",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status1",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_duration"
            ]);
        return $taskleadTable->getDatatable();
    }

    public function getProjectListBookedManager()
    {
        $taskleadModel = new TaskLeadModel();

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($taskleadModel->noticeTableBooked())
            ->setDefaultOrder("id", "DESC")
            ->setOrder([
                "id",
                null,
                "quarter",
                "employee_name",
                "status_percent",
                "status",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_start_date"
            ])
            ->setSearch([
                "customer_name",
                "contact_number",
                "id",
                "project",
                "project_amount",
                "quotation_num",
                "remark_next_step",
                "status",
                "status1"
            ])
            ->setOutput([
                "id",
                $taskleadModel->buttonEdit(),
                "employee_name",
                "quarter",
                "status",
                "status_percent",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status1",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_duration"
            ]);
        return $taskleadTable->getDatatable();
    }

    public function getProjectBookedList()
    {
        $taskleadModel = new TaskLeadModel();

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($taskleadModel->noticeTableBookedWhere(session('employee_id')))
            ->setDefaultOrder("id", "DESC")
            ->setOrder([
                "id",
                null,
                "quarter",
                "employee_name",
                "status_percent",
                "status",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_start_date"
            ])
            ->setSearch([
                "customer_name",
                "contact_number",
                "id",
                "project",
                "project_amount",
                "quotation_num",
                "remark_next_step",
                "status",
                "status1"
            ])
            ->setOutput([
                "id",
                $taskleadModel->buttonEdit(),
                "employee_name",
                "quarter",
                "status",
                "status_percent",
                "customer_name",
                "contact_number",
                "project",
                "project_amount",
                "quotation_num",
                "forecast_close_date",
                "min_forecast_date",
                "max_forecast_date",
                "status1",
                "remark_next_step",
                "close_deal_date",
                "project_start_date",
                "project_finish_date",
                "project_duration"
            ]);
        return $taskleadTable->getDatatable();
    }

    public function edit_project($id)
    {
        if (session('logged_in') == true) {

            $taskleadModel = new TaskLeadModel();
            $customersModel = new CustomersModel();


            $data['title'] = 'Update a project';
            $data['page_title'] = 'Update project';
            $data['customers'] = $customersModel->findAll();
            $data['project_details'] = $taskleadModel->find($id);
            $data['id'] = $id;
            $data['uri'] = service('uri');

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/add_project');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function update_project_status($id, $status)
    {
        if (session('logged_in') == true) {

            $taskleadModel = new TaskLeadModel();
            $taskleadData = $taskleadModel->find($id);
            $data_model = [
                'status' => $status
            ];

            switch ($status) {
                case '10.00':
                    $status_text = "<h1 class='text-danger'>IDENTIFIED (10%)</h1>";
                    $quotation_num = "";
                    break;

                case '30.00':
                    $status_text = "<h1 class='text-secondary'>QUALIFIED (30%)</h1>";
                    $quotation_num = "";
                    break;

                case '50.00':
                    $status_text = "<h1 class='text-warning'>DEVELOPED SOLUTION (50%)</h1>";

                    if ($taskleadData['quotation_num'] == "") {
                        $quotation_num = "QTN" . date('Ymd') . "001";
                        $data_model['quotation_num'] = $quotation_num;
                    } else {
                        $quotation_num = "";
                    }

                    break;

                case '70.00':
                    $status_text = "<h1 class='text-info'>EVALUATION (70%)</h1>";

                    if ($taskleadData['quotation_num'] == "") {
                        $quotation_num = "QTN" . date('Ymd') . "001";
                        $data_model['quotation_num'] = $quotation_num;
                    } else {
                        $quotation_num = "";
                    }

                    break;

                case '90.00':
                    $status_text = "<h1 class='text-primary'>NEGOTIATION (90%)</h1>";

                    if ($taskleadData['quotation_num'] == "") {
                        $quotation_num = "QTN" . date('Ymd') . "001";
                        $data_model['quotation_num'] = $quotation_num;
                    } else {
                        $quotation_num = "";
                    }

                    break;

                default:
                    $status_text = "";
                    $quotation_num = "";
                    break;
            }


            $data['title'] = 'Update Tasklead Status';
            $data['page_title'] = 'Update Tasklead Status';
            $data['uri'] = service('uri');
            $data['href'] = site_url('project-list');
            $data['status'] = $status_text;
            $data['quotation_num'] = $quotation_num;


            $taskleadModel->update($id, $data_model);

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/update_tasklead_successpage');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function booked_status($id)
    {
        if (session('logged_in') == true) {

            $taskleadModel = new TaskLeadModel();
            $taskleadData = $taskleadModel->find($id);

            if ($taskleadData['quotation_num'] == "") {
                $quotation_num = "QTN" . date('Ymd') . "001";
                $status["quotation_num"] = $quotation_num;
                $taskleadModel->update($id, $status);
            } else {
                $quotation_num = "";
            }


            $data['title'] = 'Booked Task Lead';
            $data['page_title'] = 'Book a Task Lead';
            $data['quotation_num'] = $quotation_num;
            $data['uri'] = service('uri');
            $data['id'] = $id;

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/booked_tasklead');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function booked_status_validate()
    {
        $taskleadModel = new TaskLeadModel();
        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $id = $this->request->getPost('id');
        $data = [
            "status" => $this->request->getPost('status'),
            "close_deal_date" => $this->request->getPost('close_deal_date'),
            "project_start_date" => $this->request->getPost('project_start_date'),
            "project_finish_date" => $this->request->getPost('project_finish_date'),
        ];

        if (!$taskleadModel->update($id, $data)) {
            $validate['messages'] = $taskleadModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function delete_tasklead($id)
    {
        if (session('logged_in') == true) {

            $taskleadModel = new TaskLeadModel();

            $data['title'] = 'Delete Task/Lead';
            $data['page_title'] = 'Delete Task/Lead';
            $data['uri'] = service('uri');
            $data['href'] = site_url('project-list');
            $taskleadModel->delete($id);

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('templates/deletepage');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function project_booked_details($id)
    {
        if (session('logged_in') == true) {
            helper('filesystem');
            $taskleadView = new TaskLeadView();
            $data['title'] = 'Project Booked Details';
            $data['uri'] = service('uri');
            $data['project_detail'] = $taskleadView->find($id);
            $data['errors'] = [];

            $path = WRITEPATH . 'uploads/' . $id;

            $data['map'] = directory_map($path);

            echo view('templates/header', $data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/project_booked_details');
            echo view('templates/footer');
            echo view('task_lead/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function upload($id)
    {
        if (session('logged_in') == true) {
            $validationRule = [
                'project_file' => [
                    'label' => 'File',
                    'rules' => 'uploaded[project_file]'
                        . '|max_size[project_file,5000]'
                        . '|ext_in[project_file,xlsx,jpg,jpeg,csv,docx,pdf]'
                ],
            ];
            if (!$this->validate($validationRule)) {
                helper('filesystem');
                $data = ['errors' => $this->validator->getErrors()];
                
                $taskleadView = new TaskLeadView();
                $data['title'] = 'Project Booked Details';
                $data['uri'] = service('uri');
                $data['project_detail'] = $taskleadView->find($id);

                $path = WRITEPATH . 'uploads/' . $id;

                $data['map'] = directory_map($path);

                return view('templates/header', $data)
                    .view('task_lead/header')
                    .view('templates/navbar')
                    .view('templates/sidebar')
                    .view('task_lead/project_booked_details')
                    .view('templates/footer')
                    .view('task_lead/script');
            }

            $img = $this->request->getFile('project_file');

            if (!$img->hasMoved()) {
                $filepath = WRITEPATH . 'uploads/project-booked/' . $img->store($id,$img->getClientName());

                $data = ['uploaded_flleinfo' => new File($filepath)];
                $data['title'] = 'Upload File Success';
                $data['page_title'] = 'File upload success!';
                $data['href'] = site_url('project-list-booked');
                $data['uri'] = service('uri');

                 return view('templates/header', $data)
                 .view('task_lead/header')
                 .view('templates/navbar')
                 .view('templates/sidebar')
                 .view('templates/file-successpage')
                 .view('templates/footer')
                 .view('task_lead/script');
            }


        } else {
            return redirect()->to('login');
        }
    }
}
