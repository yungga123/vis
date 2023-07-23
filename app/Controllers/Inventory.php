<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InventoryModel;
use monken\TablesIgniter;

/**
 * Controller for Inventory
 */
class Inventory extends BaseController
{
    /**
     * Display the inventory view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'Inventory | List of Items';
        $data['page_title']     = 'Inventory | List of Items';
        $data['can_add']        = true;
        $data['btn_add_lbl']    = 'Add New Item';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['custom_js']      = 'inventory/list.js';
        $data['sweetalert2']    = true;

        return view('inventory/index', $data);
    }

    /**
     * Get list of items
     *
     * @return array|dataTable
     */
    public function list()
    {
        $model = new InventoryModel();
        $table = new TablesIgniter();

        $table->setTable($model->noticeTable())
            ->setSearch([
                'item_name',
                'item_brand',
                'item_type',
                // 'item_sdp',
                // 'item_srp',
                // 'project_price',
                // 'stocks',
                // 'stock_unit',
                // 'date_of_purchase',
                'supplier',
                'location',
                'encoder',
            ])
            ->setOrder([
                null,
                'item_name',
                'item_brand',
                'item_type',
                'item_sdp',
                'item_srp',
                'project_price',
                'stocks',
                'stock_unit',
                'date_of_purchase',
                'supplier',
                'location',
                'encoder',
                'created_at',
            ])
            ->setOutput([
                $model->buttons(),
                'item_name',
                'item_brand',
                'item_type',
                'item_sdp',
                'item_srp',
                'project_price',
                'stocks',
                'stock_unit',
                'date_of_purchase',
                'supplier',
                'location',
                'encoder',
                'created_at',
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of items (inserting and updating items)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Item has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new InventoryModel();

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
    
    /**
     * For getting the item data using the id
     *
     * @return json
     */
    public function edit() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Item has been retrieved!'
        ];

        try {
            $model  = new InventoryModel();
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
            'message'   => 'Item has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $model = new InventoryModel();

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
