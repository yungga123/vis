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
        $employeesModel = new EmployeesModel();
            
        $data['title']      = 'Add Account';
        $data['page_title'] = 'Add an account';
        $data['uri']        = service('uri');
        $data['employees']  = $employeesModel->findAll();
        $data['custom_js']      = 'accounts/form.js';
        
        return view('accounts/add_account', $data);
    }

    public function add_account_validate() 
    {
        $accountsModel = new ModelsAccounts();

        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $password       = $this->request->getPost('password');
        $password_hash  = password_hash($password, PASSWORD_BCRYPT);
        $rules          = $accountsModel->getValidationRules(['except' => ['password', 'employee_id']]);
        $rules['employee_id']   = 'required';
        $rules['password']      = 'permit_empty|min_length[8]|alpha_numeric';

        if ($this->validate($rules)) {
            $checkAcct  = $this->_checkAccount($this->request->getPost(), false);

            if (! $checkAcct) {
                $data = [
                    "employee_id" => $this->request->getPost('employee_id'),
                    "username" => $this->request->getPost('username'),
                    "password" => $password_hash,
                    "access_level" => $this->request->getPost('access_level')
                ];
    
                $accountsModel->protect(false);
                $accountsModel->cleanRules(true);
                $accountsModel->skipValidation(true);
    
                if (! $accountsModel->insert($data)) {
                    $validate['messages'] = $accountsModel->errors();
                } else {
                    $validate['success'] = true;
                }
    
                // Turn protection on
                $accountsModel->protect(true);
            } else {
                $validate['messages'] = 'Employee has already an account for this username and access level!';
            }
        } else {
            $validate['messages'] = $this->validator->getErrors();
        }

        echo json_encode($validate);
    }

    public function list_account()
    {
        $data['title']          = 'List of Accounts';
        $data['page_title']     = 'List of Accounts';
        $data['uri']            = service('uri');
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        // $data['with_pdfmake']   = true;
        $data['custom_js']      = 'accounts/list.js';

        return view('accounts/list_account', $data);
    }

    public function get_accounts() 
    {
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
        $employeesModel = new EmployeesModel();
        $accountsModel = new ModelsAccounts();

        if ($account = $accountsModel->find($id)) {
            $data['title']          = 'Edit Account';
            $data['page_title']     = 'Edit account';
            $data['uri']            = service('uri');
            $data['employees']      = $employeesModel->findAll();
            $data['account_data']   = $account;
            $data['id']             = $id;
            $data['custom_js']      = 'accounts/form.js';
            
            return view('accounts/add_account', $data);
        } else {
            $redirect = site_url('add-account');
            echo "
                <script>
                    alert('Could not find account with the id `{$id}`! Click `OK` to redirect to `Add Account` page!');
                    window.location.href = '{$redirect}';
                </script>
            ";
        }
    }

    public function edit_account_validate() 
    {
        $accountsModel = new ModelsAccounts();
        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $id         = $this->request->getPost('id');
        $username   = $this->request->getPost('username');
        $rules      = $accountsModel->getValidationRules(['except' => ['password', 'employee_id']]);
        $rules['password'] = 'permit_empty|min_length[8]|alpha_numeric';

        if ($this->request->getPost('prev_username') === $username) {
            $rules['username'] = 'required|min_length[4]|alpha_numeric';
            $username = '';
        }

        if ($this->validate($rules)) {
            $password   = $this->request->getPost('password');
            $data       = ["access_level" => $this->request->getPost('access_level')];
            $checkAcct  = $this->_checkAccount($this->request->getPost(), false);
            
            if (! $checkAcct) {
                if (! empty($username)) $data['username'] = $username;
                if (! empty($password)) $data['password'] = password_hash($password, PASSWORD_BCRYPT);

                // Turn protection off - to skip validation
                $accountsModel->protect(false);
                $accountsModel->cleanRules(true);
                $accountsModel->skipValidation(true);
                
                if (! $accountsModel->update($id, $data)) {
                    $validate['messages'] = $accountsModel->errors();
                } else {
                    $validate['success'] = true;
                }

                // Turn protection on
                $accountsModel->protect(true);
            } else {
                $validate['messages'] = 'Employee has already an account for the selected access level!';
            }
        } else {
            $validate['messages'] = $this->validator->getErrors();
        }

        echo json_encode($validate);
    }

    public function delete_account($id) 
    {
        $accountsModel = new ModelsAccounts();

        $validate = [
            "success" => false,
            "messages" => 'Account has been deleted!'
        ];

        if (! $accountsModel->delete($id)) {
            $validate['messages'] = $accountsModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    private function _checkAccount(array $params, bool $with_username = true)
    {
        $accountsModel = new ModelsAccounts();
        $employee_id = isset($params['employee_id']) ? $params['employee_id'] : session('employee_id');
        $checkAcct  = $accountsModel->where('employee_id', $employee_id)
                            ->where('access_level', $params['access_level']);

        if ($with_username) $checkAcct->where('username', $params['username']);

        return $checkAcct->first();
    }

}
