<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Accounts as AccountModel;
use App\Models\EmployeesModel;
use monken\TablesIgniter;

class Accounts extends BaseController
{
    /**
     * Display the account view
     *
     * @return view
     */
    public function index()
    {
        $employeeModel  = new EmployeesModel();
        $access_level   = account_access_level();
        $fields         = 'employee_id, lastname, firstname';
        $employees      = $employeeModel->select($fields)->findAll();
    
        if (session('access_level') !== AAL_ADMIN) {
            unset($access_level[AAL_ADMIN]);
        }

        $data['title']          = 'List of Accounts';
        $data['page_title']     = 'List of Accounts';
        $data['custom_js']      = 'accounts/list.js';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['can_add']        = true;
        $data['employees']      = $employees;
        $data['access_level']   = $access_level;

        return view('accounts/index', $data);
    }

    /**
     * Get list of accounts
     *
     * @return array|dataTable
     */
    public function list()
    {
        $model = new AccountModel();
        $table = new TablesIgniter();

        $table->setTable($model->noticeTable())
            ->setSearch([
                'employee_id',
                'employee_name',
                'username',
                // 'password',
                'access_level',
            ])
            ->setOrder([
                null,
                'employee_id',
                'employee_name',
                'username',
                // 'password',
                'access_level',
            ])
            ->setOutput([
                $model->buttons(),
                'employee_id',
                'employee_name',
                'username',
                // 'password',
                $model->dtAccessLevel(),
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of accounts (inserting and updating accounts)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Account has been added successfully!'
        ];

        // Check if id field has value, then this is an update
        if (! empty($this->request->getVar('id'))) {
            return $this->_update();
        }

        // Using DB Transaction
        $this->transBegin();

        try {
            $model          = new AccountModel();
            $password       = $this->request->getVar('password');
            $password_hash  = password_hash($password, PASSWORD_BCRYPT);
            $rules          = $model->getValidationRules(['except' => ['password', 'employee_id']]);
            $rule_msg       = $model->getValidationMessages();

            $rules['employee_id']   = 'required';
            $rules['password']      = 'required|min_length[8]|alpha_numeric';

            if ($this->validate($rules, $rule_msg)) {
                $checkAcct = $this->_checkAccount($this->request->getVar(), false);

                if (! $checkAcct) {
                    $params = [
                        'employee_id'   => $this->request->getVar('employee_id'),
                        'username'      => $this->request->getVar('username'),
                        'password'      => $password_hash,
                        'access_level'  => $this->request->getVar('access_level'),
                    ];

                    // Turn protection off - to skip validation
                    $model->protect(false);
                    $model->cleanRules(true);
                    $model->skipValidation(true);
                    // Insert account
                    $model->insert($params);
                    // Turn protection on
                    $model->protect(true);
                    // Send mail
                    $data = $this->_sendMail($data);
                } else {
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = 'Employee has already an account for the selected access level!';
                }
            } else {
                $data['status']     = STATUS_ERROR;
                $data['message']    = 'Validation error!';
                $data['errors']     = $this->validator->getErrors();
            }

            // Commit transaction
            $this->transCommit();
        } catch (Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * For getting the account data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Account has been retrieved!'
        ];

        try {
            $model  = new AccountModel();
            $id     = $this->request->getVar('id');
            $fields = 'employee_id, username, access_level';
            // $fields = $model->allowedFields;

            $data['data'] = $model->select($fields)->find($id);;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Deletion of account
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Account has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new AccountModel();

            if (! $model->delete($this->request->getVar('id'))) {
                $data['errors']     = $model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            // Commit transaction
            $this->transCommit();
        } catch (Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Updaing of account
     *
     * @return json
     */
    private function _update()
    {
        $data = [
            'status' => STATUS_SUCCESS,
            'message' => 'Account has been successfully updated!',
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model      = new AccountModel();
            $bool       = true;
            $id         = $this->request->getVar('id');
            $username   = $this->request->getVar('username');
            $rules      = $model->getValidationRules(['except' => ['password', 'employee_id']]);
            $rule_msg   = $model->getValidationMessages();

            $rules['password']  = 'permit_empty|min_length[8]|alpha_numeric';

            if ($this->request->getVar('prev_username') === $username) {
                $rules['username']  = 'required|min_length[4]|alpha_numeric';
                $username   = '';
                $bool       = false;
            }


            if ($this->validate($rules, $rule_msg)) {
                $password   = $this->request->getVar('password');
                $params     = ["access_level" => $this->request->getVar('access_level')];
                $checkAcct  = $this->_checkAccount($this->request->getVar(), $bool);

                if (! $checkAcct) {
                    if (!empty($username)) {
                        $params['username'] = $username;
                    }

                    if (!empty($password)) {
                        $params['password'] = password_hash($password, PASSWORD_BCRYPT);
                    }

                    // Turn protection off - to skip validation
                    $model->protect(false);
                    $model->cleanRules(true);
                    $model->skipValidation(true);
                    // Update account
                    $model->update($id, $params);
                    // Turn protection on
                    $model->protect(true);
                    // Send mail
                    $data = $this->_sendMail($data);

                } else {
                    $data['status'] = STATUS_ERROR;
                    $data['message'] = 'Employee has already an account for the selected username or access level!';
                }
            } else {
                $data['status'] = STATUS_ERROR;
                $data['message'] = 'Validation error!';
                $data['errors'] = $this->validator->getErrors();
            }

            // Commit transaction
            $this->transCommit();
        } catch (Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status'] = STATUS_ERROR;
            $data['message'] = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Updaing of account
     * @param array $data
     * @return json
     */
    private function _sendMail($data)
    {
        if (! empty($this->request->getVar('password'))) {
            // Send mail to employee
            $res = $this->sendMail($this->request->getVar(), 'regular');
            $msg = $res['message'];

            if ($res['status'] === STATUS_SUCCESS) {
                $msg = $data['message'] . $msg;
            }

            $data['status'] = $res['status'];
            $data['message'] = $msg;
        }

        return $data;
    }

    /**
     * Checking of account
     * @param array $param
     * @param bool $username
     * @return array|bool
     */
    private function _checkAccount($params, $username = true)
    {
        $model          = new AccountModel();
        $employee_id    = isset($params['employee_id'])
                            ? $params['employee_id'] : session('employee_id');
        $checkAcct      = $model
                            ->where('employee_id', $employee_id)
                            ->where('access_level', $params['access_level']);

        if ($username) {
            $checkAcct->where('username', $params['username']);
        }

        return (!empty($params['password']))
        ? false
        : $checkAcct->first();
    }
}
