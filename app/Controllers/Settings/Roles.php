<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\RolesModel;
use monken\TablesIgniter;

class Roles extends BaseController
{
    /**
     * Use to initialize RolesModel class
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
        $this->_model       = new RolesModel(); // Current model
        $this->_module_code = MODULE_CODES['roles']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    /**
     * Display the permission view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);
        
        $data['title']          = 'Settings | Roles';
        $data['page_title']     = 'Settings | Roles';
        $data['can_add']        = is_admin() ? true : $this->_can_add;
        $data['btn_add_lbl']    = $this->_can_add ? 'Add Role' : '';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['custom_js']      = 'settings/roles.js';
        $data['sweetalert2']    = true;

        return view('settings/roles/index', $data);
    }

    /**
     * Get list of roles
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table  = new TablesIgniter();

        $table->setTable($this->_model->noticeTable())
            ->setSearch([
                'role_code',
                'description',
            ])
            ->setOrder([
                'role_code',
                'description',
                null,
            ])
            ->setOutput([
                'role_code',
                'description',
                $this->_model->buttons($this->_permissions),
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of roles (inserting and updating roles)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Role')
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id         = $this->request->getVar('id');
            $role_code  = strtoupper($this->request->getVar('role_code'));
            $inputs     = [
                'role_code'    => $role_code,
                'description'  => ucwords(strtolower($this->request->getVar('description')))
            ];

            if (! empty($id)) {                
                $prev_role_code  = strtoupper($this->request->getVar('prev_role_code'));

                if ($role_code == $prev_role_code) {
                    // Change validation rules of role_code for update
                    $rules = $this->_model->getValidationRules();

                    $rules['role_code'] = 'required|min_length[2]|max_length[50]|alpha_dash';
                    $this->_model->setValidationRules($rules);
                }

                $inputs['role_id']      = $id;
                $inputs['updated_by']   = session('username');
                $data['message']        = res_lang('success.updated', 'Role');
            } else {
                $role_type = $this->request->getVar('role_type');

                if ($role_type === 'manager') {
                    if (! check_string_contains($role_code, 'MANAGER_')) {
                        $inputs['role_code'] = 'MANAGER_' . $role_code;
                    }
                } else if ($role_type === 'supervisor') {                    
                    if (! check_string_contains($role_code, 'SUPERVISOR_')) {
                        $inputs['role_code'] = 'SUPERVISOR_' . $role_code;
                    }
                }
                
                $inputs['created_by']   = session('username');
            }

            if (! $this->_model->save($inputs)) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = res_lang('status.error');
                $data['message']    = res_lang('error.validation');
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();
            $this->logExceptionError($e, __METHOD__);
            
            $data['status']     = res_lang('status.error');
            $data['message']    = res_lang('error.process');
        }

        return $this->response->setJSON($data);
    }
    
    /**
     * For getting the permission data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Role')
        ];

        try {
            $id             = $this->request->getVar('id');
            $data['data']   = $this->_model->select($this->_model->allowedFields)->find($id);

        } catch (\Exception $e) {
            $this->logExceptionError($e, __METHOD__);

            $data['status']     = res_lang('status.error');
            $data['message']    = res_lang('error.process');
        }

        return $this->response->setJSON($data);
    }

    /**
     * Saving process of permission
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Role')
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
            
            $id         = $this->request->getVar('id');
            $role_code  = $this->_model->getSpecificRoleCode($id);

            if ($this->_model->roleHasDependecy($role_code)) {
                $data['status']     = res_lang('status.error');
                $data['message']    = "Role can't be deleted! This role was already used in either <b>Permissions</b> or <b>Accounts</b> modules.";
            } else {
                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    log_msg(
                        "Message: {message} \nDeleted by: {username}", 
                        ['message' => $data['message'], 'username' => session('username')]
                    );
                }
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();
            $this->logExceptionError($e, __METHOD__);

            $data['status']     = res_lang('status.error');
            $data['message']    = res_lang('error.process');
        }

        return $this->response->setJSON($data);
    }
}
