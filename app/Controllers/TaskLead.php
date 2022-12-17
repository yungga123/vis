<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersModel;
use App\Models\TaskLeadModel;
use CodeIgniter\API\ResponseTrait;
use monken\TablesIgniter;

class TaskLead extends BaseController
{

    use ResponseTrait;
    public function index()
    {
        if (session('logged_in')==true)
        {
            $data['title'] = 'Task/Leads Monitoring';
            $data['uri'] = service('uri');

            echo view('templates/header',$data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/task_lead_menu');
            echo view('templates/footer');
            echo view('task_lead/script');
        }
        else
        {
            return redirect()->to('login');
        }
    }

    public function add_project()
    {
        if (session('logged_in')==true)
        {   
            $customersModel = new CustomersModel();
            $data['title'] = 'Add Project';
            $data['page_title'] = 'Add Project';
            $data['customers'] = $customersModel->findAll();
            $data['uri'] = service('uri');
            

            echo view('templates/header',$data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/add_project');
            echo view('templates/footer');
            echo view('task_lead/script');
        }
        else
        {
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
            "close_deal_date" => $this->request->getPost('close_deal_date'),
            "project_start_date" => $this->request->getPost('project_start_date'),
            "project_finish_date" => $this->request->getPost('project_finish_date'),
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
            "close_deal_date" => $this->request->getPost('close_deal_date'),
            "project_start_date" => $this->request->getPost('project_start_date'),
            "project_finish_date" => $this->request->getPost('project_finish_date'),
        ];

        if (!$taskleadModel->update($id,$data)) {
            $validate['messages'] = $taskleadModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function project_list()
    {
        if (session('logged_in')==true)
        {   
            $data['title'] = 'Project List';
            $data['uri'] = service('uri');
            
            echo view('templates/header',$data);
            echo view('task_lead/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('task_lead/project_list');
            echo view('templates/footer');
            echo view('task_lead/script');
        }
        else
        {
            return redirect()->to('login');
        }
    }


    public function getProjectList()
	{
		$taskleadModel = new TaskLeadModel();

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($taskleadModel->noticeTable())
            ->setDefaultOrder("id","DESC")
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

    public function delete_tasklead($id) {
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
}
