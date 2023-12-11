<?php

namespace App\Models;

use CodeIgniter\Model;

class DispatchModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'dispatch';
    protected $view             = 'dispatch_view';
    protected $tableSchedules   = 'schedules';
    protected $customersTable   = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'schedule_id',
        'sr_number',
        'dispatch_date',
        'dispatch_out',
        'time_in',
        'time_out',
        'remarks',
        'service_type',
        'comments',
        'with_permit',
        'created_by',
        'checked_by',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'schedule_id' => [
            'rules' => 'required',
            'label' => 'schedule'
        ],
        'sr_number' => [
            'rules' => 'permit_empty|max_length[100]',
            'label' => 'SR number'
        ],
        'dispatch_date' => [
            'rules' => 'required',
            'label' => 'dispatch date'
        ],
        'dispatch_out' => [
            'rules' => 'permit_empty',
            'label' => 'dispatch out'
        ],
        'time_in' => [
            'rules' => 'permit_empty',
            'label' => 'time in'
        ],
        'time_out' => [
            'rules' => 'permit_empty',
            'label' => 'time out'
        ],
        'remarks' => [
            'rules' => 'permit_empty|string|min_length[5]',
            'label' => 'remarks'
        ],
        'comments' => [
            'rules' => 'required|string|min_length[5]',
            'label' => 'comments'
        ],
        'service_type' => [
            'rules' => 'required',
            'label' => 'service type'
        ],
        'with_permit' => [
            'rules' => 'required',
            'label' => 'with permit'
        ],
        'technicians' => [
            'rules' => 'required',
            'label' => 'assign technicians'
        ],
        'checked_by' => [
            'rules' => 'required',
            'label' => 'check by'
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
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Common columns
    private function _columns($dateTimeformmated = false, $joinSchedule = false)
    {
        $scheduleModel  = new ScheduleModel();
        $columns        = "
            {$this->table}.id,
            {$this->table}.schedule_id,     
            {$this->table}.sr_number,
            {$this->table}.dispatch_date,
            {$this->table}.dispatch_out,
            {$this->table}.time_in,
            {$this->table}.time_out,
            {$this->table}.service_type,
            {$this->table}.with_permit,
            {$this->table}.comments,
            {$this->table}.remarks,
            {$this->table}.created_by,
            {$this->table}.checked_by,
            {$this->view}.technicians,
            {$this->view}.technicians_formatted,
            {$this->view}.dispatched_by,
            {$this->view}.dispatched_by_role,
            {$this->view}.dispatched_by_position,
            {$this->view}.checked_by_name,
            {$this->view}.checked_by_position
        ";

        if ($dateTimeformmated) {
            $columns.= ",
                ".dt_sql_date_format("{$this->table}.dispatch_date")." AS dispatch_date,
                ".dt_sql_time_format("{$this->table}.dispatch_out")." AS dispatch_out,
                ".dt_sql_time_format("{$this->table}.time_in")." AS time_in,
                ".dt_sql_time_format("{$this->table}.time_out")." AS time_out,
                ".dt_sql_datetime_format("{$this->table}.created_at")." AS dispatched_at
            ";
        }

        if ($joinSchedule) {
            $columns .= ",
                {$scheduleModel->table}.id AS schedule_id,
                {$scheduleModel->table}.job_order_id,
                {$scheduleModel->table}.title AS schedule,
                {$scheduleModel->table}.description,
                {$scheduleModel->table}.start,
                {$scheduleModel->table}.end,
                {$scheduleModel->table}.type,
            ";
        }

        return $columns;
    }

    // Join dispatch_view
    public function joinView($builder = null, $type = 'left')
    {      
        $builder ?? $builder = $this;
        $builder->join($this->view, "{$this->table}.id = {$this->view}.dispatch_id", $type);
        return $this;
    }

    // Join schedules
    public function joinSchedule($builder, $model = null, $type = 'left')
    {      
        $model ?? $model = new ScheduleModel();
        $builder->join($model->table, "{$this->table}.schedule_id = {$model->table}.id", $type);
        return $this;
    }
    
    // Get dispatch data - either by id or all
    public function getDispatch($id = null, $dateTimeformmated = false, $joinSchedule = false)
    {
        $builder = $this->select($this->_columns($dateTimeformmated, $joinSchedule));
        
        $this->joinView($builder);

        if ($joinSchedule) $this->joinSchedule($builder);
        
        $builder->where("{$this->table}.deleted_at IS NULL");

        return $id ? $builder->find($id) : $builder->findAll();
    }

    // For dataTables
    public function noticeTable($request) 
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->_columns(true, true, true));

        // Join with other tables
        $this->joinView($builder);
        $this->joinSchedule($builder);

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions, false);

            if (check_permissions($permissions, 'PRINT')) {
                $print_url = site_url('dispatch/print/') . $row[$id];
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                EOF;
            }

            return $buttons;
        };
        
        return $closureFun;
    }

    // DataTable action buttons
    public function serviceTypeFormat()
    {
        $closureFun = function($row) {
            return get_dispatch_services($row['service_type']);
        };
        
        return $closureFun;
    }
}
