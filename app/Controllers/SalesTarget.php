<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeesModel;
use App\Models\SalesTargetModel;

class SalesTarget extends BaseController
{
    /**
     * Use to initialize PermissionModel class
     * @var object
     */
    private $_model;

    public function __construct()
    {
        $this->_model       = new SalesTargetModel(); // Current model
    }


    public function save()
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Sales Target has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            if (! $this->_model->save($this->request->getVar())) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Sales Target has been updated successfully!';
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    // To be used for employee selection
    public function employees() {
        $employeesModel = new EmployeesModel();

        $data['employees'] = $employeesModel->find();

        return $this->response->setJSON($data);
    }

    // To be used for employee selection
    public function employee() {
        //$employeesModel = new EmployeesModel();

        $id = $this->request->getVar('id');
        $data['employee'] = $this->_model->where('sales_id',$id)->find();

        return $this->response->setJSON($data);
    }
}
