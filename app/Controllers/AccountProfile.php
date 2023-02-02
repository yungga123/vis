<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts as ModelsAccounts;

class AccountProfile extends BaseController
{
    public function index()
    {
        $data['title']      = 'Account Profile';
        $data['page_title'] = 'Account Profile';
        $data['custom_js']  = 'accounts/profile.js';
        $data['account']       = $this->_get_account_details();

        return view('accounts/profile', $data);
    }

    public function change_password()
    {
        $data = [];
        
        // Using DB Transaction
        $this->transBegin();

        try {            
            if (! $this->validate($this->_rules())) {
                $data['status']     = STATUS_ERROR;
                $data ['errors']    = $this->validator->getErrors();
                $data ['message']   = 'Validation error!';
            } else {
                $model          = new ModelsAccounts();
                $username       = session()->get('username');
                $curr_password  = $this->request->getVar('current_password');

                $data['status']     = STATUS_ERROR;
                $data['message']    = "Wrong current password! Please try again.";

                if ($account = $model->authenticate($username, $curr_password)) {
                    $new_password = $this->request->getVar('password');
                    $hash_password = password_hash(
                        $new_password, 
                        PASSWORD_DEFAULT
                    );
    
                    $data['status']     = STATUS_SUCCESS;
                    $data['message']    = "You have successfully changed you password! You will be logged out now...";

                    // Turn protection off - to skip validation
                    $model->protect(false);
                    
                    $model->where('username', $username)
                        ->set(['password' => $hash_password])
                        ->update();

                    // Turn protection on
                    $model->protect(true);

                    if (! empty($new_password)) {
                        // Send mail to employee
                        $res = $this->sendMail($this->request->getVar(), 'regular');
                        $msg = $res['message'];

                        if($res['status'] === STATUS_SUCCESS) {
                            $msg = $data['message'] . $msg;
                        }

                        $data['status'] = $res['status'];
                        $data['message'] = $msg;
                    }
                }
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            // $data['errors']     = $e->getMessage();
            $data ['message']   = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data); 
    }

    private function _rules()
    {
        $rules = [
            'current_password' => [
                'label' => 'current password',
                'rules' => 'required|min_length[8]|max_length[20]'
            ],
            'password' => [
                'label' => 'new password',
                'rules' => 'required|alpha_numeric|min_length[8]|max_length[20]|differs[current_password]'
                // 'rules' => 'required|alpha_numeric_punct|min_length[8]|max_length[20]'
            ],
            'confirm_password' => [
                'label' => 'confirm password',
                'rules' => 'required_with[password]|matches[password]'
            ]
        ];

        return $rules;
    }

    private function _get_account_details()
    {
        $table = 'employees_view';
        $fields = '
            employee_name,
            position,
            gender,
            civil_status,
            date_of_birth,
            address,
            contact_number,
            email_address
        ';

        $db = $this->builder;
        $query = $db->table($table)->select($fields)
                    ->where('employee_id', session()->get('employee_id'))
                    ->get();

        $account = $query->getRowArray();
        $account['avatar'] = get_avatar(strtolower($account['gender']));

        return $account;
    }
}
