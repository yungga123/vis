<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

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
    protected $afterInsert    = [];
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

    // After JO accepted, add corresponding new record to schedules table
    protected function addToScheduleAfterJOAccepted(array $data)
    {
        if ($data['result']) {
            if (isset($data['data']['status']) && $data['data']['status'] === 'accepted') {
                $tlViewModel    = new TaskLeadView();
                $customerModel  = new CustomerModel();
                $id         = $data['id'][0];
                $columns    = "
                    IF({$this->table}.is_manual = 0, {$tlViewModel->table}.customer_name, {$customerModel->table}.name) AS client,
                    IF({$this->table}.is_manual = 0, {$tlViewModel->table}.tasklead_type, {$this->table}.manual_quotation_type) AS tasklead_type,
                    {$this->table}.comments,
                ";
                $job_order  = $this->getJobOrders($id, $columns);

                $this->db->table('schedules')->insert([
                    'job_order_id'  => $id,
                    'title'         => $job_order['client'],
                    'description'   => $job_order['comments'],
                    'type'          => !empty($job_order['tasklead_type']) ? strtolower($job_order['tasklead_type']) : 'project',
                    'start'         => $data['data']['date_committed'],
                    'end'           => $data['data']['date_committed'] .' 23:00', // set to 11pm
                    'created_by'    => session('username'),
                ]);
            }
        }

        return $data;
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

    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
        
    }
    
    // Join job_orders with task_lead_booked , employees, customers and customer_branches
    public function joinWithOtherTables($builder, $withStatusByAndAt = false)
    {
        $employeeModel  = new EmployeeModel();
        $tlViewModel    = new TaskLeadView();
        $customerModel  = new CustomerModel();
        $customerBranchModel  = new CustomerBranchModel();

        $builder->join($tlViewModel->table, "{$this->table}.tasklead_id = {$tlViewModel->table}.id", 'left');
        $builder->join($customerModel->table, "{$this->table}.customer_id = {$customerModel->table}.id", 'left');
        $builder->join($customerBranchModel->table, "({$this->table}.customer_branch_id = {$customerBranchModel->table}.id AND {$this->table}.customer_branch_id IS NOT NULL)", 'left');
        $builder->join($employeeModel->table, "{$this->table}.employee_id = {$employeeModel->table}.employee_id", 'left');

        if ($withStatusByAndAt) {
            $this->joinAccountView($builder, "{$this->table}.created_by", 'av1');
            $this->joinAccountView($builder, "{$this->table}.accepted_by", 'av2');
            $this->joinAccountView($builder, "{$this->table}.filed_by", 'av3');
            $this->joinAccountView($builder, "{$this->table}.discarded_by", 'av4');
            $this->joinAccountView($builder, "{$this->table}.reverted_by", 'av5');
        }

        $builder->where("{$this->table}.deleted_at IS NULL");
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
