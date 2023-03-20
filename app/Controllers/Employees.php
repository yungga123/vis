<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeesModel;
use App\Models\Accounts as ModelsAccounts;
use Exception;
use monken\TablesIgniter;

class Employees extends BaseController
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
        $this->_model       = new EmployeesModel(); // Current model
        $this->_module_code = MODULE_CODES['employees']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    /**
     * Display the employee view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);

        $data['title']          = 'List of Employees';
        $data['page_title']     = 'List of Employees';
        $data['custom_js']      = 'employees/list.js';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add New Employee';

        return view('employees/index', $data);
    }

    /**
     * Get list of employees
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table = new TablesIgniter();

        $table->setTable($this->_model->noticeTable())
            ->setSearch([
                "employee_id", 
                "employee_name", 
                "address", 
                "gender", 
                "civil_status", 
                "date_of_birth", 
                "place_of_birth", 
                "position", 
                "employment_status", 
                "date_hired", 
                "language", 
                "contact_number", 
                "email_address", 
                "sss_no", 
                "tin_no", 
                "philhealth_no", 
                "pag_ibig_no", 
                "educational_attainment", 
                "course", "emergency_name", 
                "emergency_contact_no", 
                "emergency_address", 
                "name_of_spouse", 
                "spouse_contact_no", 
                "no_of_children", 
                "spouse_address"
            ])
            ->setOrder([
                null,
                "employee_id",
                "employee_name", 
                "address", 
                "gender", 
                "civil_status", 
                "date_of_birth", 
                "place_of_birth", 
                "position", 
                "employment_status", 
                "date_hired", 
                "language", 
                "contact_number", 
                "email_address", 
                "sss_no", 
                "tin_no", 
                "philhealth_no", 
                "pag_ibig_no", 
                "educational_attainment", 
                "course", "emergency_name", 
                "emergency_contact_no", 
                "emergency_address", 
                "name_of_spouse", 
                "spouse_contact_no", 
                "no_of_children", 
                "spouse_address"
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                "employee_id",
                "employee_name", 
                "address", 
                "gender", 
                "civil_status", 
                "date_of_birth", 
                "place_of_birth", 
                "position", 
                "employment_status", 
                "date_hired", 
                "language", 
                "contact_number", 
                "email_address", 
                "sss_no", 
                "tin_no", 
                "philhealth_no", 
                "pag_ibig_no", 
                "educational_attainment", 
                "course", 
                "emergency_name", 
                "emergency_contact_no", 
                "emergency_address", 
                "name_of_spouse", 
                "spouse_contact_no", 
                "no_of_children", 
                "spouse_address"
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of employees (inserting and updating employees)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Employee has been added successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id     = $this->request->getVar('id');
            $prev   = $this->request->getVar('prev_employee_id');
            $curr   = $this->request->getVar('employee_id');
            $rules  = $this->_model->getValidationRules();

            if (! empty($id) && $prev === $curr) {
                $rules['employee_id'] = 'required|alpha_numeric|max_length[20]';
            }

            $this->_model->setValidationRules($rules);

            if (! $this->_model->save($this->request->getVar())) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error! There are still required fields that need to be addressed. Please double check!";
            } else {
                if (! empty($id)) {
                    $data['message']    = 'Employee has been updated successfully!';
    
                    if ($prev !== $curr) {
                        $accountModel = new ModelsAccounts();
                        $accountModel->where('employee_id', $prev)
                            ->set(['employee_id' => $curr])->update();
                    }
                }
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
     * For getting the employee data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Employee has been retrieved!'
        ];

        try {
            $id     = $this->request->getVar('id');
            $fields = $this->_model->allowedFields;

            $data['data'] = $this->_model->select($fields)->find($id);;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Deletion of employee
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Employee has been deleted successfully!'
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
        } catch (Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
}
