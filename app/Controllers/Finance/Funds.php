<?php

namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\FundsHistoryModel;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class Funds extends BaseController
{
    /* Declare trait here to use */
    use HRTrait, GeneralInfoTrait;

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
    private $_can_release;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_model           = new FundsHistoryModel(); // Current model
        $this->_module_code     = MODULE_CODES['funds']; // Current module
        $this->_permissions     = $this->getSpecificPermissions($this->_module_code);
        $this->_can_release     = $this->checkPermissions($this->_permissions, 'RELEASE');
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

        $data['title']          = 'Finance | Funds';
        $data['page_title']     = 'Finance | Funds';
        $data['btn_add_lbl']    = 'Release Fund';
        $data['can_release']    = $this->_can_release;
        $data['funds']          = $this->getCompanyFunds();
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['toastr']         = true;
        $data['select2']        = true;
        $data['custom_js']      = ['finance/funds/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'funds' => [
                'list'      => url_to('finance.funds.list'),
                'fetch'     => url_to('finance.funds.fetch'),
            ],
        ]);

        return view('finance/funds/index', $data);
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
            'transaction_amount',
            'previous_funds',
            'current_funds',
            'coming_from',
            'expenses',
            'remarks',
            'created_by',
            'created_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
            ])
            ->setOrder(array_merge([null], $fields))
            ->setOutput(array_merge(
                [$this->_model->dtTransactionTypeFormat()],
                $fields
            ));
        
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
            'message'   => res_lang('success.submitted', 'Data')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, 'RELEASE', true);

                $request    = $this->request->getVar();
                $amount     = floatval($request['transaction_amount'] ?? 0);
                $inputs     = [
                    'current_funds'         => $request['current_funds'],
                    'expenses'              => $request['expenses'],
                    'transaction_amount'    => $amount,
                    'remarks'               => $request['remarks'],
                    'transaction_type'      => 'outgoing',
                    'coming_from'           => 'Expenses',
                ];

                if ($this->getCompanyFunds() < $amount) {
                    throw new \Exception("Can't release fund! Current fund is less than the release amount.", 1);
                }

                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    // Update/deduct funds
                    $this->saveCompanyFunds($amount, false);
                }

                // Get current funds
                $data['funds'] = $this->getCompanyFunds();

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
            'message'   => res_lang('success.retrieved', 'Billing Invoice')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $record     = $this->_model->fetch($id, true);

                $data['data'] = $record;
                return $data;
            },
            false
        );

        return $response;
    }
}
