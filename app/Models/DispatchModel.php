<?php

namespace App\Models;

use CodeIgniter\Model;
// use App\Models\DispatchedTechniciansModel;

class DispatchModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'dispatch';
    protected $view             = 'dispatch_view';
    protected $tableSchedules   = 'schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'schedule_id',
        'customer_id',
        'customer_type',
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
        'customer_id' => [
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
    private function _columns($dateTimeformmated = false, $joinSchedule = false, $withClientDetials = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.schedule_id,
            {$this->table}.customer_id,            
            {$this->table}.sr_number,
            {$this->table}.dispatch_date,
            {$this->table}.dispatch_out,
            {$this->table}.time_in,
            {$this->table}.time_out,
            {$this->table}.remarks,
            {$this->table}.comments,
            {$this->table}.service_type,
            {$this->table}.with_permit,
            {$this->table}.created_by,
            {$this->table}.checked_by,
            {$this->table}.customer_type,
            {$this->view}.customer,
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
                DATE_FORMAT({$this->table}.dispatch_date, '%b %e, %Y') AS dispatch_date,
                TIME_FORMAT({$this->table}.dispatch_out, '%h:%i %p') AS dispatch_out,
                TIME_FORMAT({$this->table}.time_in, '%h:%i %p') AS time_in,
                TIME_FORMAT({$this->table}.time_out, '%h:%i %p') AS time_out
            ";
        }

        if ($joinSchedule) {
            $columns .= ",
                {$this->tableSchedules}.title AS schedule,
                {$this->tableSchedules}.title,
                {$this->tableSchedules}.description,
                {$this->tableSchedules}.start,
                {$this->tableSchedules}.end,
                {$this->tableSchedules}.type,
            ";
        }

        if ($withClientDetials) {
            $columns .= ",
                {$this->view}.contact_person,
                {$this->view}.contact_number,
                {$this->view}.email_address,
                {$this->view}.address
            ";
        }

        return $columns;
    }
    
    // Join with schedules table 
    private function _join($builder, $joinSchedule = false)
    {
        if ($joinSchedule) 
            $builder->join($this->tableSchedules, "{$this->table}.schedule_id = {$this->tableSchedules}.id");

        $builder->join($this->view, "{$this->table}.id = {$this->view}.dispatch_id");
        $builder->where("{$this->table}.deleted_at IS NULL");
    }
    
    // Get dispatch data - either by id or all
    public function getDispatch($id = null, $dateTimeformmated = false, $joinSchedule = false, $withClientDetials = false)
    {
        $builder = $this->select($this->_columns($dateTimeformmated, $joinSchedule, $withClientDetials));
        
        $this->_join($builder, $joinSchedule);
        return $id ? $builder->find($id) : $builder->findAll();
    }

    // For dataTables
    public function noticeTable($request) 
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->_columns(true, true));

        // Join with schedules table
        $this->_join($builder, true);
        $builder->orderBy('id', 'DESC');

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
