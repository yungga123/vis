<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomersModel;
use monken\TablesIgniter;

class Customers extends BaseController
{
    public function index()
    {
        $data['title'] = 'Customers Forecast';
        $data['page_title'] = 'Customers Forecast | List';
        $data['can_add'] = true;
        $data['with_dtTable'] = true;
        $data['with_jszip'] = true;
        $data['sweetalert2'] = true;
        $data['custom_js'] = 'customers/list.js';

        return view('customers/index',$data);
    }

    public function list()
    {
        $customersModel = new CustomersModel();
        $customersTable = new TablesIgniter();

        $customersTable->setTable($customersModel->noticeTable())
                         ->setSearch([
                            "id",
                            "customer_name",
                            "contact_person",
                            "address",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes"
                         ])
                         ->setDefaultOrder('id','desc')
                         ->setOrder([
                            null,
                            null,
                            "id",
                            "customer_name",
                            "contact_person",
                            "address",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes"
                         ])
                         ->setOutput([
                            $customersModel->button(),
                            $customersModel->buttonBranch(),
                            "id",
                            "customer_name",
                            "contact_person",
                            "address",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes"
                         ]);
        
        return $customersTable->getDatatable();

    }

    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new CustomersModel();

            if (! $model->save($this->request->getVar())) {
                $data['errors']     = $model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Customer has been updated successfully!';
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
     * For getting the item data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been retrieved!'
        ];

        try {
            $model  = new CustomersModel();
            $id     = $this->request->getVar('id');
            // $item   = $model->select($model->allowedFields)->find($id);

            $data['data'] = $model->select($model->allowedFields)->find($id);;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    /**
     * Saving process of items
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new CustomersModel();

            if (! $model->delete($this->request->getVar('id'))) {
                $data['errors']     = $model->errors();
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

    public function branchCustomersList() {
        $customersBranchModel = new CustomerBranchModel();
        $customersBranchTable = new TablesIgniter();
        $customers_id = $this->request->getGet('customers_id');

        $customersBranchTable->setTable($customersBranchModel->noticeTable($customers_id))
                         ->setSearch([
                            "branch_name",
                            "contact_person",
                            "contact_number",
                            "address",
                            "email_address",
                            "notes"
                         ])
                         ->setDefaultOrder('id','desc')
                         ->setOrder([
                            null,
                            "branch_name",
                            "contact_person",
                            "contact_number",
                            "address",
                            "email_address",
                            "notes"
                         ])
                         ->setOutput([
                            $customersBranchModel->button(),
                            "branch_name",
                            "contact_person",
                            "contact_number",
                            "address",
                            "email_address",
                            "notes"
                         ]);

        return $customersBranchTable->getDatatable();
    }

    public function getCustomers() {
        $model = new CustomersModel();
        $id = $this->request->getVar('gcustomer_id');
        $data['status'] = STATUS_SUCCESS;
        $data['data'] = $model->find($id);

        print_r($id); return;
        return $this->response->setJSON($data);
    }

    public function saveBranch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer Branch has been saved successfully!'
        ];

        // print_r($this->request->getVar()); return;
        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new CustomerBranchModel();

            if (! $model->save($this->request->getVar())) {
                $data['errors']     = $model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";

                $errors = $model->errors();
                $arr = [];
                if (! empty($errors)) {
                    foreach ($errors as $key => $value) {
                        $arr['b'.$key] = $value;
                    }
                }

                $data['errors']  = $arr;
            }

            if ($this->request->getVar('id')) {
                $data['message']    = 'Customer Branch has been updated successfully!';
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

    public function editBranch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been retrieved!'
        ];

        try {
            $model  = new CustomerBranchModel();
            $id     = $this->request->getVar('id');
            // $item   = $model->select($model->allowedFields)->find($id);

            $data['data'] = $model->select($model->allowedFields)->find($id);;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }

    public function deleteBranch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer Branch has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new CustomerBranchModel();

            if (! $model->delete($this->request->getVar('id'))) {
                $data['errors']     = $model->errors();
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
