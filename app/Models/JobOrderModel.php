<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\RawSql;

class JobOrderModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'job_orders';
    protected $tableJoined      = 'task_lead_booked';
    protected $tableEmployees   = 'employees';
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
        'created_by',
        'remarks',
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
            'rules' => 'required',
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
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
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
            {$this->tableJoined}.quotation_num AS quotation,
            {$this->tableJoined}.tasklead_type AS type,
            {$this->tableJoined}.tasklead_type,
            {$this->tableJoined}.customer_name AS client,
            CONCAT({$this->tableEmployees}.firstname,' ',{$this->tableEmployees}.lastname) AS manager,
            {$this->tableEmployees}.firstname,
            {$this->tableEmployees}.lastname,
            {$this->table}.work_type,
            {$this->table}.comments,            
            {$this->table}.warranty,
            {$this->table}.remarks,
            created_by
        ";
        $dates = "
            ,{$this->table}.date_requested,
            {$this->table}.date_committed,
            {$this->table}.date_reported,
        ";

        if ($isDateFormatted) {
            $dates = "
                ,IF({$this->table}.date_requested IS NULL, 'N/A', DATE_FORMAT({$this->table}.date_requested, '%b %e, %Y')) AS date_requested,
                IF({$this->table}.date_committed IS NULL, 'N/A', DATE_FORMAT({$this->table}.date_committed, '%b %e, %Y')) AS date_committed,
                IF({$this->table}.date_reported IS NULL, 'N/A', DATE_FORMAT({$this->table}.date_reported, '%b %e, %Y')) AS date_reported
            ";
        }

        $columns .= $dates;

        if ($withRequestBy) {
            $columns .= "
               , (SELECT av.employee_name FROM accounts_view AS av WHERE {$this->table}.created_by = av.username) AS requested_by 
            ";
        }

        return $columns;
    }
    
    // Join job_orders with task_lead_booked 
    private function _join($builder)
    {
        $builder->join($this->tableJoined, "{$this->table}.tasklead_id = {$this->tableJoined}.id");
        $builder->join($this->tableEmployees, "{$this->table}.employee_id = {$this->tableEmployees}.employee_id");
        $builder->where("{$this->table}.deleted_at IS NULL");
    }

    // Get job orders
    public function getJobOrders($id = null, $columns = '', $join = true)
    {
        $columns = $columns ? $columns : $this->_columns(true);
        $builder = $this->select($columns);

        if ($join) $this->_join($builder);
        return $id ? $builder->find($id) : $builder->findAll();
    }

    // After JO accepted, add corresponding new record to schedules table
    public function addToScheduleAfterJOAccepted(array $data)
    {
        if ($data['result']) {
            if ($data['data']['status'] === 'accepted') {
                $id         = $data['id'][0];
                $columns    = "
                    {$this->tableJoined}.customer_name AS client,
                    {$this->tableJoined}.tasklead_type AS type,
                    {$this->table}.comments,
                ";
                $job_order  = $this->getJobOrders($id, $columns);

                $this->db->table('schedules')->insert([
                    'job_order_id'  => $id,
                    'title'         => $job_order['client'],
                    'description'   => $job_order['comments'],
                    'type'          => $job_order['type'] ? strtolower($job_order['type']) : 'project',
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
        $builder = $this->db->table($this->table);
        $builder->select($this->_columns(false, true));        
        $this->_join($builder);

        if (isset($request['params'])) {
            $params = $request['params'];

            if (! empty($params['status'])) {}
                $builder->whereIn("{$this->table}.status", $params['status']);

            if (! empty($params['type'])) 
                $builder->whereIn("{$this->tableJoined}.tasklead_type", $params['type']);

            if (! empty($params['work_type'])) 
                $builder->whereIn("{$this->table}.work_type", $params['work_type']);
        }

        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $no_actions = ['filed', 'discarded'];
        $closureFun = function($row) use($id, $permissions, $dropdown, $no_actions) {
            $buttons = 'No actions';

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
            $text   = ucfirst(set_jo_status($row['status']));
            $class  = 'rounded text-sm text-white pl-2 pr-2 pt-1 pb-1';

            switch ($row['status']) {
                case 'pending':
                    $format = '<span class="bg-warning '.$class.'">'.$text.'</span>';
                    break;
                case 'accepted':
                    $format = '<span class="bg-primary '.$class.'">'.$text.'</span>';
                    break;
                case 'filed':
                    $format = '<span class="bg-success '.$class.'">'.$text.'</span>';
                    break;
                default:
                    $format = '<span class="bg-secondary '.$class.'">'.$text.'</span>';
                    break;
            }

            return $format;
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
}
