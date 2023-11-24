<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\AccountModel;
use App\Traits\ExportTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class Employee extends BaseController
{
    /* Declare trait here to use */
    use ExportTrait, HRTrait;

    /**
     * Use to initialize model class
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
        $this->_model       = new EmployeeModel(); // Current model
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
        $data['btn_add_lbl']    = 'Add New Employee';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['custom_js']      = 'hr/employee/index.js';
        $data['routes']         = json_encode([
            'employee' => [
                'list'      => url_to('employee.list'),
                'fetch'     => url_to('employee.fetch'),
                'delete'    => url_to('employee.delete'),
            ],
        ]);

        return view('hr/employee/index', $data);
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
                'employee_id', 
                'employee_name', 
                'gender', 
                'civil_status', 
                'place_of_birth', 
                'position', 
                'employment_status', 
                'contact_number', 
                'email_address', 
                'sss_no', 
                'tin_no', 
                'philhealth_no', 
                'pag_ibig_no', 
                'educational_attainment', 
                'course',
            ])
            ->setDefaultOrder('employee_name', 'asc')
            ->setOrder(array_merge([null, null], $this->_model->dtColumns))
            ->setOutput(array_merge(
                [dt_empty_col(), $this->_model->buttons($this->_permissions)], 
                $this->_model->dtColumns
            ));

        return $table->getDatatable();
    }

    /**
     * Saving process of employees (inserting and updating employee)
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Employee has been added successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
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
                    $data['message']    = 'Validation error! There are still required fields that need to be addressed. Please double check!';
                } else {
                    if (! empty($id)) {
                        $data['message']    = 'Employee has been updated successfully!';
        
                        if ($prev !== $curr) {
                            $accountModel = new AccountModel();
                            $accountModel->where('employee_id', $prev)
                                ->set(['employee_id' => $curr])->update();
                        }
                    }
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * For getting the employee data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Employee has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $fields         = $this->_model->allowedFields;    
                $data['data']   = $this->_model->select($fields)->find($id);

                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Deletion of employee
     *
     * @return json
     */
    public function delete() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Employee has been deleted successfully!'
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
        $builder    = $this->_model->db->table($this->_model->view);
        $builder->select($this->_model->dtColumns);
        $builder->orderBy('employee_name', 'ASC');

        $data       = $builder->get()->getResultArray();
        $header     = [
            'Employee ID',
            'Employee Name',
            'Address',
            'Gender',
            'Civil Status',
            'Birthdate',
            'Birthplace',
            'Position',
            'Employment Status',
            'Date Hired',
            'Date Resigned',
            'Contact Number',
            'Email Address',
            'SSS Number',
            'TIN Number',
            'PhilHealth Number',
            'PAGIBIG Number',
            'Educational Attainment',
            'Course',
            'Emergency Name',
            'Emergency Contact Number',
            'Emergency Address',
            'Spouse Name',
            'Spouse Contact Number',
            'No. of Children',
            'Spouse Address',
            'Created By',
            'Created At',
        ];
        $filename   = 'Employees Masterlist';

        $this->exportToCsv($data, $header, $filename);
    }
}
