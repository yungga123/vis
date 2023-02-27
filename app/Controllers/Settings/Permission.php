<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\PermissionModel;
use monken\TablesIgniter;

/**
 * Controller for Permission
 */
class Permission extends BaseController
{
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
        $this->_model       = new PermissionModel(); // Current model
        $this->_module_code = MODULE_CODES['permissions']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
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
        
        $data['title']          = 'Settings | Permissions';
        $data['page_title']     = 'Settings | Permissions';
        $data['can_add']        = is_admin() ? true : $this->_can_add;
        $data['btn_add_lbl']    = $this->_can_add ? 'Add Permission' : '';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['custom_js']      = 'settings/permission.js';
        $data['sweetalert2']    = true;
        $data['select2']        = true;

        return view('settings/permission/index', $data);
    }

    /**
     * Get list of permissions
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table  = new TablesIgniter();
        $custom = $this->_model->dtCustomizeData();

        $table->setTable($this->_model->noticeTable())
            ->setSearch([
                'role_code',
                'module_code',
                'permissions',
            ])
            ->setOrder([
                'role_code',
                'module_code',
                'permissions',
                null,
                // 'added_by',
                // 'updated_by',
            ])
            ->setOutput([
                $custom['role'],
                $custom['module'],
                $custom['permission'],
                $this->_model->buttons($this->_permissions),
                // 'added_by',
                // 'updated_by',
                // 'created_at',
                // 'updated_at',
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of permissions (inserting and updating permissions)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Permission has been added successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id             = $this->request->getVar('id');
            $role_code      = $this->request->getVar('role_code');
            $module_code    = $this->request->getVar('module_code');
            $permissions    = $this->request->getVar('permissions');
            $record         = $this->_model->checkRoleAndModule($role_code, $module_code);

            if (! empty($record) && empty($id)) {        
                $data['status']     = STATUS_ERROR;
                $data['message']    = 'Selected Role and Module had already a record! Use that to edit.'; 
            } else {
                $inputs = [
                    'role_code'    => $this->request->getVar('role_code'),
                    'module_code'  => $this->request->getVar('module_code'),
                    'permissions'  => is_array($permissions) ? implode(',', $permissions) : $permissions,
                ];

                if (! empty($id)) {
                    $inputs['permission_id']    = $id;
                    $inputs['updated_by']       = session('username');
                    $data['message']            = 'Permission has been updated successfully!';
                } else {
                    $inputs['added_by']         = session('username');
                }

                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                }
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Permission has been retrieved!'
        ];

        try {
            $id     = $this->request->getVar('id');
            $record = $this->_model->select($this->_model->allowedFields)->find($id);
            $record['permissions'] = explode(',', $record['permissions']);

            $data['data'] = $record;
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Permission has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            if (! $this->_model->delete($this->request->getVar('id'))) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
}