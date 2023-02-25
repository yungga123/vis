<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersVtBranchModel;
use App\Models\CustomersVtModel;
use monken\TablesIgniter;

class CustomersVt extends BaseController
{
    public function index()
    {
        $data['title'] = 'Customers (Commercial)';
        $data['page_title'] = 'Customers | List (Commercial)';
        $data['can_add'] = true;
        $data['with_dtTable'] = true;
        $data['with_jszip'] = true;
        $data['sweetalert2'] = true;
        $data['custom_js'] = 'customersvt/list.js';

        return view('customers_vt/index',$data);
    }

    public function list()
    {
        $customersVtModel = new CustomersVtModel();
        $customersVtTable = new TablesIgniter();

        $customersVtTable->setTable($customersVtModel->noticeTable())
                         ->setSearch([
                            "forecast",
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
                            "forecast",
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
                            $customersVtModel->button(),
                            $customersVtModel->buttonBranch(),
                            "forecast",
                            "id",
                            "customer_name",
                            "contact_person",
                            "address",
                            "contact_number",
                            "email_address",
                            "source",
                            "notes"
                         ]);
        
        return $customersVtTable->getDatatable();

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
            $model = new CustomersVtModel();

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
            $model  = new CustomersVtModel();
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
            $model = new CustomersVtModel();

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

    public function branchCustomervtList() {
        $customersVtBranchModel = new CustomersVtBranchModel();
        $customersVtBranchTable = new TablesIgniter();
        $customervt_id = $this->request->getGet('customervt_id');

        $customersVtBranchTable->setTable($customersVtBranchModel->noticeTable($customervt_id))
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
                            $customersVtBranchModel->button(),
                            "branch_name",
                            "contact_person",
                            "contact_number",
                            "address",
                            "email_address",
                            "notes"
                         ]);

        return $customersVtBranchTable->getDatatable();
    }

    public function getCustomers() {
        $model = new CustomersVtModel();
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
            $model = new CustomersVtBranchModel();

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
            $model  = new CustomersVtBranchModel();
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
            $model = new CustomersVtBranchModel();

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
