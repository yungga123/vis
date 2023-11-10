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
    protected $tableJoined      = 'task_lead_booked';
    protected $tableEmployees   = 'employees';
    protected $tableCustomers   = 'customers';
    protected $tableCustomerBranches   = 'customer_branches';
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
        $columns = "
            {$this->table}.id,
            {$this->table}.tasklead_id,
            {$this->table}.employee_id,
            {$this->table}.status,
            IF({$this->table}.is_manual = 0, {$this->tableJoined}.quotation_num, {$this->table}.manual_quotation) AS quotation,
            UPPER(IF({$this->table}.is_manual = 0, {$this->tableJoined}.tasklead_type, {$this->table}.manual_quotation_type)) AS tasklead_type,
            CONCAT({$this->tableEmployees}.firstname,' ',{$this->tableEmployees}.lastname) AS manager,
            {$this->tableEmployees}.firstname,
            {$this->tableEmployees}.lastname,
            {$this->table}.work_type,
            {$this->table}.comments,            
            {$this->table}.warranty,
            {$this->table}.remarks,
            {$this->table}.is_manual,
            {$this->table}.manual_quotation,
            {$this->table}.customer_id,
            {$this->table}.customer_branch_id,
            IF({$this->table}.is_manual = 0, {$this->tableJoined}.customer_name, {$this->tableCustomers}.name) AS client,
            IF({$this->table}.is_manual = 0, {$this->tableJoined}.customer_type, {$this->tableCustomers}.type) AS customer_type,
            IF({$this->table}.is_manual = 0, {$this->tableJoined}.branch_name, {$this->tableCustomerBranches}.branch_name) AS customer_branch_name,
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
                IF({$this->table}.date_requested IS NULL, 'N/A', DATE_FORMAT({$this->table}.date_requested, '%b %e, %Y')) AS date_requested,
                IF({$this->table}.date_committed IS NULL, 'N/A', DATE_FORMAT({$this->table}.date_committed, '%b %e, %Y')) AS date_committed,
                IF({$this->table}.date_reported IS NULL, 'N/A', DATE_FORMAT({$this->table}.date_reported, '%b %e, %Y')) AS date_reported
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

    // Common columns
    public function selectedColumns($with_text = false, $with_date = false)
    {
        $columns = "
            {$this->table}.tasklead_id,
            {$this->table}.status AS jo_status,
            IF({$this->table}.is_manual = 0, {$this->tableJoined}.quotation_num, {$this->table}.manual_quotation) AS quotation,
            UPPER(IF({$this->table}.is_manual = 0, {$this->tableJoined}.tasklead_type, {$this->table}.manual_quotation_type)) AS tasklead_type,
            IF({$this->table}.is_manual = 0, {$this->tableJoined}.customer_name, {$this->tableCustomers}.name) AS client,
            {$this->table}.work_type,
            CONCAT({$this->tableEmployees}.firstname,' ',{$this->tableEmployees}.lastname) AS manager
        ";

        if ($with_text) {
            $columns .= ", 
                {$this->table}.id,
                CONCAT({$this->table}.id, ' | ', IF({$this->table}.is_manual = 0, {$this->tableJoined}.quotation_num, {$this->table}.manual_quotation), ' | ', IF({$this->table}.is_manual = 0, {$this->tableJoined}.customer_name, {$this->tableCustomers}.name)) AS option_text
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
    
    // Join job_orders with task_lead_booked 
    public function _join($builder, $withStatusByAndAt = false)
    {
        $builder->join($this->tableJoined, "{$this->table}.tasklead_id = {$this->tableJoined}.id", 'left');
        $builder->join($this->tableCustomers, "{$this->table}.customer_id = {$this->tableCustomers}.id", 'left');
        $builder->join($this->tableCustomerBranches, "({$this->table}.customer_branch_id = {$this->tableCustomerBranches}.id AND {$this->table}.customer_branch_id IS NOT NULL)", 'left');
        $builder->join($this->tableEmployees, "{$this->table}.employee_id = {$this->tableEmployees}.employee_id", 'left');

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

        if ($join) $this->_join($builder);
        else $builder->where("{$this->table}.deleted_at IS NULL");

        return $id ? $builder->find($id) : $builder->findAll();
    }

    // After JO accepted, add corresponding new record to schedules table
    public function addToScheduleAfterJOAccepted(array $data)
    {
        if ($data['result']) {
            if (isset($data['data']['status']) && $data['data']['status'] === 'accepted') {
                $id         = $data['id'][0];
                $columns    = "
                    IF({$this->table}.is_manual = 0, {$this->tableJoined}.customer_name, {$this->tableCustomers}.name) AS client,
                    IF({$this->table}.is_manual = 0, {$this->tableJoined}.tasklead_type, {$this->table}.manual_quotation_type) AS tasklead_type,
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

    // For dataTables
    public function noticeTable($request) 
    {
        $columns = $this->_columns(false, true);
        $columns .= ', ' 
            . $this->_dtStatusByFormat('created_by', 'av1', true)
            . $this->_dtStatusByFormat('accepted_by', 'av2', true)
            . $this->_dtStatusByFormat('filed_by', 'av3', true)
            . $this->_dtStatusByFormat('discarded_by', 'av4', true)
            . $this->_dtStatusByFormat('reverted_by', 'av5', true)
            . $this->_dtStatusAtFormat('created_at', true)
            . $this->_dtStatusAtFormat('accepted_at', true)
            . $this->_dtStatusAtFormat('filed_at', true)
            . $this->_dtStatusAtFormat('discarded_at', true)
            . $this->_dtStatusAtFormat('reverted_at', true);

        $builder = $this->db->table($this->table);
        $builder->select($columns);        
        $this->_join($builder, true);

        $this->filterParam($request, $builder, "{$this->table}.status");
        $this->filterParam($request, $builder, "IF({$this->table}.is_manual = 0, {$this->tableJoined}.tasklead_type, {$this->table}.manual_quotation_type)", 'type');
        $this->filterParam($request, $builder, "{$this->table}.is_manual", 'is_manual');
        $this->filterParam($request, $builder, "{$this->table}.work_type", 'work_type');

        $builder->orderBy('id', 'DESC');
        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $no_actions = ['filed', 'discarded'];
        $closureFun = function($row) use($id, $permissions, $dropdown, $no_actions) {
            $buttons = '~~N/A~~';

            if (! in_array($row['status'], $no_actions)) {                
                $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

                if ($row['status'] === 'pending') {
                    if (check_permissions($permissions, 'ACCEPT')) {
                        // Accept JO
                        $changeTo = 'accept';
                        $buttons .= dt_button_html([
                            'text'      => $dropdown ? ucfirst($changeTo) : '',
                            'button'    => 'btn-primary',
                            'icon'      => 'fas fa-check-circle',
                            'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
                        ], $dropdown);
                    }

                    if (check_permissions($permissions, 'DISCARD')) {
                        // Discard JO
                        $changeTo = 'discard';
                        $buttons .= dt_button_html([
                            'text'      => $dropdown ? ucfirst($changeTo) : '',
                            'button'    => 'btn-secondary',
                            'icon'      => 'fas fa-times-circle',
                            'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
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
                        'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
                    ], $dropdown);
                }

                if (
                    check_permissions($permissions, 'RESCHEDULE') && 
                    in_array($row['status'], ['accepted', 'discarded'])
                ) {
                    // Reschedule JO - Revert to pending state
                    $changeTo = 'pending';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-secondary',
                        'icon'      => 'fas fa-calendar',
                        'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
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
