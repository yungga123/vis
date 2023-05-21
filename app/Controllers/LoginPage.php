<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts as AccountsModel;
use App\Models\EmployeesModel;

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
                $accountsModel  = new AccountsModel();
                $username       = $this->request->getVar('username');
                $password       = $this->request->getVar('password');

                $data['status']         = STATUS_ERROR;
                $data['message']        = 'Wrong username or password. Please try again!';

                if ($user = $accountsModel->authenticate($username, $password)) {
                    $employeesModel = new EmployeesModel();
                    $employee = $employeesModel->where('employee_id', $user['employee_id'])->first();

                    $session = session();
                    $session->set([
                        'logged_in'     => true,
                        'username'      => $username,
                        'access_level'  => $user['access_level'],
                        'access'        => $user['access_level'],
                        'employee_id'   => $employee['employee_id'],
                        'name'          => $employee['firstname'].' '.$employee['lastname'],
                        'gender'        => $employee['gender'],
                        'logged_at'     => date('Y-m-d H:i:s'),
                    ]);

                    $data['status']     = STATUS_SUCCESS;
                    $data['message']    = 'You have successfully logged in!';
                    $data['redirect']   = base_Url('/dashboard');
                }
            } else {
                $data['status']     = STATUS_ERROR;
                $data['message']    = 'Validation error!';
                $data['errors']     = $this->validator->getErrors();
            }
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status'] = STATUS_ERROR;
            // $data['errors']     = $e->getMessage();
            $data ['message']   = 'Error while processing data! Please contact your system administrator.';
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
