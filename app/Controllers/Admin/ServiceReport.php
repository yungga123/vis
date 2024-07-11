<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerBranchModel;
use App\Models\CustomerModel;
use App\Models\JobOrderModel;
use App\Models\ServiceReportItemModel;
use App\Models\ServiceReportModel;
use App\Models\ServiceReportUnitModel;
use App\Traits\CommonTrait;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class ServiceReport extends BaseController
{
    /* Declare trait here to use */
    use HRTrait, CommonTrait, GeneralInfoTrait;

    /**
     * Use to initialize JobOrderModel class
     * @var object
     */
    private $_model;

    /**
     * Use to get current module code
     * @var string
     */
    private $_module_code;

    /**
     * Use to get current module title
     * @var string
     */
    private $_module_title;
    
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
        $this->_model           = new ServiceReportModel(); // Current model
        $this->_module_code     = MODULE_CODES['service_reports']; // Current module
        $this->_module_title    = MODULES[$this->_module_code]; // Current module title
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

        $title = $this->_module_title .' List';

        // Make singular
        $this->_module_title = $this->_module_title;

        $data['title']          = $title;
        $data['page_title']     = $title;
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add '. $this->_module_title;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = [
            'admin/service_report/index.js', 
            'customer/common.js', 
            'admin/common.js', 
            'dt_filter.js',
        ];
        $data['routes']         = json_encode([
            'service_report' => [
                'list'      => url_to('admin.service_report.list'),
                'save'      => url_to('admin.service_report.save'),
                'fetch'     => url_to('admin.service_report.fetch'),
                'delete'    => url_to('admin.service_report.delete'),
                'change'    => url_to('admin.service_report.change'),
            ],
            'admin' => [
                'common' => [
                    'job_orders' => url_to('admin.common.job_orders'),
                ]
            ],
            'clients' => [
                'common' => [
                    'customers'         => url_to('clients.common.customers'),
                    'customer_branches' => url_to('clients.common.customer.branches'),
                ]
            ],
        ]);
        $data['php_to_js_options'] = json_encode([
            'unitc_items' => get_unit_condition_items(),
        ]);

        return view('admin/service_report/index', $data);
    }

    /**
     * Get list of job orders
     *
     * @return array|dataTable
     */
    public function list()
    {
        $joModel    = new JobOrderModel();
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);
        $fields     = [
            'id',
            'job_order_id',
            'client_name',
            'client_branch_name',
            'area',
            'serial_number',
            'server_type',
            'service_type',
            'arrival_at',
            'error_description',
            'corrective_action',
            'remarks',
            'labor',
            'travel_time',
            'time_out',
            'created_by',
            'created_at',
            'accepted_by',
            'accepted_at',
            'filed_by',
            'filed_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$joModel->view}.client_name",
                "{$joModel->view}.client_branch_name",
                "{$this->_model->table}.serial_number",
                "{$this->_model->table}.server_type",
                "{$this->_model->table}.error_description",
                "{$this->_model->table}.corrective_action",
                "{$this->_model->table}.remarks",
            ])
            ->setOrder(array_merge([null, null, null, null], $fields))
            ->setOutput(
                array_merge(
                    [
                        dt_empty_col(),
                        $this->_model->buttons($this->_permissions),
                        $this->_model->dtViewUnitNItems(),
                        $this->_model->dtStatusFormat(),
                    ], 
                    $fields
                )
            );

        return $table->getDatatable();
    }

    /**
     * Saving process of job order (inserting and updating job order)
     *
     * @return json
     */
    public function save() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', $this->_module_title)
        ];

        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $action         = ACTION_ADD;
                $request        = $this->request->getVar();

                $id             = $request['id'];
                $arrival_at     = $this->_formatArrivalAt($request['arrival_date'], $request['arrival_time']);
                $inputs         = [
                    'id'                    => $request['id'],
                    'job_order_id'          => $request['job_order_id'] ?? null,
                    'serial_number'         => $request['serial_number'],
                    'server_type'           => $request['server_type'],
                    'service_type'          => $request['service_type'],
                    'area'                  => $request['area'],
                    'error_description'     => $request['error_description'],
                    'corrective_action'     => $request['corrective_action'],
                    'remarks'               => $request['remarks'],
                    'labor'                 => $request['labor'],
                    'travel_time'           => $request['travel_time'],
                    'time_out'              => $request['time_out'],
                    'arrival_at'            => $arrival_at,
                ];
    
                if (! empty($id)) {
                    $action                 = ACTION_EDIT;
                    $data['message']        = res_lang('success.updated', $this->_module_title);
                } else {
                    if (empty($request['item'][0])) {
                        $exception = 'Unit codition is required. Please add at least one item!';

                        throw new \Exception($exception, 2);
                    }
                }

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
                $this->checkRecordRestrictionViaStatus($id, $this->_model);
    
                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $service_report_id  = $id ? $id : $this->_model->insertID();
                    $unitModel          = new ServiceReportUnitModel();
                    $itemModel          = new ServiceReportItemModel();

                    $unitModel->saveUnits($request, $service_report_id);
                    $itemModel->saveItems($request, $service_report_id);
                }

                return $data;
            }
        );

        return $response;
    }
    
    /**
     * For getting the job order data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', $this->_module_title)
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id  = $this->request->getVar('id');

                if (! $this->_model->exists($id)) {
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "<strong>{$this->_module_title} #: {$id}</strong> doesn't exists anymore!";

                    return $data;
                }
                
                $unitModel = new ServiceReportUnitModel();
                $itemModel = new ServiceReportItemModel();

                $units     = $unitModel->fetchUnits($id);
                $items     = $itemModel->fetchItems($id);

                if ($this->request->getVar('items')) {
                    $data['data']       = [
                        'units' => $units,
                        'items' => $items,
                    ];
                    $data['message']    = res_lang('success.retrieved', "{$this->_module_title} Units & Items");
                } else {
                    $record     = $this->_model->fetch($id, true);
                    $arrival_at = $this->_formatArrivalAt($record['arrival_at']);

                    $record['arrival_date'] = $arrival_at[0];
                    $record['arrival_time'] = $arrival_at[1] . ':00';
                    $data['data']           = $record;
                    $data['data']['units']  = $units;
                    $data['data']['items']  = $items;
                }

                return $data;
            },
            false
        );

        return $response;
    }

    /**
     * Deleting job order
     *
     * @return json
     */
    public function delete() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', $this->_module_title)
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
                } else {
                    log_msg(
                        $data['message']. " Job Order #: {$id} \nDeleted by: {username}",
                        ['username' => session('username')]
                    );
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
                $status = $this->request->getVar('status');

                $pasttense  = set_prf_status($status);
                $inputs     = ['status' => $pasttense];

                $this->checkRoleActionPermissions($this->_module_code, $status, true);

                if (! $this->_model->update($id, $inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $data['status']     = res_lang('status.success');
                    $data['message']    = res_lang('success.changed', [$this->_module_title, strtoupper($pasttense)]);
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
    public function print($id) 
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code, ACTION_PRINT);
        
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $joModel        = new JobOrderModel();
        $unitModel      = new ServiceReportUnitModel();
        $itemModel      = new ServiceReportItemModel();
        $columns        = "
            {$this->_model->table}.id,
            {$this->_model->table}.status,
            {$this->_model->table}.job_order_id,
            {$joModel->view}.client_id,
            {$joModel->view}.client_name,
            {$customerModel->table}.contact_person AS client_contact_person,
            {$customerModel->table}.contact_number AS client_contact_number,
            ".dt_sql_concat_client_address($customerModel->table, '')." AS client_address,
            {$customerModel->table}.telephone AS client_telephone,
            {$joModel->view}.client_branch_id,
            {$joModel->view}.client_branch_name,
            {$branchModel->table}.contact_person AS client_branch_contact_person,
            {$branchModel->table}.contact_number AS client_branch_contact_number,
            ".dt_sql_concat_client_address($branchModel->table, '')." AS client_branch_address,
            {$this->_model->table}.serial_number,
            {$this->_model->table}.server_type,
            {$this->_model->table}.service_type,
            {$this->_model->table}.area,
            {$this->_model->table}.arrival_at,
            {$this->_model->table}.error_description,
            {$this->_model->table}.corrective_action,
            {$this->_model->table}.remarks,
            {$this->_model->table}.labor,
            {$this->_model->table}.travel_time,
            {$this->_model->table}.time_out,
            {$this->_model->table}.created_at,
            cb.employee_name AS created_by,
        ";

        $builder        = $this->_model->select($columns);

        $this->_model->joinJobOrders($builder, $joModel, true);
        $this->_model->joinCustomers($builder, "{$joModel->view}.client_id", "{$joModel->view}.client_branch_id");
        $this->joinAccountView($builder, 'created_by', 'cb');

        $builder->where("{$this->_model->table}.id", $id);

        $service_report = $builder->first($id);

        // For restriction
        if (empty($service_report)) {
            return $this->redirectTo404Page();
        }

        $form_code = $this->getGeneralInfo('service_report_form_code');
        $form_code = empty($form_code) ? COMPANY_SERVICE_REPORT_FORM_CODE : $form_code;
        
        $data['service_report'] = $service_report;
        $data['units']          = $unitModel->fetchUnits($id);
        $data['items']          = $itemModel->fetchItems($id);
        $data['title']          = 'Print '. $this->_module_title;
        $data['form_code']      = $form_code;
        $data['company_info']   = $this->getCompanyInfo();

        return view('admin/service_report/print', $data);
    }

    /**
     * Format arrival date and time
     *
     * @param string        Arrival date or date & time
     * @param string|null   Arrival time or null
     * 
     * @return array|string
     */
    private function _formatArrivalAt($date_or_datetime, $time = null) 
    {
        if (is_null($time)) {
            return [
                format_date($date_or_datetime, 'Y-m-d'), 
                format_time($date_or_datetime, 'H:i')
            ];
        }

        $arrival_at = $date_or_datetime . ' ' . $time;

        return format_datetime($arrival_at, 'Y-m-d H:i:s');
    }
}
