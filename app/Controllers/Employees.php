<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeesModel;
use monken\TablesIgniter;

class Employees extends BaseController
{
    /**
     * Display the employee view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'List of Employees';
        $data['page_title']     = 'List of Employees';
        $data['custom_js']      = 'employees/list.js';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['can_add']        = true;

        return view('employees/index', $data);
    }

    /**
     * Get list of employees
     *
     * @return array|dataTable
     */
    public function list()
    {
        $model = new EmployeesModel();
        $table = new TablesIgniter();

        $table->setTable($model->noticeTable())
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
                $model->buttons(),
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
            $model  = new EmployeesModel();
            $id     = $this->request->getVar('id');
            $prev   = $this->request->getVar('prev_employee_id');
            $curr   = $this->request->getVar('employee_id');
            $rules  = $model->getValidationRules();

            if (! empty($id) && $prev === $curr) {
                $rules['employee_id'] = 'required|alpha_numeric|max_length[20]';
            }

            $model->setValidationRules($rules);

            if (! $model->save($this->request->getVar())) {
                $data['errors']     = $model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Employee has been updated successfully!';
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
            $model  = new EmployeesModel();
            $id     = $this->request->getVar('id');
            $fields = $model->allowedFields;

            $data['data'] = $model->select($fields)->find($id);;
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
            $model = new EmployeesModel();

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
}
