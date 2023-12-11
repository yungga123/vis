<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;
use App\Services\Mail\AdminMailService;

class JobOrderModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'job_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tasklead_id',
        'employee_id',
        'status',
        'work_type',
        'comments',
        'date_requested',
        'date_reported',
        'date_committed',
        'warranty',
        'remarks',
        'is_manual',
        'manual_quotation',
        'manual_quotation_type',
        'customer_id',
        'customer_branch_id',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'quotation' => [
            'rules' => 'required_without[is_manual,manual_quotation]',
            'label' => 'quotation number'
        ],
        'work_type' => [
            'rules' => 'required',
            'label' => 'work type'
        ],
        'comments' => [
            'rules' => 'required|string',
            'label' => 'comments'
        ],
        'date_requested' => [
            'rules' => 'required|date',
            'label' => 'date requested'
        ],
        'date_reported' => [
            'rules' => 'required|date',
            'label' => 'date reported'
        ],
        'warranty' => [
            'rules' => 'required',
            'label' => 'warranty'
        ],
        'manual_quotation' => [
            'rules' => 'required_with[is_manual]',
            'label' => 'manual quotation number'
        ],
        'customer_id' => [
            'rules' => 'required_with[is_manual]',
            'label' => 'client'
        ],
    ];
    protected $validationMessages   = [
        'quotation' => [
            'required_without' => 'The quotation number field is required.'
        ],
        'manual_quotation' => [
            'required_with' => 'The manual quotation number field is required.'
        ],
        'customer_id' => [
            'required_with' => 'The client field is required.'
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = ['mailNotif'];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = ['addToScheduleAfterJOAccepted'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Common columns
    private function _columns($withRequestBy = false, $isDateFormatted = false)
    {
        $tlViewModel    = new TaskLeadView();
        $customerModel  = new CustomerModel();
        $employeeModel  = new EmployeeModel();
        $customerBranchModel  = new CustomerBranchModel();
        $columns = "
            {$this->table}.id,
            {$this->table}.tasklead_id,
            {$this->table}.employee_id,
            {$this->table}.status,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.quotation_num, {$this->table}.manual_quotation) AS quotation,
            UPPER(IF({$this->table}.is_manual = 0, {$tlViewModel->table}.tasklead_type, {$this->table}.manual_quotation_type)) AS tasklead_type,
            CONCAT({$employeeModel->table}.firstname,' ',{$employeeModel->table}.lastname) AS manager,
            {$employeeModel->table}.firstname,
            {$employeeModel->table}.lastname,
            {$this->table}.work_type,
            {$this->table}.comments,            
            {$this->table}.warranty,
            {$this->table}.remarks,
            {$this->table}.is_manual,
            {$this->table}.manual_quotation,
            {$this->table}.customer_id,
            {$this->table}.customer_branch_id,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_name, {$customerModel->table}.name) AS client,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_type, {$customerModel->table}.type) AS customer_type,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.branch_name, {$customerBranchModel->table}.branch_name) AS customer_branch_name,
            {$this->table}.created_by
        ";
        $dates = "
            ,{$this->table}.date_requested,
            {$this->table}.date_committed,
            {$this->table}.date_reported,
        ";

        if ($isDateFormatted) {
            $dates = ",
                IF({$this->table}.is_manual = 0, 'NO', 'YES') AS is_manual_formatted,
                ".dt_sql_date_format("{$this->table}.date_requested")." AS date_requested,
                ".dt_sql_date_format("{$this->table}.date_committed")." AS date_committed,
                ".dt_sql_date_format("{$this->table}.date_reported")." AS date_reported
            ";
        }

        $columns .= $dates;

        if ($withRequestBy) {
            $columns .= "
               , (SELECT av.employee_name FROM `accounts_view` AS av WHERE {$this->table}.created_by = av.username) AS requested_by 
            ";
        }

        return $columns;
    }

    // Get JO details for mail notif
    private function _getJODetails($id)
    {
        $tlViewModel    = new TaskLeadView();
        $customerModel  = new CustomerModel();
        $employeeModel  = new EmployeeModel();
        $columns        = "
            {$this->table}.id,
            {$this->table}.tasklead_id,
            {$this->table}.status,
            IF({$this->table}.is_manual = 0, 'NO', 'YES') AS is_manual,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_name, {$customerModel->table}.name) AS client,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.quotation_num, {$this->table}.manual_quotation) AS quotation,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.tasklead_type, {$this->table}.manual_quotation_type) AS tasklead_type,
            CONCAT({$employeeModel->table}.firstname,' ',{$employeeModel->table}.lastname) AS manager,
            {$this->table}.work_type,
            {$this->table}.comments,
            cb.employee_name AS requested_by,
            ".dt_sql_date_format("{$this->table}.date_requested")." AS date_requested,
            ".dt_sql_date_format("{$this->table}.date_committed")." AS date_committed,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at
        ";
        $builder        = $this->select($columns);  
        $this->joinWithOtherTables($builder);
        $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');
        
        $builder->where("{$this->table}.id", $id);
        return $builder->first();
    }

    // Set the value for 'status_' by and at before updating status
    protected function setStatusByAndAt(array $data)
    {
        if (isset($data['data']['status'])) {
            $status = $data['data']['status'];
            $status = $status === 'pending' ? 'reverted' : $status;
            $data['data'][$status .'_by'] = session('username');
            $data['data'][$status .'_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    // Mail notif after JO created
    protected function mailNotif(array $data)
    {
        if ($data['result']) {
            $id             = $data['id'];
            $job_order      = $this->_getJODetails($id);
            $module_code    = get_module_codes('job_orders');

            // Send mail notification
            $service = new AdminMailService();
            $service->sendJOMailNotif($job_order, $module_code);
        }
        
        return $data;
    }

    // After JO accepted, add corresponding new record to schedules table
    protected function addToScheduleAfterJOAccepted(array $data)
    {
        // Used try catch so that if there's an error
        // mail will not be sent
        try {
            if ($data['result']) {
                if (isset($data['data']['status']) && $data['data']['status'] === 'accepted') {
                    $id             = $data['id'][0];
                    $job_order      = $this->_getJODetails($id);
                    
                    // Create schedule
                    $scheduleTitle  = $job_order['client'];
                    $scheduleDesc   = $job_order['comments'];
                    $scheduleStart  = $data['data']['date_committed'];
                    $scheduleEnd    = $scheduleStart .' 23:00'; // set to 11pm
                    $scheduleType   = empty($job_order['tasklead_type']) ? 'project' : strtolower($job_order['tasklead_type']);
                    $scheduleModel  = new ScheduleModel();
                    $schedBuilder   = $this->db->table($scheduleModel->table);
                    $schedInsert    = $schedBuilder->insert([
                            'job_order_id'  => $id,
                            'title'         => $scheduleTitle,
                            'description'   => $scheduleDesc,
                            'type'          => $scheduleType,
                            'start'         => $scheduleStart,
                            'end'           => $scheduleEnd,
                            'created_by'    => session('username'),
                        ]);
                    
                    // Initialize service
                    $service    = new AdminMailService();

                    // If schedule successfully created, then send mail
                    if ($schedInsert && $schedId = $scheduleModel->insertID()) {
                        $created_at = current_datetime();
                        $schedule   = [
                            'id'            => $schedId,
                            'job_order_id'  => $id,
                            'title'         => $scheduleTitle,
                            'description'   => $scheduleDesc,
                            'type'          => $scheduleType,
                            'start'         => $scheduleStart,
                            'end'           => $scheduleEnd,
                            'created_at'    => format_datetime($created_at),
                            'created_by'    => session('name'),
                        ];
    
                        // Send Schedule mail notification
                        $module_code = get_module_codes('schedules');
                        $service->sendScheduleMailNotif($schedule, $module_code);
                    }
    
                    // Send JO mail notification
                    $module_code = get_module_codes('job_orders');
                    $service->sendJOMailNotif($job_order, $module_code);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $data;
    }

    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
        
    }

    // DataTable default columns 
    public function dtColumns()
    {
        $tlViewModel    = new TaskLeadView();
        $customerModel  = new CustomerModel();
        $employeeModel  = new EmployeeModel();
        $customerBranchModel  = new CustomerBranchModel();
        $columns        = "
            UPPER({$this->table}.status) AS status,
            {$this->table}.id,
            {$this->table}.tasklead_id,
            IF({$this->table}.is_manual = 0, 'NO', 'YES') AS is_manual,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.quotation_num, {$this->table}.manual_quotation) AS quotation,
            UPPER(IF({$this->table}.is_manual = 0, {$tlViewModel->table}.tasklead_type, {$this->table}.manual_quotation_type)) AS tasklead_type,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_type, {$customerModel->table}.type) AS customer_type,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_name, {$customerModel->table}.name) AS client,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.branch_name, {$customerBranchModel->table}.branch_name) AS customer_branch_name,
            CONCAT({$employeeModel->table}.firstname,' ',{$employeeModel->table}.lastname) AS manager,
            {$this->table}.work_type,
            ".dt_sql_date_format("{$this->table}.date_requested")." AS date_requested,
            ".dt_sql_date_format("{$this->table}.date_committed")." AS date_committed,
            ".dt_sql_date_format("{$this->table}.date_reported")." AS date_reported,
            {$this->table}.warranty,
            {$this->table}.comments,
            {$this->table}.remarks,
            av1.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at,
            av2.employee_name AS accepted_by,
            ".dt_sql_datetime_format("{$this->table}.accepted_at")." AS accepted_at,
            av3.employee_name AS filed_by,
            ".dt_sql_datetime_format("{$this->table}.filed_at")." AS filed_at,
            av4.employee_name AS discarded_by,
            ".dt_sql_datetime_format("{$this->table}.discarded_at")." AS discarded_at,
            av5.employee_name AS reverted_by,
            ".dt_sql_datetime_format("{$this->table}.reverted_at")." AS reverted_at
        ";

        return $columns;
    }

    // Common columns
    public function selectedColumns($with_text = false, $with_date = false)
    {
        $tlViewModel    = new TaskLeadView();
        $customerModel  = new CustomerModel();
        $employeeModel  = new EmployeeModel();
        $columns        = "
            {$this->table}.tasklead_id,
            {$this->table}.status AS jo_status,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.quotation_num, {$this->table}.manual_quotation) AS quotation,
            UPPER(IF({$this->table}.is_manual = 0, {$tlViewModel->table}.tasklead_type, {$this->table}.manual_quotation_type)) AS tasklead_type,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_name, {$customerModel->table}.name) AS client,
            {$this->table}.work_type,
            CONCAT({$employeeModel->table}.firstname,' ',{$employeeModel->table}.lastname) AS manager
        ";

        if ($with_text) {
            $columns .= ", 
                {$this->table}.id,
                CONCAT({$this->table}.id, ' | ', IF({$this->table}.is_manual = 0, {$tlViewModel->table}.quotation_num, {$this->table}.manual_quotation), ' | ', IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_name, {$customerModel->table}.name)) AS option_text
            ";
        }

        if ($with_date) {
            $columns .= ",
                {$this->table}.date_requested,
                {$this->table}.date_committed,
                {$this->table}.date_reported,
                {$this->table}.created_by AS jo_created_at
            ";
        }

        return $columns;
    }
    
    // Join job_orders with task_lead_booked , employees, customers and customer_branches
    public function joinWithOtherTables($builder, $withStatusByAndAt = false)
    {
        $this->joinTaskleadBooked($builder);
        $this->joinCustomers($builder, null, 'left', true);
        $this->joinEmployees($builder);

        if ($withStatusByAndAt) {
            $this->joinAccountView($builder, "{$this->table}.created_by", 'av1');
            $this->joinAccountView($builder, "{$this->table}.accepted_by", 'av2');
            $this->joinAccountView($builder, "{$this->table}.filed_by", 'av3');
            $this->joinAccountView($builder, "{$this->table}.discarded_by", 'av4');
            $this->joinAccountView($builder, "{$this->table}.reverted_by", 'av5');
        }

        $builder->where("{$this->table}.deleted_at IS NULL");
    }

    // Join job_orders with task_lead_booked
    public function joinTaskleadBooked($builder, $model = null, $type = 'left')
    {      
        $model ?? $model = new TaskLeadView();
        $builder->join($model->table, "{$this->table}.tasklead_id = {$model->table}.id", $type);

        return $this;
    }

    // Join job_orders with customers
    public function joinCustomers($builder, $model = null, $type = 'left', $branch = false)
    {      
        $model ?? $model = new CustomerModel();
        $builder->join($model->table, "{$this->table}.customer_id = {$model->table}.id", $type);

        if ($branch) {
            $branchModel  = new CustomerBranchModel();
            $builder->join($branchModel->table, "({$this->table}.customer_branch_id = {$branchModel->table}.id AND {$this->table}.customer_branch_id IS NOT NULL)", 'left');
        }

        return $this;
    }

    // Join job_orders with employees
    public function joinEmployees($builder, $model = null, $type = 'left')
    {      
        $model ?? $model = new EmployeeModel();
        $builder->join($model->table, "{$this->table}.employee_id = {$model->table}.employee_id", $type);

        return $this;
    }

    // Get job orders
    public function getJobOrders($id = null, $columns = '', $join = true)
    {
        $columns = $columns ? $columns : $this->_columns(true);
        $builder = $this->select($columns);

        if ($join) $this->joinWithOtherTables($builder);
        else $builder->where("{$this->table}.deleted_at IS NULL");

        return $id ? $builder->find($id) : $builder->findAll();
    }

    // Get clients details using job_order_id
    public function getClientInfo($job_order_id, $columns = '')
    {
        $customerModel  = new CustomerModel();
        $tlViewModel    = new TaskLeadView();
        $tlCustomer     = 'tl_customer';
        $columns        = $columns ? $columns : "
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_id, {$this->table}.customer_id) AS client_id,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_name, {$customerModel->table}.name) AS client_name,
            IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_type, {$customerModel->table}.type) AS client_type,
            IF({$this->table}.is_manual = 0, {$tlCustomer}.contact_person, {$customerModel->table}.contact_person) AS client_contact_person,
            IF({$this->table}.is_manual = 0, {$tlCustomer}.contact_number, {$customerModel->table}.contact_number) AS client_contact_number,
            IF({$this->table}.is_manual = 0, {$tlCustomer}.telephone, {$customerModel->table}.telephone) AS client_telephone,
            IF({$this->table}.is_manual = 0, {$tlCustomer}.email_address, {$customerModel->table}.email_address) AS client_email_address,
            IF({$this->table}.is_manual = 0, ".dt_sql_concat_client_address($tlCustomer, '').", ".dt_sql_concat_client_address($customerModel->table, '').") AS client_address
        ";
        $builder = $this->select($columns);

        $this->joinTaskleadBooked($builder, $tlViewModel);
        $this->joinCustomers($builder, $customerModel);
        $tlViewModel->joinCustomers($builder, $customerModel, 'left', $tlCustomer);
        
        $builder->where("{$this->table}.deleted_at IS NULL");

        if (is_array($job_order_id) && count($job_order_id) > 1) {
            $builder->whereIn("{$this->table}.id", $job_order_id);
            return $builder->findAll();
        }

        $builder->where("{$this->table}.id", $job_order_id);
        return $builder->first();
    }

    // For dataTables
    public function noticeTable($request) 
    {
        $tlViewModel    = new TaskLeadView();
        $builder        = $this->db->table($this->table);
        $builder->select($this->dtColumns());        
        $this->joinWithOtherTables($builder, true);

        $this->filterParam($request, $builder, "{$this->table}.status");
        $this->filterParam($request, $builder, "IF({$this->table}.is_manual = 0, {$tlViewModel->table}.tasklead_type, {$this->table}.manual_quotation_type)", 'type');
        $this->filterParam($request, $builder, "{$this->table}.is_manual", 'is_manual');
        $this->filterParam($request, $builder, "{$this->table}.work_type", 'work_type');

        $builder->orderBy("{$this->table}.id", 'DESC');
        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $no_actions = ['filed', 'discarded'];
        $closureFun = function($row) use($id, $permissions, $dropdown, $no_actions) {
            $buttons    = '~~N/A~~';
            $status     = strtolower($row['status']);

            if (! in_array($status, $no_actions)) {                
                $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

                if ($status === 'pending') {
                    if (check_permissions($permissions, 'ACCEPT')) {
                        // Accept JO
                        $changeTo = 'accept';
                        $buttons .= dt_button_html([
                            'text'      => $dropdown ? ucfirst($changeTo) : '',
                            'button'    => 'btn-primary',
                            'icon'      => 'fas fa-check-circle',
                            'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $status),
                        ], $dropdown);
                    }

                    if (check_permissions($permissions, 'DISCARD')) {
                        // Discard JO
                        $changeTo = 'discard';
                        $buttons .= dt_button_html([
                            'text'      => $dropdown ? ucfirst($changeTo) : '',
                            'button'    => 'btn-secondary',
                            'icon'      => 'fas fa-times-circle',
                            'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $status),
                        ], $dropdown);
                    }
                }

                if (check_permissions($permissions, 'FILE')) {
                    // File JO
                    $changeTo = 'file';
                    
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-success',
                        'icon'      => 'fas fa-file-import',
                        'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $status),
                    ], $dropdown);
                }

                if (
                    check_permissions($permissions, 'RESCHEDULE') && 
                    in_array($status, ['accepted', 'discarded'])
                ) {
                    // Reschedule JO - Revert to pending state
                    $changeTo = 'pending';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-secondary',
                        'icon'      => 'fas fa-calendar',
                        'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $status),
                    ], $dropdown);
                }

                $buttons = dt_buttons_dropdown($buttons);
            }

            return $buttons;
        };
        
        return $closureFun;
    }

    // DataTable status formatter
    public function dtJOStatusFormat()
    {
        $closureFun = function($row) {
            $text    = ucwords(set_jo_status($row['status']));
            $color   = dt_status_color($row['status']);
            return text_badge($color, $text);
        };
        
        return $closureFun;
    }

    // For status onchange event
    private function _statusDTOnchange($id, $changeTo, $status)
    {
        $title  = ucfirst($changeTo);
        $title  = $changeTo === 'pending' ? 'Reschedule or reverting to '. $changeTo : $title;

        return <<<EOF
            onclick="status({$id}, '{$changeTo}', '{$status}')" title="{$title} JO"
        EOF;
    }

    // For status by format
    private function _dtStatusByFormat($columnName, $alias, $comma = false)
    {
        $aliasCol   = $columnName .'_formatted';
        $comma      = $comma ? ',' : '';
        $statement  = "
            IF({$this->table}.$columnName IS NULL, 'N/A', {$alias}.employee_name) AS {$aliasCol}
        ";

        return $statement . $comma;
    }

    // For status at format
    private function _dtStatusAtFormat($columnName, $comma = false)
    {
        $atFormat   = dt_sql_datetime_format();
        $alias      = $columnName .'_formatted';
        $comma      = $comma ? ',' : '';
        $statement  = "
            IF({$this->table}.$columnName IS NULL, 'N/A', DATE_FORMAT({$this->table}.$columnName, '{$atFormat}')) AS {$alias}
        ";

        return $statement . $comma;
    }
}
