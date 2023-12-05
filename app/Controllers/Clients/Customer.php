<?php

namespace App\Controllers\Clients;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Traits\ExportTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class Customer extends BaseController
{
    /* Declare trait here to use */
    use ExportTrait, HRTrait;

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
        $this->_model           = new CustomerModel(); // Current model
        $this->_module_code     = MODULE_CODES['customers']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add         = $this->checkPermissions($this->_permissions, 'ADD');
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
        $data['dropzone']       = true;
        $data['custom_js']      = [
            'customer/index.js', 
            'customer/branch.js', 
            'dt_filter.js', 
            'dropzone.js'
        ];
        $data['btn_add_lbl']    = 'Add New Client';
        $data['routes']         = json_encode([
            'customer' => [
                'list'      => url_to('customer.list'),
                'fetch'     => url_to('customer.fetch'),
                'delete'    => url_to('customer.delete'),
                'files'     => [
                    'fetch'     => site_url('clients/files'),
                    'upload'    => url_to('customer.files.upload'),
                    'download'  => site_url('clients/files/download'),
                    'remove'    => url_to('customer.files.remove'),
                ],
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
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);
        $fields     = [            
            'id',
            'new_client',
            'name',
            'type',
            'contact_person',
            'contact_number',
            'email_address',
            'address',
            'source',
            'notes',
            'referred_by',
            'created_by',
            'created_at'
        ];

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
            ->setOrder(array_merge([null, null], $fields))
            ->setOutput(
                array_merge(
                    [dt_empty_col(), $this->_model->buttons($this->_permissions)], 
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
            'message'   => res_lang('success.saved', 'Client')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->_model->save($this->request->getVar())) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }
    
                if ($this->request->getVar('id')) {
                    $data['message']    = res_lang('success.updated', 'Client');
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Client')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $data['data']   = $this->_model->select($this->_model->allowedFields)->find($id);;
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
            'message'   => res_lang('success.deleted', 'Client')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                if (! $this->_model->delete($this->request->getVar('id'))) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }
                return $data;
            },
            true
        );

        return $response;
    }
}
