<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskLeadModel;

class TaskLead extends BaseController
{
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
            $data['title'] = 'Add Project';

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
}
