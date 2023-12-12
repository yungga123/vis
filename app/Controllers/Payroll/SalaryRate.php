<?php

namespace App\Controllers\Payroll;

use App\Controllers\BaseController;
use App\Models\SalaryRateModel;
use monken\TablesIgniter;

class SalaryRate extends BaseController
{
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
        $this->_model           = new SalaryRateModel(); // Current model
        $this->_module_code     = MODULE_CODES['salary_rates']; // Current module
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
        $this->checkRolePermissions($this->_module_code);

        $data['title']          = 'Employee Salary Rates';
        $data['page_title']     = 'Employee Salary Rates';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['exclude_toastr'] = true;
        $data['select2']        = true;
        $data['custom_js']      = 'payroll/salary_rate/index.js';
        $data['routes']         = json_encode([
            'salary_rate' => [
                'list'      => url_to('salary_rate.list'),
                'fetch'     => url_to('salary_rate.fetch'),
                'delete'    => url_to('salary_rate.delete'),
            ],
        ]);

        return view('payroll/salary_rate/index', $data);
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
        $fields     = [            
            'id',
            'new_client',
            'name',
            'type',
            'contact_person',
            'contact_number',
            'telephone',
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
            'message'   => res_lang('success.added', 'Salary Rate')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $action         = ACTION_ADD;
                $request        = $this->request->getVar();
                $contact_number = '';
    
                if ($this->request->getVar('id')) {
                    $action             = ACTION_EDIT;
                    $data['message']    = res_lang('success.updated', 'Salary Rate');
                }

                $this->checkRoleActionPermissions($this->_module_code, $action, true);

                if (! $this->_model->save($request)) {
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
     * For getting the item data using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Salary Rate')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $record         = $this->_model->select($this->_model->allowedFields)
                    ->where('id', $id)->first();
                    

                $data['data']   = $record;
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
            'message'   => res_lang('success.deleted', 'Salary Rate')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);

                if (! $this->_model->delete($this->request->getVar('id'))) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                }
                return $data;
            }
        );

        return $response;
    }
}
