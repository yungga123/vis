<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts as ModelsAccounts;
use App\Models\EmployeesModel;
use monken\TablesIgniter;

class Accounts extends BaseController
{
    public function index()
    {
        if (session('logged_in') == true) {

            $employeesModel = new EmployeesModel();
            
            
            $data['title'] = 'Add Account';
            $data['page_title'] = 'Add an account';
            $data['uri'] = service('uri');
            $data['employees'] = $employeesModel->findAll();
            
            return view('accounts/add_account',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function add_account_validate() {
        $accountsModel = new ModelsAccounts();

        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $data = [
            "employee_id" => $this->request->getPost('employee_id'),
            "username" => $this->request->getPost('username'),
            "password" => $this->request->getPost('password'),
            "access_level" => $this->request->getPost('access_level')
        ];

        if (!$accountsModel->insert($data)) {
            $validate['messages'] = $accountsModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function list_account()
    {
        if (session('logged_in') == true) {

            
            
            $data['title'] = 'List of Accounts';
            $data['page_title'] = 'List of Accounts';
            $data['uri'] = service('uri');

            return view('accounts/list_account',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function get_accounts() {
        $accountsModel = new ModelsAccounts();
        $accountsTable = new TablesIgniter();

        $accountsTable->setTable($accountsModel->noticeTable())
                       ->setSearch([
                            "employee_id",
                            "employee_name",
                            "username",
                            "password",
                            "access_level"
                       ])
                       ->setOrder([
                            "employee_id",
                            null,
                            "employee_name",
                            "username",
                            "password",
                            "access_level"
                       ])
                       ->setOutput(
                        [
                            "employee_id",
                            $accountsModel->buttonEdit(),
                            "employee_name",
                            "username",
                            "password",
                            "access_level"
                        ]);

        return $accountsTable->getDatatable();
    }

    public function edit_account($id)
    {
        if (session('logged_in') == true) {

            $employeesModel = new EmployeesModel();
            $accountsModel = new ModelsAccounts();
            
            
            $data['title'] = 'Add Account';
            $data['page_title'] = 'Add an account';
            $data['uri'] = service('uri');
            $data['employees'] = $employeesModel->findAll();
            $data['account_data'] = $accountsModel->find($id);
            $data['id'] = $id;

            echo view('templates/header', $data);
            echo view('accounts/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('accounts/add_account');
            echo view('templates/footer');
            echo view('accounts/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function edit_account_validate() {
        $accountsModel = new ModelsAccounts();

        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $id = $this->request->getPost('id');

        $data = [
            "username" => $this->request->getPost('username'),
            "password" => $this->request->getPost('password'),
            "access_level" => $this->request->getPost('access_level')
        ];

        if (!$accountsModel->update($id,$data)) {
            $validate['messages'] = $accountsModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function delete_account($id) {
        if (session('logged_in') == true) {

            $accountsModel = new ModelsAccounts();
            
            $data['title'] = 'Delete Account';
            $data['page_title'] = 'Delete Account';
            $data['uri'] = service('uri');
            $data['href'] = site_url('list-account');
            $accountsModel->delete($id);

            echo view('templates/header', $data);
            echo view('employees/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('templates/deletepage');
            echo view('templates/footer');
            echo view('employees/script');
        } else {
            return redirect()->to('login');
        }
    }
}
