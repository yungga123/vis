<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use CodeIgniter\Events\Events;
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
     * @var array
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
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    /**
     * Display the account view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $data['title']          = 'List of Accounts';
        $data['page_title']     = 'List of Accounts';
        $data['btn_add_lbl']    = 'Add New Account';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        $data['custom_js']      = ['hr/account/index.js', 'dt_filter.js'];
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
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);

        $table->setTable($builder)
            ->setSearch([
                'employee_id',
                'employee_name',
                'username',
            ])
            ->setDefaultOrder('employee_name', 'asc')
            ->setOrder([
                null,
                'employee_id',
                'employee_name',
                'username',
                'access_level',
                'created_by_name',
                'created_at',
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                'employee_id',
                'employee_name',
                'username',
                $this->_model->dtAccessLevel(),
                'created_by_name',
                'created_at',
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Account')
        ];

        // Check if id field has value, then this is an update
        if (! empty($this->request->getVar('id'))) {
            return $this->_update();
        }

        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_ADD, true);

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
                        $details = [
                            'employee_id'   => $employee_id,
                            'username'      => $this->request->getVar('username'),
                            'password'      => $this->request->getVar('password'),
                            'action'        => 'Created',
                        ];
                        Events::trigger('send_mail_notif_account', $details);
                    } else {
                        $data['status']     = res_lang('status.error');
                        $data['message']    = 'Employee has already an account for the selected access level!';
                    }
                } else {
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Account')
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Account')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
                
                if (! $this->_model->delete($this->request->getVar('id'))) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * Updating of account
     *
     * @return json
     */
    private function _update()
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.updated', 'Account'),
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_EDIT, true);

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
                        $details = [
                            'employee_id'   => $this->request->getVar('employee_id'),
                            'username'      => $this->request->getVar('username'),
                            'password'      => $this->request->getVar('password'),
                            'action'        => 'Changed',
                        ];
                        Events::trigger('send_mail_notif_account', $details);
                    } else {
                        $data['status']     = res_lang('status.error');
                        $data['message']    = 'Employee has already an account for the selected username or access level!';
                    }
                } else {
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                    $data['errors']     = $this->validator->getErrors();
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
