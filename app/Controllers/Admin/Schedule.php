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
        $this->_model       = new ScheduleModel(); // Current model
        $this->_module_code = MODULE_CODES['schedules']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
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
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.added', 'Schedule')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $action     = ACTION_ADD;
                $id         = $this->request->getVar('id');
                $inputs     = [
                    'date_range'    => $this->request->getVar('date_range'),
                    'title'         => $this->request->getVar('title'),
                    'description'   => $this->request->getVar('description'),
                    'start'         => $this->request->getVar('start'),
                    'end'           => $this->request->getVar('end'),
                    'type'          => $this->request->getVar('type'),
                    'created_by'    => session('username'),
                ];
                
                if (! empty($id)) {
                    $action             = ACTION_EDIT;
                    $inputs['id']       = $id;
                    $data['message']    = res_lang('success.updated', 'Schedule');
    
                    unset($inputs['created_by']);
                }

                $this->checkRoleActionPermissions($this->_module_code, $action, true);
    
                if (! $this->_model->save($inputs)) {
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
            $this->logExceptionError($e, __METHOD__);
            $data = res_lang('error.process');
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
        $data       = [
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.deleted', 'Schedule')
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
                        $data['message']. " Schedule #: {$id} \nDeleted by: {username}",
                        ['username' => session('username')]
                    );
                }

                return $data;
            }
        );

        return $response;
    }
}
