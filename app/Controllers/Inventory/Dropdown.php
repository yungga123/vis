<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Models\InventoryDropdownModel;
use monken\TablesIgniter;

class Dropdown extends BaseController
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
        $this->_model       = new InventoryDropdownModel(); // Current model
        $this->_module_code = MODULE_CODES['inventory']; // Current module
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

        $data['title']          = 'Inventory Dropdowns';
        $data['page_title']     = '<a href="'. url_to('inventory.home') .'" title="Click here to go to Inventory.">Inventory</a> Dropdowns';
        $data['can_add']        = $this->_can_add;
        $data['can_edit']       = $this->checkPermissions($this->_permissions, 'EDIT');
        $data['can_delete']     = $this->checkPermissions($this->_permissions, 'DELETE');
        $data['btn_add_lbl']    = 'Add Dropdown';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = 'inventory/dropdown/index.js';
        $data['routes']         = json_encode([
            'dropdown' => [
                'show'      => url_to('inventory.dropdown.show'),
                'list'      => url_to('inventory.dropdown.list'),
                'types'     => url_to('inventory.dropdown.types'),
                'save'      => url_to('inventory.dropdown.save'),
                'edit'      => url_to('inventory.dropdown.edit'),
                'delete'    => url_to('inventory.dropdown.delete'),
            ],
        ]);

        return view('inventory/dropdown/index', $data);
    }

    /**
     * Get list of items
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table = new TablesIgniter();
        $filter = (null !== $this->request->getVar('params')) ? $this->request->getVar('params') : null;

        $table->setTable($this->_model->noticeTable($filter))
            ->setSearch([
                'dropdown_id',
                'dropdown',
                'dropdown_type',
            ])
            ->setOrder([
                'dropdown_id',
                'dropdown',
                'dropdown_type',
                null,
            ])
            ->setOutput([
                'dropdown_id',
                'dropdown',
                'dropdown_type',
                $this->_model->buttons($this->_permissions),
            ]);

        return $table->getDatatable();
    }
    
    /**
     * Filtering records via dropdown types
     *
     * @return json
     */
    public function types() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Data has been retrieved!'
        ];

        try {
            $data['data'] = $this->_model->getDropdownTypes();
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
    
    /**
     * For getting the list of data based on 'dropdown_type'
     *
     * @return json
     */
    public function show() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Data has been retrieved!'
        ];

        try {
            $type = $this->request->getVar('dropdown_type');
            $type = is_string($type) ? strtoupper($type) : $type;

            if (in_array($type, $this->_model->otherCategoryTypes)) {
                $result = $this->_model->getOtherCategoryTypes($type);
            } else {
                $is_all = $type == 'CATEGORY' ? true : false;
                $columns = 'dropdown_id, dropdown, dropdown_type, other_category_type';
                $result = $this->_model->getDropdowns($type, $columns, $is_all);
            }

            $data['data'] = $result;
            $data['type'] = $type;
        } catch (\Exception$e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
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
            'message'   => 'Data has been saved successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $inputs = $this->request->getVar();

            if (isset($inputs['other_category_type'])) {
                if (! $this->_model->saveOtherCategoryTypes($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                }
            } else {
                if ($inputs['is_category']) {
                    $inputs['dropdown']         = strtoupper($inputs['dropdown']);
                    $inputs['dropdown_type']    = 'CATEGORY';
                } else {
                    $inputs['parent_id'] = $inputs['dropdown_type'];
                    $inputs['dropdown_type'] = $inputs['dropdown_type_text'];
                }
    
                if ($this->request->getVar('dropdown_id')) {
                    $data['message']    = 'Data has been updated successfully!';
                }
    
                if (! $this->_model->saveDropdowns($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    if ($inputs['is_category'] && !empty($inputs['dropdown_id'])) {
                        $this->_model->set(['dropdown_type' => $inputs['dropdown']])
                            ->where('parent_id', $inputs['dropdown_id'])->update();
                    }
                }
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
            'message'   => 'Data has been retrieved!'
        ];

        try {
            $id     = $this->request->getVar('id');
            $fields = 'dropdown_id, dropdown, dropdown_type, parent_id';
            $result = $this->_model->select($fields)->find($id);

            $data['data'] = $result;
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
            'message'   => 'Data has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id = $this->request->getVar('id');
            if ($this->_model->categoryHasDropdowns($id)) {                
                $data['status']     = STATUS_INFO;
                $data['message']    = "Category has already dropdowns added and can't be deleted! Remove the dropdown first in order to delete this category.";
            } else {
                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                }
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
