<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Traits\AccountMailTrait;
use App\Traits\ExportTrait;
use monken\TablesIgniter;

class Account extends BaseController
{
    /* Declare trait here to use */
    use AccountMailTrait, ExportTrait;

    /**
     * Use to initialize PermissionModel class
     * @var object
     */
    private $_model;

    /**
     * Use to get current module code
     * @var string
     */
    private $_module_code;
    
    /**
     * Use to get current permissions
     * @var string
     */

    private $_permissions;

    /**
     * Use to check if can add
     * @var bool
     */
    private $_can_add;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new AccountModel(); // Current model
        $this->_module_code = MODULE_CODES['accounts']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    /**
     * Display the account view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);

        $data['title']          = 'List of Accounts';
        $data['page_title']     = 'List of Accounts';
        $data['btn_add_lbl']    = 'Add New Account';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        $data['custom_js']      = 'hr/account/index.js';
        $data['routes']         = json_encode([
            'account' => [
                'list'      => url_to('account.list'),
                'fetch'     => url_to('account.fetch'),
                'delete'    => url_to('account.delete'),
            ],
        ]);

        return view('hr/account/index', $data);
    }

    /**
     * Get list of accounts
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table = new TablesIgniter();

        $table->setTable($this->_model->noticeTable())
            ->setSearch([
                'employee_id',
                'employee_name',
                'username',
                'access_level',
            ])
            ->setDefaultOrder('employee_name', 'asc')
            ->setOrder([
                'employee_id',
                'employee_name',
                'username',
                'access_level',
                null,
            ])
            ->setOutput([
                'employee_id',
                'employee_name',
                'username',
                $this->_model->dtAccessLevel(),
                $this->_model->buttons($this->_permissions),
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

        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $password       = $this->request->getVar('password');
                $employee_id    = $this->request->getVar('employee_id');
                $password_hash  = password_hash($password, PASSWORD_BCRYPT);
                $rules          = $this->_model->getValidationRules(['except' => ['password', 'employee_id']]);
                $rule_msg       = $this->_model->getValidationMessages();

                $rules['employee_id']   = 'required';
                $rules['password']      = 'required|min_length[8]|alpha_numeric';

                if ($this->validate($rules, $rule_msg)) {
                    $checkAcct = $this->_checkAccount($this->request->getVar(), false);

                    if (! $checkAcct) {
                        $params = [
                            'employee_id'   => $employee_id,
                            'username'      => $this->request->getVar('username'),
                            'password'      => $password_hash,
                            'access_level'  => $this->request->getVar('access_level'),
                        ];

                        // Turn protection off - to skip validation
                        $this->_model->protect(false);
                        $this->_model->cleanRules(true);
                        $this->_model->skipValidation(true);
                        // Insert account
                        $this->_model->insert($params);
                        // Turn protection on
                        $this->_model->protect(true);
                        // Send mail
                        $mailMsg            = $this->sendMailAccountNotif($employee_id, $this->request->getVar());
                        $data['message']    = $data['message'] . $mailMsg;
                    } else {
                        $data['status']     = STATUS_ERROR;
                        $data['message']    = 'Employee has already an account for the selected access level!';
                    }
                } else {
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = 'Validation error!';
                    $data['errors']     = $this->validator->getErrors();
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * For getting the data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Account has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id     = $this->request->getVar('id');
                $fields = 'employee_id, username, UPPER(access_level) as access_level';    
                $data['data'] = $this->_model->select($fields)->find($id);

                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Deletion of account
     *
     * @return json
     */
    public function delete() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Account has been deleted successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->_model->delete($this->request->getVar('id'))) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        $columns    = "
            {$this->_model->table}.employee_id,
            {$this->_model->view}.employee_name,
            {$this->_model->table}.username,
            UPPER({$this->_model->table}.access_level) AS role_code,
            {$this->_model->view}.created_by_name,
            DATE_FORMAT({$this->_model->table}.created_at, '".dt_sql_datetime_format()."') AS created_at_formatted
        ";
        $builder    = $this->_model->select($columns);
        $builder->join($this->_model->view, "{$this->_model->table}.account_id = {$this->_model->view}.id", 'left');
        $builder->orderBy("{$this->_model->view}.employee_name", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Employee ID',
            'Employee Name',
            'Username',
            'Role',
            'Created By',
            'Created At',
        ];
        $filename   = 'Accounts Masterlist';

        $this->exportToCsv($data, $header, $filename, function($data, $output) {
            $i      = 0;
            $roles  = get_roles();
            while (isset($data[$i])) {
                $row = $data[$i];
                
                if (isset($row['role_code']))
                    $row['role_code'] = $roles[$row['role_code']];

                fputcsv($output, $row);
                $i++;
            }
        });
    }

    /**
     * Updaing of account
     *
     * @return json
     */
    private function _update()
    {
        $data       = [
            'status' => STATUS_SUCCESS,
            'message' => 'Account has been successfully updated!',
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $bool       = true;
                $id         = $this->request->getVar('id');
                $username   = $this->request->getVar('username');
                $rules      = $this->_model->getValidationRules(['except' => ['password', 'employee_id']]);
                $rule_msg   = $this->_model->getValidationMessages();
    
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
                        $this->_model->protect(false);
                        $this->_model->cleanRules(true);
                        $this->_model->skipValidation(true);
                        // Update account
                        $this->_model->update($id, $params);
                        // Turn protection on
                        $this->_model->protect(true);
                        // Send mail
                        $employee_id        = $this->request->getVar('employee_id');
                        $mailMsg            = $this->sendMailAccountNotif($employee_id, $this->request->getVar(), true);
                        $data['message']    = $data['message'] . $mailMsg;
    
                    } else {
                        $data['status'] = STATUS_ERROR;
                        $data['message'] = 'Employee has already an account for the selected username or access level!';
                    }
                } else {
                    $data['status'] = STATUS_ERROR;
                    $data['message'] = 'Validation error!';
                    $data['errors'] = $this->validator->getErrors();
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * Checking of account
     * 
     * @param array $param
     * @param bool $username
     * 
     * @return array|bool
     */
    private function _checkAccount($params, $username = true)
    {
        $employee_id    = isset($params['employee_id']) ? $params['employee_id'] : session('employee_id');
        $checkAcct      = $this->_model
                            ->where('employee_id', $employee_id)
                            ->where('access_level', $params['access_level']);

        if ($username) {
            $checkAcct->where('username', $params['username']);
        }

        return (!empty($params['password'])) ? false : $checkAcct->first();
    }
}