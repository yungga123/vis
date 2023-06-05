<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InventoryModel;
use App\Models\InventoryDropdownModel;
use monken\TablesIgniter;

/**
 * Controller for Inventory
 */
class Inventory extends BaseController
{
    /**
     * Use to initialize corresponding model
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
     * Use to initialize inventory dropdowns model
     * @var object
     */
    private $_mdropdown;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new InventoryModel(); // Current model
        $this->_module_code = MODULE_CODES['inventory']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
        $this->_mdropdown   = new InventoryDropdownModel();
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);

        $dropdowns_link         = '<small><a href="'. url_to('inventory.dropdown.home') .'" title="Click here to go to Inventory Dropdowns">Inventory Dropdowns</a></small>';
        $data['title']          = 'Inventory | Masterlist';
        $data['page_title']     = 'Inventory | Masterlist - '. $dropdowns_link;
        $data['can_add']        = $this->_can_add;
        $data['can_edit']       = $this->checkPermissions($this->_permissions, 'EDIT');
        $data['can_item_in']    = $this->checkPermissions($this->_permissions, 'ITEM_IN');
        $data['can_item_out']   = $this->checkPermissions($this->_permissions, 'ITEM_OUT');
        $data['btn_add_lbl']    = 'Add New Item';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = 'inventory/list.js';
        $data['routes']         = json_encode([
            'inventory' => [
                'list'      => url_to('inventory.list'),
                'save'      => url_to('inventory.save'),
                'edit'      => url_to('inventory.edit'),
                'delete'    => url_to('inventory.delete'),
            ],
            'dropdown' => [
                'show'      => url_to('inventory.dropdown.show'),
                'save'      => url_to('inventory.dropdown.save'),
            ],
        ]);
        $data['categories']     = $this->inventoryCategoriesOptions();

        return view('inventory/index', $data);
    }

    /**
     * Get list of items
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table = new TablesIgniter();
        $request = $this->request->getVar();

        $table->setTable($this->_model->noticeTable($request))
            ->setSearch([
                'id',
                'category',
                'sub_category',
                'item_brand',
                'item_model',
                'item_description',
                'encoder',
            ])
            ->setOrder([
                null,
                'id',
                'category',
                'sub_category',
                'item_brand',
                'item_model',
                'item_description',
                'item_size',
                'total',
                'stocks',
                'stock_unit',
                'encoder',
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                'id',
                'category',
                'sub_category',
                'item_brand',
                'item_model',
                'item_description',
                'item_size',
                'total',
                'stocks',
                'stock_unit',
                'encoder',
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
            if (! $this->_model->save($this->request->getVar())) {
                $data['errors']     = $this->_model->errors();
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
            $id             = $this->request->getVar('id');
            $data['data']   = $this->_model->getInventories($id);
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

    private function inventoryCategoriesOptions()
    {
        $option     = '';
        $others     = '';
        $columns    = 'dropdown_id, dropdown, other_category_type';
        $categories = $this->_mdropdown->getDropdowns('CATEGORY', $columns, true);

        if (! empty($categories)) {
            foreach ($categories as $category) {
                if (empty($category['other_category_type'])) {
                    $option     .= '
                        <option value="'. $category['dropdown_id'] .'">
                            '. $category['dropdown'] .'
                        </option>
                    ';
                } else {
                    $others     .= '
                        <option value="'. $category['dropdown_id'] .'">
                            '. $category['dropdown'] .'
                        </option>
                    ';
                }
            }

            $option .= '<optgroup label="Other Categories">'. $others .'</optgroup>';
        }

        return $option;
    }
}
