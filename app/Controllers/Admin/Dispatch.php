<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DispatchModel;
use App\Models\DispatchedTechniciansModel;
use monken\TablesIgniter;

class Dispatch extends BaseController
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
        $data['custom_js']      = 'admin/dispatch/index.js';
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
        $table      = new TablesIgniter();
        $request    = $this->request->getVar();
        $builder    = $this->_model->noticeTable($request);

        $table->setTable($builder)
            ->setSearch([
                'title',
                'customer',
                'technicians',
                'service_type',
                'sr_number',
            ])
            ->setOrder([
                null,
                'id',
                'schedule_id',
                'schedule',
                'customer',
                'dispatch_date',
                'dispatch_out',
                'time_in',
                'time_out',
                'sr_number',
                'technicians_formatted',
                null,
                'with_permit',
                'comments',
                'remarks',
            ])
            ->setOutput([
                $this->_model->buttons($this->_permissions),
                'id',
                'schedule_id',
                'schedule',
                'customer',
                'dispatch_date',
                'dispatch_out',
                'time_in',
                'time_out',
                'sr_number',
                'technicians_formatted',
                $this->_model->serviceTypeFormat(),
                'with_permit',
                'comments',
                'remarks',
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
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Dispatch has been added successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
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

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
    
    /**
     * For getting the record using the id
     *
     * @return json
     */
    public function fetch() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Dispatch has been retrieved!'
        ];

        try {
            $id         = $this->request->getVar('id');
            $dTModel    = new DispatchedTechniciansModel();

            $data['data']                   = $this->_model->getDispatch($id, false, true);
            $data['data']['technicians']    = $dTModel->getDispatchedTechnicians($id);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
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
            'message'   => 'Dispatch has been deleted successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id = $this->request->getVar('id');

            if (! $this->_model->delete($id)) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            } else {
                log_message('error', 'Deleted by {username}', ['username' => session('username')]);
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception$e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
}
