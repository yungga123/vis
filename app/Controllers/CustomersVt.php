<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersVtModel;
use monken\TablesIgniter;

class CustomersVt extends BaseController
{
    public function index()
    {
        $data['title'] = 'Customers';
        $data['page_title'] = 'Customers | List';
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
            'message'   => 'Item has been saved successfully!'
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
                $data['message']    = 'Item has been updated successfully!';
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
