<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Models\ProjectRequestFormModel;
use App\Models\PRFItemModel;
use App\Traits\InventoryTrait;
use monken\TablesIgniter;

class ProjectRequestForm extends BaseController
{
    /* Declare trait here to use */
    use InventoryTrait;

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
        $this->_model       = new ProjectRequestFormModel(); // Current model
        $this->_module_code = MODULE_CODES['inventory_prf']; // Current module
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

        $data['title']          = 'Project Request Forms';
        $data['page_title']     = 'Project Request Forms';
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Project Request Form';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['custom_js']      = 'inventory/prf/index.js';
        $data['routes']         = json_encode([
            'prf' => [
                'list'      => url_to('prf.list'),
                'save'      => url_to('prf.save'),
                'fetch'     => url_to('prf.fetch'),
                'delete'    => url_to('prf.delete'),
                'change'    => url_to('prf.change'),
            ],
            'inventory' => [
                'common' => [
                    'masterlist'    => url_to('inventory.common.masterlist'),
                    'joborders'     => url_to('inventory.common.joborders'),
                ]
            ]
        ]);

        return view('inventory/prf/index', $data);
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
                'quotation_num',
                'customer_name',
                'work_type',
            ])
            ->setOrder([
                null,
                null,
                null,
                'id',
                'job_order_id',
                'quotation_num',
                'customer_name',
                'work_type',
                'process_date_formatted',
                'remarks',
                'created_by_name',
                'created_at_formatted',
                'accepted_by_name',
                'accepted_at_formatted',
                'rejected_by_name',
                'rejected_at_formatted',
                'item_out_by_name',
                'item_out_at_formatted',
                'filed_by_name',
                'filed_at_formatted',
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                $this->_model->dtViewPrfItems(),
                $this->_model->dtPRFStatusFormat(),
                'id',
                'job_order_id',
                'quotation_num',
                'customer_name',
                'work_type',
                'process_date_formatted',
                'remarks',
                'created_by_name',
                'created_at_formatted',
                'accepted_by_name',
                'accepted_at_formatted',
                'rejected_by_name',
                'rejected_at_formatted',
                'item_out_by_name',
                'item_out_at_formatted',
                'filed_by_name',
                'filed_at_formatted',
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
            'message'   => 'PRF has been saved successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id     = $this->request->getVar('id');
                $inv_id = $this->request->getVar('inventory_id');
                $q_out  = $this->request->getVar('quantity_out');
                $bool   = $this->traitIsStocksLessThanQuantityOut(
                    $this->request->getVar('item_available'),
                    $q_out
                );

                if ($bool) {
                    throw new \Exception("There is/are item(s)'s <strong>available stocks</strong> are less than the <strong>quantity out</strong>!", 2);
                } else {
                    $inputs = [
                        'id'            => $id,
                        'job_order_id'  => $this->request->getVar('job_order_id'),
                        'process_date'  => $this->request->getVar('process_date'),
                        'inventory_id'  => (isset($inv_id) && !has_empty_value($inv_id)) 
                            ? (!has_empty_value($q_out) && count($inv_id) === count($q_out) ? $inv_id : null) 
                            : null,
                        'quantity_out'  => !has_empty_value($q_out) ? $q_out : null,
                    ];

                    if (! $this->_model->save($inputs)) {
                        $data['errors']     = $this->_model->errors();
                        $data['status']     = STATUS_ERROR;
                        $data['message']    = "Validation error!";
                    } else {
                        $prfItemModel   = new PRFItemModel();
                        $prf_id         = $id ? $id : $this->_model->insertID();
                        $prfItemModel->savePrfItems($this->request->getVar(), $prf_id);
                    }

                    if ($id) {
                        $data['message']    = 'PRF has been updated successfully!';
                    }
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
            'message'   => 'PRF has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id  = $this->request->getVar('id');
                if ($this->request->getVar('prf_items')) {                
                    $data['data']       = $this->traitFetchPrfItems($id, true, true);
                    $data['message']    = 'PRF items has been retrieved!';
                } else {
                    $table          = $this->_model->table;
                    $columns        = "{$table}.id, {$table}.job_order_id, {$table}.process_date";
                    
                    $record         = $this->_model->getProjectRequestForms($id, true, $columns);
                    $job_order      = $this->fetchJobOrders($record['job_order_id'], []);
                    $items          = $this->traitFetchPrfItems($id, true);

                    $data['data']               = $record;
                    $data['data']['job_order']  = $job_order[0];
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
                $status = set_prf_status($this->request->getVar('status'));
                $inputs = ['status' => $status];

                if (null !== $this->request->getVar('remarks'))
                    $inputs['remarks'] = trim($this->request->getVar('remarks'));

                if (in_array($status, ['accepted', 'item_out'])) {
                    if ($this->checkPrfItemsOutNStocks($id))
                        throw new \Exception("There is/are item(s)'s <strong>available stocks</strong> are less than the <strong>quantity out</strong>!", 2);
                }

                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    $data['status']     = STATUS_SUCCESS;
                    $data['message']    = 'PRF has been '. strtoupper($status) .' successfully!';

                    if ($status === 'filed') {
                        $prfItemModel = new PRFItemModel();
                        $prfItemModel->updatePrfItems($this->request->getVar(), $id);
                    }
                }
                return $data;
            }, 
            false
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
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);
        
        $id             = $this->request->getUri()->getSegment(3);
        $columns        = $this->_model->columns(true, true);
        $columns       .= ','. $this->_model->jobOrderColumns(false, true);
        $builder        = $this->_model->select($columns);
        $this->_model->joinView($builder);
        $this->_model->joinJobOrder($builder);
        
        $data['prf']        = $builder->find($id);
        $data['prf_items']  = $builder->traitFetchPrfItems($id, true, true);
        $data['title']      = 'Print Project Request Form';
        // d($data['prf']); 
        // d($data['prf_items']); 
        // die;

        return view('inventory/prf/print', $data);
    }
}
