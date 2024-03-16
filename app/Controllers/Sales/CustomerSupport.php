<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\CustomerSupportModel;
use App\Models\CustomerSupportSpecialistModel;
use App\Traits\CommonTrait;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class CustomerSupport extends BaseController
{
    /* Declare trait here to use */
    use CommonTrait, HRTrait, GeneralInfoTrait;

    /**
     * Use to initialize model class
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
     * @var array
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
        $this->_model           = new CustomerSupportModel(); // Current model
        $this->_module_code     = MODULE_CODES['customer_supports']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    /**
     * Display the view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_VIEW);

        $data['title']          = 'Sales | Customer Supports';
        $data['page_title']     = 'Sales | Customer Supports';
        $data['btn_add_lbl']    = 'Add a Record';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = ['sales/customer_support/index.js', 'dt_filter.js', 'customer/common.js'];
        $data['routes']         = json_encode([
            'customer_support' => [
                'list'      => url_to('sales.customer_support.list'),
                'fetch'     => url_to('sales.customer_support.fetch'),
                'delete'    => url_to('sales.customer_support.delete'),
            ],
            'clients' => [
                'common' => [
                    'customers'         => url_to('clients.common.customers'),
                    'customer_branches' => url_to('clients.common.customer.branches'),
                ]
            ],
            'employee' => [
                'common' => [
                    'search'            => url_to('employee.common.search'),
                ]
            ],
        ]);
        $data['php_to_js_options'] = json_encode([
            'status' => get_customer_support_status(),
        ]);

        return view('sales/customer_support/index', $data);
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
        $builder    = $this->_model->noticeTable($request, $this->_permissions);
        $fields     = [
            'id',
            'client_name',
            'client_branch_name',
            'ticket_number',
            'security_ict_system',
            'priority',
            'due_date',
            'follow_up_date',
            'issue',
            'findings',
            'action',
            'troubleshooting',
            'remarks',
            'specialists_formatted',
            'created_by',
            'created_at',
            'done_by',
            'done_at',
            'turn_over_by',
            'turn_over_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
                "{$this->_model->table}.ticket_number",
                "{$this->_model->view}.client_name",
            ])
            ->setOrder(array_merge([null, null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(), 
                        $this->_model->buttons($this->_permissions),
                        $this->_model->dtStatusFormat(),
                    ], 
                    $fields
                )
            );
        
        return $table->getDatatable();

    }

    /**
     * For saving data
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Customer Support')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $request        = $this->request->getVar();
                $id             = $request['id'];
                $action         = empty($id) ? ACTION_ADD : ACTION_EDIT;
                $security       = $request['security_ict_system'] ?? null;
                $inputs         = [
                    'id'                        => $id,
                    'customer_id'               => $request['customer_id'] ?? null,
                    'customer_branch_id'        => $request['customer_branch_id'] ?? null,
                    'ticket_number'             => $request['ticket_number'] ?? null,
                    'issue'                     => $request['issue'] ?? null,
                    'findings'                  => $request['findings'] ?? null,
                    'action'                    => $request['action'] ?? null,
                    'troubleshooting'           => $request['troubleshooting'] ?? null,
                    'security_ict_system'       => $security,
                    'security_ict_system_other' => $request['security_ict_system_other'] ?? null,
                    'priority'                  => $request['priority'] ?? null,
                    'due_date'                  => $request['due_date'] ?? null,
                    'follow_up_date'            => $request['follow_up_date'] ?? null,
                    'remarks'                   => $request['remarks'] ?? null,
                ];

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                if ($security === 'OTHER') {
                    $this->_model->makeSecICTSystemRequired();
                }
    
                if ($id) {
                    $data['message']    = res_lang('success.updated', 'Customer Support');
                }

                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $model  = new CustomerSupportSpecialistModel();
                    $_id    = $id ? $id : $this->_model->insertID();
                    
                    $model->saveSpecialists($request['specialists'], $_id);
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * For getting the item data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Customer Support')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $model      = $this->_model;
                $id         = $this->request->getVar('id');

                if ($this->request->getVar('status')) {
                    $model->select('remarks');
                    $model->where($model->primaryKey, $id);

                    $record = $model->first();
                } else {
                    $columns    = array_map(
                        function ($column) use ($model) {
                            return $model->table .'.'. $column;
                        }, 
                        $model->allowedFields
                    );
                    $columns[]  = $model->view .'.client_name';
                    $columns[]  = $model->view .'.customer_type';
                    $columns[]  = $model->view .'.client_branch_name';
                    $columns[]  = $model->view .'.specialist_ids';
                    $columns[]  = $model->view .'.specialists';
    
                    $model->select($columns);
                    $model->joinView();
                    $model->where($model->table .'.'. $model->primaryKey, $id);
    
                    $record     = $model->first();
                    $spec_ids   = empty($record['specialist_ids']) ? null : explode(',', $record['specialist_ids']);
                    $specialists = empty($record['specialists']) ? null : explode(',', $record['specialists']);
    
                    $record['specialist_ids']   = $spec_ids;
                    $record['specialists']      = $specialists;
                }
                

                $data['data'] = $record;

                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Deleting record
     *
     * @return json
     */
    public function delete() 
    {
        $data = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Customer Support')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model);

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }
                return $data;
            }
        );

        return $response;
    }

    /**
     * Changing status of Customer Support
     *
     * @return json
     */
    public function change() 
    {
        $data       = [];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $status     = $this->request->getVar('status');
                $inputs     = [
                    'status'        => $this->request->getVar('status'),
                    'remarks'       => $this->request->getVar('remarks'),
                ];

                $this->checkRoleActionPermissions($this->_module_code, 'CHANGE', true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model);
    
                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $data['status']     = res_lang('status.success');
                    $data['message']    = res_lang('success.saved');
                }

                return $data;
            }
        );

        return $response;
    }
}