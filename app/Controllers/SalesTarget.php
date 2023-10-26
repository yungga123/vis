<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\SalesTargetModel;
use Exception;
use monken\TablesIgniter;

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

    public function list()
    {
        $table  = new TablesIgniter();
        $builder = $this->_model->noticeTable();

        $table
            ->setTable($builder)
            ->setSearch([
                "sales_id",
                "employee_name",
                "q1_target",
                "q2_target",
                "q3_target",
                "q4_target"
            ])
            ->setDefaultOrder('id','desc')
            ->setOrder([
                "sales_id",
                "employee_name",
                "q1_target",
                "q2_target",
                "q3_target",
                "q4_target"
            ])
            ->setOutput([
                $this->_model->buttons(),
                "sales_id",
                "employee_name",
                "q1_target",
                "q2_target",
                "q3_target",
                "q4_target"
            ]);
        
        return $table->getDatatable();

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
        $employeesModel = new EmployeeModel();

        $data['employees'] = $employeesModel->find();

        return $this->response->setJSON($data);
    }

    // To be used for employee selection
    public function employee() {
        //$employeesModel = new EmployeeModel();

        $id = $this->request->getVar('id');
        $data['employee'] = $this->_model->where('sales_id',$id)->find();

        return $this->response->setJSON($data);
    }

    public function delete()
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Succesfully Deleted!!'
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

    //Compute total Sales Target
    public function totalSalesTarget() {
        
        $model = $this->_model;

        $data['q1_total'] = $model->selectSum('q1_target')->find();
        $data['q2_total'] = $model->selectSum('q2_target')->find();
        $data['q3_total'] = $model->selectSum('q3_target')->find();
        $data['q4_total'] = $model->selectSum('q4_target')->find();

        return $this->response->setJSON($data);

    }

    //Individual Sales Target
    public function indvSalesTarget() {
        
        $model = $this->_model;
        $employee_id = session('employee_id');

        $data['q1_target'] = $model->select('q1_target')->where('sales_id',$employee_id)->find();
        $data['q2_target'] = $model->select('q2_target')->where('sales_id',$employee_id)->find();
        $data['q3_target'] = $model->select('q3_target')->where('sales_id',$employee_id)->find();
        $data['q4_target'] = $model->select('q4_target')->where('sales_id',$employee_id)->find();

        return $this->response->setJSON($data);
        
    }
}
