<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DispatchModel;
use App\Models\DispatchedTechniciansModel;
use App\Models\ScheduleModel;
use App\Models\CustomerModel;
use App\Traits\GeneralInfoTrait;
use App\Traits\HRTrait;
use App\Traits\ExportTrait;
use monken\TablesIgniter;

class Dispatch extends BaseController
{
    /* Declare trait here to use */
    use GeneralInfoTrait, ExportTrait, HRTrait;

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
        $this->_model       = new DispatchModel(); // Current model
        $this->_module_code = MODULE_CODES['dispatch']; // Current module
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
        
        $data['title']          = 'Dispatch List';
        $data['page_title']     = 'Dispatch List';
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = 'Add Dispatch';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['moment']         = true;
        $data['custom_js']      = ['admin/dispatch/index.js', 'admin/common.js'];
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
                    'customers' => url_to('admin.common.customers'),
                ]
            ]
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
        $customerModel  = new CustomerModel();
        $table          = new TablesIgniter();
        $request        = $this->request->getVar();
        $builder        = $this->_model->noticeTable($request);
        $fields         = [
            'id',
            'schedule_id',
            'schedule',
            'customer_name',
            'customer_type',
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
                "{$scheduleModel->table}.title",
                "{$this->_model->view}.technicians",
                "{$this->_model->table}.service_type",
                "{$this->_model->table}.sr_number",
                "{$this->_model->view}.dispatched_by",
                "{$this->_model->view}.checked_by_name",
                "{$customerModel->table}.name",
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Dispatch has been added successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id     = $this->request->getVar('id');
                $inputs = [
                    'schedule_id'   => $this->request->getVar('schedule_id'),
                    'customer_id'   => $this->request->getVar('customer_id'),
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
                    $inputs['id']       = $id;
                    $data['message']    = 'Dispatch has been updated successfully!';

                    unset($inputs['created_by']);
                } 
                
                if (! $this->_model->save($inputs)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Dispatch has been retrieved!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id         = $this->request->getVar('id');
                $dTModel    = new DispatchedTechniciansModel();

                $data['data']                   = $this->_model->getDispatch($id, false, true, true);
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'Dispatch has been deleted successfully!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id = $this->request->getVar('id');

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    log_message('error', 'Deleted by {username}', ['username' => session('username')]);
                }

                return $data;
            },
            true
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
        
        $id                     = $this->request->getUri()->getSegment(3);
        $data['dispatch']       = $this->_model->getDispatch($id, false, false, true);
        $data['title']          = 'Print Dispatch';
        $data['company_logo']   = $this->getGeneralInfo('company_logo');

        return view('admin/dispatch/print', $data);
    }

    /**
     * For exporting data to csv
     *
     * @return void
     */
    public function export() 
    {
        $scheduleModel  = new ScheduleModel();
        $customerModel  = new CustomerModel();
        $columns        = "
            {$this->_model->table}.id,
            {$this->_model->table}.schedule_id,
            {$scheduleModel->table}.title,
            {$customerModel->table}.name AS client,
            {$customerModel->table}.type AS client_type,
            ".dt_sql_date_format("{$this->_model->table}.dispatch_date")." AS dispatch_date,
            ".dt_sql_time_format("{$this->_model->table}.dispatch_out")." AS dispatch_out,
            ".dt_sql_time_format("{$this->_model->table}.time_in")." AS time_in,
            ".dt_sql_time_format("{$this->_model->table}.time_out")." AS time_out,
            {$this->_model->table}.sr_number,
            {$this->_model->view}.technicians,
            {$this->_model->table}.service_type,
            {$this->_model->table}.with_permit,
            {$this->_model->table}.comments,
            {$this->_model->table}.remarks,
            {$this->_model->view}.checked_by_name,
            {$this->_model->view}.dispatched_by,
            ".dt_sql_datetime_format("{$this->_model->table}.created_at")." AS dispatched_at
        ";
        $builder    = $this->_model->select($columns);

        // Join with other tables
        $this->_model->joinView($builder);
        $this->_model->joinSchedule($builder);
        $this->_model->joinCustomer($builder);
        $builder->orderBy("{$this->_model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Dispatch ID',
            'Schedule ID',
            'Schedule Title',
            'Client',
            'Client Type',
            'Dispatch Date',
            'Dispatch Out',
            'Time In',
            'Time Out',
            'SR Number',
            'Technicians',
            'Service Type',
            'With Permit',
            'Comments',
            'Remarks',
            'Checked By',
            'Dispatched By',
            'Dispatched At'
        ];
        $filename   = 'Dispatch Masterlist';

        $this->exportToCsv($data, $header, $filename, function($data, $output) {
            $i          = 0;
            $services   = get_dispatch_services();
            while (isset($data[$i])) {
                $row = $data[$i];
                
                if (isset($row['service_type']))
                    $row['service_type'] = $services[$row['service_type']];

                fputcsv($output, $row);
                $i++;
            }
        });
    }
}
