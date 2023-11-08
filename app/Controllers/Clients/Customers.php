<?php

namespace App\Controllers\Clients;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Traits\ExportTrait;
use monken\TablesIgniter;

class Customers extends BaseController
{
    /* Declare trait here to use */
    use ExportTrait;

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
        $this->_model       = new CustomerModel(); // Current model
        $this->_module_code = MODULE_CODES['customers']; // Current module
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

        $data['title']          = 'Clients Masterlist';
        $data['page_title']     = 'Clients Masterlist';
        $data['can_add']        = $this->_can_add;
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        $data['custom_js']      = ['customer/index.js', 'customer/branch.js'];
        $data['btn_add_lbl']    = 'Add New Client';
        $data['routes']         = json_encode([
            'customer' => [
                'list'      => url_to('customer.list'),
                'fetch'     => url_to('customer.fetch'),
                'delete'    => url_to('customer.delete'),
                'branch' => [
                    'list'      => url_to('customer.branch.list'),
                    'fetch'     => url_to('customer.branch.fetch'),
                    'delete'    => url_to('customer.branch.delete'),
                ],
            ],
        ]);

        return view('customer/index', $data);
    }

    /**
     * Get list of customers
     *
     * @return array|dataTable
     */
    public function list()
    {
        $table  = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
                "{$this->_model->table}.name",
                "{$this->_model->table}.contact_person",
                "{$this->_model->table}.province",
                "{$this->_model->table}.city",
                "{$this->_model->table}.subdivision",
            ])
            ->setDefaultOrder("id",'desc')
            ->setOrder([
                null,
                'new_client',
                'type',
                'id',
                'name',
                'contact_person',
                'contact_number',
                'email_address',
                'address',
                'source',
                'notes',
                'referred_by',
                'created_by',
                'created_at'
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                'new_client',
                'type',
                'id',
                'name',
                'contact_person',
                'contact_number',
                'email_address',
                'address',
                'source',
                'notes',
                'referred_by',
                'created_by',
                'created_at'
            ]);
        
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been saved successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->_model->save($this->request->getVar())) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = 'Validation error!';
                }
    
                if ($this->request->getVar('id')) {
                    $data['message']    = 'Customer has been updated successfully!';
                }
                return $data;
            },
            true
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $data['data']   = $this->_model->select($this->_model->allowedFields)->find($id);;
                return $data;
            }
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Customer has been deleted successfully!'
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
            },
            true
        );

        return $response;
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        log_message('error', 'Export to csv');
        try {
            $datetimeFormat = dt_sql_datetime_format();
            $address    = dt_sql_concat_client_address();
            $columns    = "
                {$this->_model->table}.id,
                IF({$this->_model->table}.forecast = 0, 'NO', 'YES') AS new_client,
                {$this->_model->table}.name,
                {$this->_model->table}.type,
                {$this->_model->table}.contact_person,
                {$this->_model->table}.contact_number,
                {$this->_model->table}.email_address,
                {$address},
                {$this->_model->table}.source, 
                {$this->_model->table}.notes,
                {$this->_model->table}.referred_by,
                DATE_FORMAT({$this->_model->table}.created_at, '{$datetimeFormat}') AS created_at,
                {$this->_model->accountsView}.employee_name AS created_by
            ";
            $builder    = $this->_model->select($columns);
            $builder->join($this->_model->accountsView, "{$this->_model->table}.created_by = {$this->_model->accountsView}.username", 'left');
            $builder->where("deleted_at IS NULL")->orderBy('id', 'DESC');

            $data       = $builder->findAll();
            $header     = [
                'Client ID',
                'New Client?',
                'Client Name',
                'Client Type',
                'Contact Person',
                'Contact Number',
                'Email Address',
                'Address',
                'Source',
                'Notes',
                'Referred By',
                'Created By',
                'Created At',
            ];
            $filename   = 'Clients Masterlist';
            $this->exportToCsv($data, $header, $filename);
        } catch (\Exception $e) {
            log_message('error', '[EXPORT ERROR] {exception}', ['exception' => $e]);
        }
    }
}
