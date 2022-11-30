<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\DataTable;
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
            $data['customers'] = $customersModel->findAll();
            

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
            "quotation_num" => $this->request->getPost('quotation_num'),
            "quarter" => $this->request->getPost('quarter'),
            "status" => $this->request->getPost('status'),
            "customer_id" => $this->request->getPost('customer_id'),
            "project" => $this->request->getPost('project'),
            "project_amount" => $this->request->getPost('project_amount'),
            "remark_next_step" => $this->request->getPost('remark_next_step')
        ];

        if (!$taskleadModel->insert($data)) {
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

    // public function getProjectList()
	// {
	// 	$dataTable = new DataTable;
	// 	$response = $dataTable->process('TaskLeadModel', [
    //         [
	// 			'name' => 'id'
	// 		],
    //         [
	// 			'name' => 'quarter'
	// 		],
    //         [
	// 			'name' => 'status'
	// 		],
    //         [
	// 			'name' => 'status',
    //             'formatter' => 'status_percent'
	// 		],
    //         [
	// 			'name' => 'customer_id',
    //             'formatter' => 'customers_name'
	// 		],
    //         [
	// 			'name' => 'customer_id',
    //             'formatter' => 'customers_name'
	// 		],
    //         [
	// 			'name' => 'project'
	// 		],
    //         [
	// 			'name' => 'project_amount'
	// 		],
    //         [
	// 			'name' => 'quotation_num'
	// 		],
    //         [
	// 			'name' => 'forecast_close_date'
	// 		],
    //         [
	// 			'name' => 'status'
	// 		],
    //         [
	// 			'name' => 'remark_next_step'
	// 		],
    //         [
	// 			'name' => 'close_deal_date'
	// 		],
    //         [
	// 			'name' => 'project_start_date'
	// 		],
    //         [
	// 			'name' => 'project_finish_date'
	// 		],
    //         [
	// 			'name' => 'project_start_date'
	// 		]
	// 	]);

		
	// 	return $this->setResponseFormat('json')->respond($response);
	// }

    public function getProjectList()
	{
		$taskleadModel = new TaskLeadModel();

        $taskleadTable = new TablesIgniter();
        $taskleadTable->setTable($taskleadModel->noticeTable())
          ->setOutput(["tasklead_id","quarter",
            "status_percent",
            "status",
            "customer_name",
            "contact_number",
            "project",
            "project_amount",
            "quotation_num",
            "forecast_close_date",
            "hit",
            "remark_next_step",
            "close_deal_date",
            "project_start_date",
            "project_finish_date",
            "project_duration"
        ]);
        return $taskleadTable->getDatatable();
	}
}
