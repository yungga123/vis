<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\TaskleadHistoryModel;

class TaskLeadHistory extends BaseController
{

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

     private $_time;

    /**
     * Class constructor
     */

    
    public function __construct()
    {
        $this->_model       = new TaskleadHistoryModel(); // Current model
        $this->_module_code = MODULE_CODES['task_lead']; // Current module
        $this->_permissions = $this->getSpecificPermissions($this->_module_code);
        $this->_can_add     = $this->checkPermissions($this->_permissions, ACTION_ADD);
    }

    public function history_add($data)
    {
        $this->_model->insert($data);
    }
}
