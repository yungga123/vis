<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\RequestPurchaseFormModel;
use App\Models\RPFItemModel;
use App\Traits\InventoryTrait;
use App\Traits\GeneralInfoTrait;
use App\Traits\CommonTrait;
use monken\TablesIgniter;

class RequestPurchaseForm extends BaseController
{
    /* Declare trait here to use */
    use InventoryTrait, GeneralInfoTrait, CommonTrait;

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
     * Class constructor
     */
    public function __construct()
    {
        $this->_model       = new RequestPurchaseFormModel(); // Current model
        $this->_module_code = MODULE_CODES['purchasing_rpf']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
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

        $data['title']          = get_modules($this->_module_code);
        $data['page_title']     = get_modules($this->_module_code);
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Request to Purchase Form';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = ['purchasing/rpf/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'rpf' => [
                'list'      => url_to('rpf.list'),
                'save'      => url_to('rpf.save'),
                'fetch'     => url_to('rpf.fetch'),
                'delete'    => url_to('rpf.delete'),
                'change'    => url_to('rpf.change'),
            ],
            'inventory' => [
                'common' => [
                    'masterlist'    => url_to('inventory.common.masterlist'),
                ]
            ],
            'purchasing' => [
                'common' => [
                    'suppliers' => url_to('purchasing.common.suppliers'),
                ]
            ],
        ]);

        return view('purchasing/rpf/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);

        $table->setTable($builder)
            ->setSearch([
                'status',
            ])
            ->setOrder([
                null,
                null,
                null,
                'id',
                'date_needed_formatted',
                'created_by_name',
                'created_at_formatted',
                'accepted_by_name',
                'accepted_at_formatted',
                'reviewed_by_name',
                'reviewed_at_formatted',
                'received_by_name',
                'received_at_formatted',
                'rejected_by_name',
                'rejected_at_formatted',
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                $this->_model->dtViewRpfItems(),
                $this->_model->dtRpfStatusFormat(),
                'id',
                'date_needed_formatted',
                'created_by_name',
                'created_at_formatted',
                'accepted_by_name',
                'accepted_at_formatted',
                'reviewed_by_name',
                'reviewed_at_formatted',
                'received_by_name',
                'received_at_formatted',
                'rejected_by_name',
                'rejected_at_formatted',
            ]);

        return $table->getDatatable();
    }

    /**
     * Saving process of record (inserting and updating)
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'RFP has been saved successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id     = $this->request->getVar('id');
                $inv_id = $this->request->getVar('inventory_id');
                $sup_id = $this->request->getVar('supplier_id');
                $q_in   = $this->request->getVar('quantity_in');
                $inputs = [
                    'id'            => $id,
                    'date_needed'   => $this->request->getVar('date_needed'),
                    'inventory_id'  => (isset($inv_id) && !has_empty_value($inv_id)) 
                        ? (!has_empty_value($q_in) && count($inv_id) === count($q_in) ? $inv_id : null) 
                        : null,
                    // 'supplier_id'  => (isset($inv_id) && !has_empty_value($sup_id)) ? $sup_id : null,
                    'quantity_in'   => !has_empty_value($q_in) ? $q_in : null,
                ];

                // Check restriction
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    $rpfItemModel   = new RPFItemModel();
                    $rpf_id         = $id ? $id : $this->_model->insertID();
                    $rpfItemModel->saveRpfItems($this->request->getVar(), $rpf_id);
                }

                if ($id) {
                    $data['message']    = 'RFP has been updated successfully!';
                }
                return $data;
            }
        );

        return $response;
    }
    
    /**
     * For fetching record using the id or other
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'RPF has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $rpfItemModel   = new RPFItemModel(); 
                $items          = $rpfItemModel->getRpfItemsByRpfId($id, true, true);
                if ($this->request->getVar('rpf_items')) {
                    $data['data']       = $items;
                    $data['message']    = 'RPF items has been retrieved!';
                } else {
                    $table      = $this->_model->table;
                    $view       = $this->_model->view;  
                    $columns    = "
                        {$table}.id, {$table}.date_needed,
                        DATE_FORMAT({$table}.date_needed, '".dt_sql_date_format()."') AS date_needed_formatted,
                        DATE_FORMAT({$table}.created_at, '".dt_sql_datetime_format()."') AS created_at_formatted
                    ";                 
                    $record     = $this->_model->getRequestPurchaseForms($id, true, $columns);
                    $data['data']               = $record;
                    $data['data']['items']      = $items;
                }
                return $data;
            }, 
            false
        );

        return $response;
    }

    /**
     * Saving process of items
     *
     * @return json
     */
    public function delete() 
    {
        $data       = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'PRF has been deleted successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                // Check restriction
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                if (! $this->_model->delete($id)) {
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
     * Changing status of prf
     *
     * @return json
     */
    public function change() 
    {
        $data       = [];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id     = $this->request->getVar('id');
                $status = set_rpf_status($this->request->getVar('status'));
                $inputs = ['status' => $status];

                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    $data['status']     = STATUS_SUCCESS;
                    $data['message']    = 'RPF has been '. strtoupper($status) .' successfully!';

                    if ($status === 'received') {
                        $prfItemModel = new RPFItemModel();
                        $prfItemModel->updateRpfItems($this->request->getVar(), $id);
                    }
                }
                return $data;
            }
        );

        return $response;
    }

    /**
     * Printing record
     *
     * @return view
     */
    public function print() 
    {
        // Check role & action if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, 'PRINT');
        
        $id                 = $this->request->getUri()->getSegment(3);
        $columns            = $this->_model->columns(true, true);
        $builder            = $this->_model->select($columns);
        
        $this->_model->joinView($builder);

        $rpfItemModel           = new RPFItemModel(); 
        $items                  = $rpfItemModel->getRpfItemsByRpfId($id, true, true);
        $data['rpf']            = $builder->find($id);
        $data['rpf_items']      = $items;
        $data['title']          = 'Print Requisition Form';
        $data['company_logo']   = $this->getGeneralInfo('company_logo');

        return view('purchasing/rpf/print', $data);
    }
}
