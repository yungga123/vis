<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use App\Traits\AdminTrait;
use App\Traits\HRTrait;

class Schedule extends BaseController
{
    /* Declare trait here to use */
    use AdminTrait, HRTrait;

    /**
     * Use to initialize ScheduleModel class
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
        $this->_model       = new ScheduleModel(); // Current model
        $this->_module_code = MODULE_CODES['schedules']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, 'ADD');
    }

    /**
     * Display the schedule view
     *
     * @return view
     */
    public function index()
    {
        // Check role if has permission, otherwise redirect to denied page
        $this->checkRolePermissions($this->_module_code);
        
        $data['title']          = 'Schedules List';
        $data['page_title']     = 'Schedules List';
        $data['can_add']        = $this->_can_add;
        $data['btn_add_lbl']    = $this->_can_add ? 'Add Schedule' : '';
        $data['with_dtTable']   = true;
        $data['with_jszip']     = true;
        $data['sweetalert2']    = true;
        $data['select2']        = true;
        $data['moment']         = true;
        $data['date_range_picker'] = true;
        $data['full_calendar']  = true;
        $data['custom_js']      = 'admin/schedule/index.js';
        $data['routes']         = json_encode([
            'schedule' => [
                'list'      => url_to('schedule.list'),
                'save'      => url_to('schedule.save'),
                'delete'    => url_to('schedule.delete'),
            ],
        ]);
        $data['type_legend']    = $this->scheduleTypeLegend();


        return view('admin/schedule/index', $data);
    }

    /**
     * Saving record process (insert and update)
     *
     * @return json
     */
    public function save() 
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'Schedule has been added successfully!'
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            $id         = $this->request->getVar('id');
            $inputs = [
                'date_range'    => $this->request->getVar('date_range'),
                'title'         => $this->request->getVar('title'),
                'description'   => $this->request->getVar('description'),
                'start'         => $this->request->getVar('start'),
                'end'           => $this->request->getVar('end'),
                'type'          => $this->request->getVar('type'),
                'created_by'    => session('username'),
            ];

            
            if (! empty($id)) {
                if (!$this->checkPermissions($this->_permissions, 'EDIT')) {
                    return $this->response->setJSON(
                        [
                            'status'    => STATUS_INFO,
                            'message'   => "You don't have permission to EDIT any schedule currently! Ask your superior to provide one."
                        ]
                    );
                }

                $inputs['id']       = $id;
                $data['message']    = 'Schedule has been updated successfully!';

                unset($inputs['created_by']);
            } 

            if (! $this->_model->save($inputs)) {
                $data['errors']     = $this->_model->errors();
                $data['status']     = STATUS_ERROR;
                $data['message']    = "Validation error!";
            }

            // Commit transaction
            $this->transCommit();
        } catch (\Exception $e) {
            // Rollback transaction if there's an error
            $this->transRollback();

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data['status']     = STATUS_ERROR;
            $data['message']    = 'Error while processing data! Please contact your system administrator.';
        }

        return $this->response->setJSON($data);
    }
    
    /**
     * For retrieving list of records
     *
     * @return json
     */
    public function list() 
    {
        try {
            $data       = [];
            $columns    = $this->_model->allowedFields;
            $columns[]  = 'id';
            $records    = $this->_model->getSchedules(null, $columns);

            if (! empty($records)) {
                foreach ($records as $key => $val) {
                    $type   = get_schedule_type($val['type']);
                    $color  = $type['color']; 
                    $data[] = [
                        'id'                => $val['id'],
                        'title'             => $val['title'],
                        'start'             => $val['start'],
                        'end'               => $val['end'],
                        'backgroundColor'   => $color,
                        'borderColor'       => $color,
                        'extendedProps'     => [
                            'description'   => $val['description'],
                            'type'          => strtolower($val['type']),
                            'typeText'      => $type['text'],
                        ],
                    ]; 
                }
            }
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            $data = 'Error while processing data! Please contact your system administrator.';
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
            'status'    => STATUS_INFO,
            'message'   => "You don't have permission to DELETE any schedule currently! Ask your superior to provide one."
        ];

        // Using DB Transaction
        $this->transBegin();

        try {
            if ($this->checkPermissions($this->_permissions, 'DELETE')) {
                $id = $this->request->getVar('id');
                
                $data['status']     = STATUS_SUCCESS;
                $data['message']    = "Schedule has been deleted successfully!";            

                if (! $this->_model->delete($id)) {
                    $data['errors']     = $this->_model->errors();
                    $data['status']     = STATUS_ERROR;
                    $data['message']    = "Validation error!";
                } else {
                    log_message('error', 'Deleted by {username}', ['username' => session('username')]);
                }
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
