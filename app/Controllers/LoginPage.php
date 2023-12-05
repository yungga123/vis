<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Models\EmployeeModel;

class LoginPage extends BaseController
{
    public function index()
    {
        $data['title']          = "Welcome to M.I.S.";
        $data['sweetalert2']    = true;

        return view('login', $data);
    }

    public function login()
    {
        $data = [];
        
        try {
            $rules = [
                'username' => 'required|min_length[4]|max_length[20]',
                'password' => 'required|min_length[8]|max_length[20]'
            ];
            
            if ($this->validate($rules)) {
                $accountsModel  = new AccountModel();
                $username       = $this->request->getVar('username');
                $password       = $this->request->getVar('password');

                $data['status']         = res_lang('status.error');
                $data['message']        = 'Wrong username or password. Please try again!';

                if ($user = $accountsModel->authenticate($username, $password)) {
                    $employeesModel = new EmployeeModel();
                    $fields         = '
                        employee_id,
                        firstname,
                        lastname,
                        gender,
                        email_address
                    ';
                    $employee       = $employeesModel->select($fields)
                                        ->where('employee_id', $user['employee_id'])->first();

                    $session = session();
                    $session->set([
                        'logged_in'     => true,
                        'username'      => $username,
                        'access_level'  => $user['access_level'],
                        'access'        => $user['access_level'],
                        'employee_id'   => $employee['employee_id'],
                        'name'          => $employee['firstname'].' '.$employee['lastname'],
                        'gender'        => $employee['gender'],
                        'email_address' => $employee['email_address'],
                        'logged_at'     => date('Y-m-d H:i:s'),
                    ]);

                    $data['status']     = res_lang('status.success');
                    $data['message']    = 'You have successfully logged in!';
                    $data['redirect']   = base_Url('/dashboard');
                }
            } else {
                $data['status']     = res_lang('status.error');
                $data['message']    = res_lang('error.validation');
                $data['errors']     = $this->validator->getErrors();
            }
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = res_lang('status.error');
            $data ['message']   = res_lang('error.process');
        }

        return $this->response->setJSON($data); 
    }

    public function logout()
    {
        $session = session();

        $session->destroy();

        return redirect()->to('');
    }
}
