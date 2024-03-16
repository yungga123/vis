<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DispatchModel;
use App\Models\DispatchedTechniciansModel;
use App\Models\ScheduleModel;
use App\Models\JobOrderModel;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use monken\TablesIgniter;

class Dispatch extends BaseController
{
    /* Declare trait here to use */
    use GeneralInfoTrait, HRTrait;

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
        $this->_model       = new DispatchModel(); // Current model
        $this->_module_code = MODULE_CODES['dispatch']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
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
        
        $data['title']          = 'Dispatch List';
        $data['page_title']     = 'Dispatch List';
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Dispatch';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['moment']         = true;
        $data['custom_js']      = ['admin/dispatch/index.js', 'dt_filter.js'];
        $data['routes']         = json_encode([
            'dispatch' => [
                'list'      => url_to('dispatch.list'),
                'save'      => url_to('dispatch.save'),
                'fetch'     => url_to('dispatch.fetch'),
                'delete'    => url_to('dispatch.delete'),
            ],
            'admin' => [
                'common' => [
                    'schedules' => url_to('admin.common.schedules'),
                ]
            ],
            'clients' => [
                'common' => [
                    'customers' => url_to('clients.common.customers'),
                ]
            ],
            'employee' => [
                'common' => [
                    'search'    => url_to('employee.common.search'),
                ]
            ],
        ]);
        $data['php_to_js_options'] = json_encode([
            'employees'     => get_employees(),
            'schedule_type' => get_schedule_type(),
        ]);

        return view('admin/dispatch/index', $data);
    }

    /**
     * Get list of records
     *
     * @return array|dataTable
     */
    public function list()
    {
        $scheduleModel  = new ScheduleModel();
        $table          = new TablesIgniter();
        $request        = $this->request->getVar();
        $builder        = $this->_model->noticeTable($request);
        $fields         = [
            'id',
            'schedule_id',
            'schedule',
            'description',
            'dispatch_date',
            'dispatch_out',
            'time_in',
            'time_out',
            'sr_number',
            'technicians_formatted',
        ];
        $fields1        = [
            'with_permit',
            'comments',
            'remarks',
            'checked_by_name',
            'dispatched_by',
            'dispatched_at',
        ];

        $table->setTable($builder)
            ->setSearch([
                "{$this->_model->table}.id",
                "{$this->_model->table}.schedule_id",
                "{$scheduleModel->table}.id",
                "{$scheduleModel->table}.title",
                "{$this->_model->view}.technicians",
                "{$this->_model->table}.sr_number",
                "{$this->_model->view}.dispatched_by",
                "{$this->_model->view}.checked_by_name",
            ])
            ->setOrder(array_merge([null, null], $fields, [null], $fields1))
            ->setOutput(
                array_merge(
                    [dt_empty_col(), $this->_model->buttons($this->_permissions)], 
                    $fields,
                    [$this->_model->serviceTypeFormat()], 
                    $fields1
                )
            );

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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Dispatch')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $action = ACTION_ADD;
                $id     = $this->request->getVar('id');
                $inputs = [
                    'schedule_id'   => $this->request->getVar('schedule_id'),
                    'customer_type' => $this->request->getVar('customer_type'),
                    'sr_number'     => $this->request->getVar('sr_number'),
                    'dispatch_date' => $this->request->getVar('dispatch_date'),
                    'dispatch_out'  => $this->request->getVar('dispatch_out'),
                    'time_in'       => $this->request->getVar('time_in'),
                    'time_out'      => $this->request->getVar('time_out'),
                    'remarks'       => $this->request->getVar('remarks'),
                    'service_type'  => $this->request->getVar('service_type'),
                    'comments'      => $this->request->getVar('comments'),
                    'with_permit'   => $this->request->getVar('with_permit'),
                    'technicians'   => $this->request->getVar('technicians'),
                    'checked_by'    => $this->request->getVar('checked_by'),
                    'created_by'    => session('username'),
                ];

                if (! empty($id)) {
                    $action             = ACTION_EDIT;
                    $inputs['id']       = $id;
                    $data['message']    = res_lang('success.updated', 'Dispatch');

                    unset($inputs['created_by']);
                } 

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
                
                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    $dispatch_id    = !empty($id) ? $id : $this->_model->insertID();
                    $dTModel        = new DispatchedTechniciansModel();
                    $dTModel->saveDispatchedTechnicians(
                        $dispatch_id,
                        $this->request->getVar('technicians')
                    );
                }
                return $data;
            },
            true
        );

        return $response;
    }
    
    /**
     * For getting the record using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.retrieved', 'Dispatch')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $dTModel    = new DispatchedTechniciansModel();

                $data['data']                   = $this->_model->getDispatch($id, false, true);
                $data['data']['technicians']    = $dTModel->getDispatchedTechnicians($id);
                
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
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Dispatch')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $this->checkRoleActionPermissions($this->_module_code, ACTION_DELETE, true);
                
                $id = $this->request->getVar('id');

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');
                } else {
                    log_msg(
                        $data['message']. " Dispatch #: {$id} \nDeleted by: {username}",
                        ['username' => session('username')]
                    );
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

        $dispatch = $this->_model->getDispatch($id, false, true);

        // For restriction
        if (empty($dispatch)) {
            return $this->redirectTo404Page();
        }

        // Get client details
        $joModel    = new JobOrderModel();
        $client     = $joModel->getClientInfo($dispatch['job_order_id'], '', true);
        
        $data['dispatch']       = $dispatch;
        $data['client']         = $client;
        $data['title']          = 'Print Dispatch';
        $data['company_logo']   = $this->getCompanyLogo();

        return view('admin/dispatch/print', $data);
    }
}
