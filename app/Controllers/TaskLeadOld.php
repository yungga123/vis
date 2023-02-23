<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomersModel;
use App\Models\CustomersVtBranchModel;
use App\Models\CustomersVtModel;
use App\Models\TaskleadHistorViewModel;
use App\Models\TaskleadHistoryModel;
use App\Models\TaskLeadModel;
use App\Models\TaskLeadView;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\I18n\Time;
use Config\Services;
use monken\TablesIgniter;

class TaskLead extends BaseController
{

    use ResponseTrait;
    public function index()
    {
        if (session('logged_in') == true) {

            $customersModel = new CustomersModel();
            $customersVtModel = new CustomersVtModel();
            $data['title'] = 'Task/Leads Monitoring';
            $data['page_title'] = 'Task/Leads Monitoring';
            $data['uri'] = service('uri');
            $data['customers'] = $customersModel->find();
            $data['customersVt'] = $customersVtModel->find();

            return view('task_lead/task_lead_menu',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function add_project()
    {
        if (session('logged_in') == true) {
            $time = new Time('now');

            $selectedCustomer = $this->request->getGet('forecast_custmer');
            $customersModel = new CustomersModel();
            $customersBranchModel = new CustomerBranchModel();
            $data['title'] = 'Add Project';
            $data['page_title'] = 'Add Project';
            $data['customers'] = $customersModel->findAll();
            $data['uri'] = service('uri');
            $data['date_quarter'] = $time->getQuarter();
            $data['customers_branch'] = $customersBranchModel->where('customer_id',$selectedCustomer)->find();
            $data['selectedCustomer'] = $selectedCustomer;


            return view('task_lead/add_project',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function add_projectExistingCustomer()
    {
        if (session('logged_in') == true) {

            if ($this->request->getMethod()=="post") {
                $taskleadModel = new TaskLeadModel();
                $taskleadHistoryModel = new TaskleadHistoryModel();
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
                    "branch_id" => $this->request->getPost('branch_id'),
                    "project" => $this->request->getPost('project'),
                    "project_amount" => $this->request->getPost('project_amount'),
                    "remark_next_step" => $this->request->getPost('remark_next_step'),
                    "forecast_close_date" => $this->request->getPost('forecast_close_date'),
                    "min_forecast_date" => $this->request->getPost('min_forecast_date'),
                    "max_forecast_date" => $this->request->getPost('max_forecast_date'),
                    "existing_customer" => 1,
                ];

                if (!$taskleadModel->insert($data)) {
                    $validate['messages'] = $taskleadModel->errors();
                } else {
                    $validate['success'] = true;
                    $db = \Config\Database::connect();
                    $query = $db->query("SELECT * FROM tasklead ORDER BY id DESC LIMIT 1");
                    $result = $query->getResultObject();
                    

                    $taskleadHistoryModel->insert([
                        "tasklead_id" => $result[0]->id,
                        "quarter" => $data["quarter"],
                        "status" => $data["status"],
                        "remark_next_step" => $data["remark_next_step"],
                    ]);
                }

                return json_encode($validate);
            }


            $time = new Time('now');
            $selectedCustomer = $this->request->getGet('existing_customer');
            $customersVtModel = new CustomersVtModel();
            $customersVtBranchModel = new CustomersVtBranchModel();
            $data['title'] = 'Add Project';
            $data['page_title'] = 'Add Project';
            $data['customers'] = $customersVtModel->findAll();
            $data['uri'] = service('uri');
            $data['date_quarter'] = $time->getQuarter();
            $data['customers_branch'] = $customersVtBranchModel->where('customer_id',$selectedCustomer)->find();
            $data['selectedCustomer'] = $selectedCustomer;


            return view('task_lead/add_project',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function add_project_validate()
    {
        $taskleadModel = new TaskLeadModel();
        $taskleadHistoryModel = new TaskleadHistoryModel();
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
            "branch_id" => $this->request->getPost('branch_id'),
            "project" => $this->request->getPost('project'),
            "project_amount" => $this->request->getPost('project_amount'),
            "remark_next_step" => $this->request->getPost('remark_next_step'),
            "forecast_close_date" => $this->request->getPost('forecast_close_date'),
            "min_forecast_date" => $this->request->getPost('min_forecast_date'),
            "max_forecast_date" => $this->request->getPost('max_forecast_date'),
            "existing_customer" => 0,

        ];

        if (!$taskleadModel->insert($data)) {
            $validate['messages'] = $taskleadModel->errors();
        } else {
            $validate['success'] = true;
            $db = \Config\Database::connect();
            $query = $db->query("SELECT * FROM tasklead ORDER BY id DESC LIMIT 1");
            $result = $query->getResultObject();
            

            $taskleadHistoryModel->insert([
                "tasklead_id" => $result[0]->id,
                "quarter" => $data["quarter"],
                "status" => $data["status"],
                "remark_next_step" => $data["remark_next_step"],
            ]);
        }

        echo json_encode($validate);
    }

    public function edit_project_validate()
    {
        $taskleadModel = new TaskLeadModel();
        $taskleadHistoryModel = new TaskleadHistoryModel();
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
            "branch_id" => $this->request->getPost('branch_id'),
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
            $data['page_title'] = 'Project List';
            $data['uri'] = service('uri');

            return view('task_lead/project_list',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function manager_project_list()
    {

        if (session('logged_in') == true && (session('access') == 'manager' || session('access') == 'admin')) {
            $data['title'] = 'Managers Project List';
            $data['page_title'] = 'Managers Project List';
            $data['uri'] = service('uri');

            return view('task_lead/project_list',$data);
        } elseif (session('logged_in') == true) {
            $data['title'] = 'Invalid Access';
            $data['page_title'] = 'Invalid Access';
            $data['href'] = site_url('tasklead');
            $data['uri'] = service('uri');


            return view('templates/offlimits',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function project_list_booked()
    {
        if (session('logged_in') == true) {
            //$taskleadModel = new TaskLeadModel();
            $taskleadView = new TaskLeadView();

            $request = service('request');
            $searchData = $request->getGet();

            $data['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
            $data['perPage'] = 10;
            $data['total'] = $taskleadView->countAll();
            $search = "";
            if (isset($searchData) && isset($searchData['search'])) {
                $search = $searchData['search'];
            }

            if ($search == '') {
                $paginateData = $taskleadView->where('employee_name',session('name'))->orderBy('id', 'desc')->paginate($data['perPage']);
            } else {
                $paginateData = $taskleadView->orLike('id', $search)
                    ->orLike('employee_name', $search)
                    ->orLike('customer_name', $search)
                    ->orderBy('id', 'desc')
                    ->paginate($data['perPage']);
            }

            $data['title'] = 'Booked Project List';
            $data['page_title'] = 'Booked Project List';
            $data['uri'] = service('uri');
            $data['booked_projects'] = $paginateData;
            $data['pager'] = $taskleadView->pager;
            $data['search'] = $search;

            return view('task_lead/project_list_booked',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function manager_project_list_booked()
    {

        if (session('logged_in') == true && (session('access') == 'manager' || session('access') == 'admin')) {

            $taskleadView = new TaskLeadView();

            $request = service('request');
            $searchData = $request->getGet();

            $data['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
            $data['perPage'] = 10;
            $data['total'] = $taskleadView->countAll();
            $search = "";
            if (isset($searchData) && isset($searchData['search'])) {
                $search = $searchData['search'];
            }

            if ($search == '') {
                $paginateData = $taskleadView->orderBy('id', 'desc')->paginate($data['perPage']);
            } else {
                $paginateData = $taskleadView->orLike('id', $search)
                    ->orLike('employee_name', $search)
                    ->orLike('customer_name', $search)
                    ->orderBy('id', 'desc')
                    ->paginate($data['perPage']);
            }

            $data['title'] = 'Booked Managers Project List';
            $data['page_title'] = 'Booked Managers Project List';
            $data['uri'] = service('uri');
            $data['booked_projects'] = $paginateData;
            $data['pager'] = $taskleadView->pager;
            $data['search'] = $search;

            return view('task_lead/project_list_booked',$data);
        } elseif (session('logged_in') == true) {
            $data['title'] = 'Invalid Access';
            $data['page_title'] = 'Invalid Access';
            $data['href'] = site_url('tasklead');
            $data['uri'] = service('uri');

            return view('templates/offlimits',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function getProjectListManager()
    {
        $taskleadModel = new TaskLeadModel();

        if ($this->request->getGet('existing_customer')==1) {
            $project_table = $taskleadModel->noticeTableExistingCustomer();
        } else {
            $project_table = $taskleadModel->noticeTable();
        }

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($project_table)
            ->setDefaultOrder("id", "DESC")
            ->setOrder([
                "id",
                null,
                "quarter",
                "employee_name",
                "status_percent",
                "status",
                "customer_name",
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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

        if ($this->request->getGet('existing_customer')==1) {
            $project_table = $taskleadModel->noticeTableWhereExistingCustomer(session('employee_id'));
        } else {
            $project_table = $taskleadModel->noticeTableWhere(session('employee_id'));
        }

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($project_table)
            ->setDefaultOrder("id", "DESC")
            ->setOrder([
                "id",
                null,
                "quarter",
                "employee_name",
                "status_percent",
                "status",
                "customer_name",
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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
                "branch_name",
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

    // public function edit_project($id)
    // {
    //     if (session('logged_in') == true) {

    //         $taskleadModel = new TaskLeadModel();
    //         $customersModel = new CustomersModel();
    //         $customersBranchModel = new CustomerBranchModel();

    //         $customersVtModel = new CustomersVtModel();
    //         $customersVtBranchModel = new CustomersVtBranchModel();

    //         $project_details = $taskleadModel->find($id);


    //         $data['title'] = 'Update a project';
    //         $data['page_title'] = 'Update project';

    //         if ($project_details['existing_customer']) {
    //             $data['customers'] = $customersVtModel->findAll();
    //             $data['customers_branch'] = $customersVtBranchModel->where('customer_id',$project_details['customer_id'])->find();
    //         } else {
    //             $data['customers'] = $customersModel->findAll();
    //             $data['customers_branch'] = $customersBranchModel->where('customer_id',$project_details['customer_id'])->find();
    //         }
            
            
    //         $data['project_details'] = $project_details;
    //         $data['id'] = $id;
    //         $data['uri'] = service('uri');

    //         return view('task_lead/add_project',$data);
    //     } else {
    //         return redirect()->to('login');
    //     }
    // }

    public function update_project_status($id, $status)
    {
        if (session('logged_in') == true) {

            $taskleadModel = new TaskLeadModel();
            $taskleadHistoryModel = new TaskleadHistoryModel();
            $taskleadData = $taskleadModel->find($id);

            $data['title'] = 'Update Tasklead Status';
            $data['page_title'] = 'Update Tasklead Status';
            $data['uri'] = service('uri');
            $data['href'] = site_url('project-list');
            $data['taskleadData'] = $taskleadData;
            

            $data_model = [
                'status' => $status
            ];


            switch ($status) {

                case '30.00': // QUALIFiED
                    
                    $status_text = "<h1 class='text-secondary'>QUALIFIED (30%)</h1>";
                    $quotation_num = "";

                    $data['status'] = $status;
                    $data['status_text'] = $status_text;
                    $data['id'] = $id;
                    $data['quotation_num'] = $quotation_num;

                    

                    return view('task_lead/booked_tasklead',$data);
                    break;

                case '50.00': // DEVELOPED SOLUTION
                    $status_text = "<h1 class='text-warning'>DEVELOPED SOLUTION (50%)</h1>";

                    $name = session('name');
                    $pos = strpos($name," ") + 1;
                    $quotation_num = "QTN" . $name[0].$name[$pos] . date('ym') . $id;

                    $data['status'] = $status;
                    $data['status_text'] = $status_text;
                    $data['id'] = $id;
                    $data['quotation_num'] = $quotation_num;

                    

                    return view('task_lead/booked_tasklead',$data);

                    break;

                case '70.00': // EVALUATION
                    $status_text = "<h1 class='text-info'>EVALUATION (70%)</h1>";

                    $name = session('name');
                    $pos = strpos($name," ") + 1;
                    $quotation_num = "QTN" . $name[0].$name[$pos] . date('ym') . $id;

                    $data['status'] = $status;
                    $data['status_text'] = $status_text;
                    $data['id'] = $id;
                    $data['quotation_num'] = $quotation_num;

                    

                    return view('task_lead/booked_tasklead',$data);

                    break;

                case '90.00': // NEGOTIATION
                    $status_text = "<h1 class='text-primary'>NEGOTIATION (90%)</h1>";

                    $name = session('name');
                    $pos = strpos($name," ") + 1;
                    $quotation_num = "QTN" . $name[0].$name[$pos] . date('ym') . $id;

                    $data['status'] = $status;
                    $data['status_text'] = $status_text;
                    $data['id'] = $id;
                    $data['quotation_num'] = $quotation_num;

                    

                    return view('task_lead/booked_tasklead',$data);

                    break;

                case '100.00':
                    $status_text = "<h1 class='text-success'>BOOKED (100%)</h1>";

                    $name = session('name');
                    $pos = strpos($name," ") + 1;
                    $quotation_num = "QTN" . $name[0].$name[$pos] . date('ym') . $id;

                    $data['status'] = $status;
                    $data['status_text'] = $status_text;
                    $data['id'] = $id;
                    $data['quotation_num'] = $quotation_num;

                    

                    return view('task_lead/booked_tasklead',$data);


                    break;

                default:
                    $status_text = "";
                    $quotation_num = "";

                    
                    break;
            }


        } else {
            return redirect()->to('login');
        }
    }

    public function update_project_status_validate()
    {
        $taskleadModel = new TaskLeadModel();
        $taskleadHistoryModel = new TaskleadHistoryModel();
        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $id = $this->request->getPost('id');
        $data = [
            "quotation_num" => $this->request->getPost('quotation_num'),//
            "status" => $this->request->getPost('status'),//
            "project" => $this->request->getPost('project'), //
            "project_amount" => $this->request->getPost('project_amount'),//
            "remark_next_step" => $this->request->getPost('remark_next_step'), //
            "forecast_close_date" => $this->request->getPost('forecast_close_date'),//
            "min_forecast_date" => $this->request->getPost('min_forecast_date'),//
            "max_forecast_date" => $this->request->getPost('max_forecast_date'),//
            "close_deal_date" => $this->request->getPost('close_deal_date'),//
            "project_start_date" => $this->request->getPost('project_start_date'),//
            "project_finish_date" => $this->request->getPost('project_finish_date'),//
        ];

        $insert_data = [
            "tasklead_id" => $id,
            "quotation_num" => $this->request->getPost('quotation_num'),//
            "status" => $this->request->getPost('status'),//
            "project" => $this->request->getPost('project'), //
            "project_amount" => $this->request->getPost('project_amount'),//
            "remark_next_step" => $this->request->getPost('remark_next_step'), //
            "forecast_close_date" => $this->request->getPost('forecast_close_date'),//
            "min_forecast_date" => $this->request->getPost('min_forecast_date'),//
            "max_forecast_date" => $this->request->getPost('max_forecast_date'),//
            "close_deal_date" => $this->request->getPost('close_deal_date'),//
            "project_start_date" => $this->request->getPost('project_start_date'),//
            "project_finish_date" => $this->request->getPost('project_finish_date'),//
        ];

        if (!$taskleadModel->update($id, $data)) {
            $validate['messages'] = $taskleadModel->errors();
        } else {
            $validate['success'] = true;
            $taskleadHistoryModel->insert($insert_data);

            $existingCustomer = $this->request->getPost('existing_customer');

            if ($data['status']=='100.00' && !$existingCustomer) {
                
                //ADD MAIN CUSTOMER

                $customersModel = new CustomersModel();
                $customersVtModel = new CustomersVtModel();
                

                $taskleadData = $taskleadModel->find($id);
                $customerID = $taskleadData['customer_id'];
                $customersData = $customersModel->find($customerID);

                $customersVTInsert = [
                    "customer_name" => $customersData['customer_name'],
                    "contact_person" => $customersData['contact_person'],
                    "address_province" => $customersData['address_province'],
                    "address_city" => $customersData['address_city'],
                    "address_brgy" => $customersData['address_brgy'],
                    "address_sub" => $customersData['address_sub'],
                    "contact_number" => $customersData['contact_number'],
                    "email_address" => $customersData['email_address'],
                    "source" => $customersData['source'],
                    "notes" => $customersData['notes'],
                ];
                
                $customersVtModel->insert($customersVTInsert);
                

                $customersVtLastData = $customersVtModel->orderBy('id','desc')->limit(1)->find();
                $taskleadModel->update($id,[
                    "customer_id" => $customersVtLastData[0]['id']
                ]);
                $customersModel->delete($customerID);

                 // ADD CUSTOMER BRANCH
                if ($taskleadData['branch_id']) {
                    $customersBranchModel = new CustomerBranchModel();
                    $customersVTBranchModel = new CustomersVtBranchModel();
                    $customerBranchID = $taskleadData['branch_id'];
                    $customersBranchData = $customersBranchModel->find($customerBranchID);
                    $customersVTBranchInsert = [
                        "customer_id" => $customersBranchData['customer_id'],
                        "branch_name" => $customersBranchData['branch_name'],
                        "address_province" => $customersBranchData['address_province'],
                        "address_city" => $customersBranchData['address_city'],
                        "address_brgy" => $customersBranchData['address_brgy'],
                        "address_sub" => $customersBranchData['address_sub'],
                        "contact_number" => $customersBranchData['contact_number'],
                        "contact_person" => $customersBranchData['contact_person'],
                        "email_address" => $customersBranchData['email_address'],
                        "notes" => $customersBranchData['notes'],
                    ];
                    $customersVTBranchModel->insert($customersVTBranchInsert);
                    
                    $customersVtBranchLastData = $customersVTBranchModel->orderBy('id', 'desc')->limit(1)->find();
                    $taskleadModel->update($id, [
                        "branch_id" => $customersVtBranchLastData[0]['id']
                    ]);
                    $customersVTBranchModel->delete($customerBranchID);
                }

            }
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
            $taskleadModel = new TaskLeadModel();
            $taskleadView = new TaskLeadView();
            $taskleadHistoryViewModel = new TaskleadHistorViewModel();
            $data['title'] = 'Project Booked Details';
            $data['page_title'] = 'Project Booked Details';
            $data['uri'] = service('uri');
            $data['project_detail'] = $taskleadView->find($id);
            $data['errors'] = [];
            $data['id'] = $id;
            $data['tasklead_history'] = $taskleadHistoryViewModel->where('tasklead_id', $id)->findAll();
            $data['tasklead_data'] = $taskleadModel->find($id);

            $path = '../public/uploads/project-booked/' . $id;

            $data['map'] = directory_map($path);

            return view('task_lead/project_booked_details',$data);
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

                $path = '../public/uploads/project-booked/' . $id;

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
                //$filepath = WRITEPATH . 'uploads/' . $img->store($id,$img->getClientName());
                $filename = $img->getClientName();
                $filepath = '../public/uploads/project-booked/'.$id;
                $img->move($filepath,$filename);

                $data = ['uploaded_flleinfo' => new File($filepath)];
                $data['title'] = 'Upload File Success';
                $data['page_title'] = 'File upload success!';
                $data['href'] = site_url('project-list-booked');
                $data['uri'] = service('uri');
                $data['id'] = $id;

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
