<?php

namespace App\Models;

use App\Traits\FilterParamTrait;
use App\Traits\HRTrait;
// use CodeIgniter\Model;

class ServiceReportModel extends BaseModel
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'service_reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'job_order_id',
        'serial_number',
        'server_type',
        'service_type',
        'area',
        'arrival_at',
        'error_description',
        'corrective_action',
        'remarks',
        'labor',
        'travel_time',
        'time_out',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'status' => [
            'rules' => 'permit_empty',
            'label' => 'status'
        ],
        'job_order_id' => [
            'rules' => 'required|if_exist',
            'label' => 'job order'
        ],
        'serial_number' => [
            'rules' => 'permit_empty',
            'label' => 'serial number'
        ],
        'server_type' => [
            'rules' => 'permit_empty',
            'label' => 'server type'
        ],
        'service_type' => [
            'rules' => 'required|if_exist',
            'label' => 'service type'
        ],
        'arrival_date' => [
            'rules' => 'required|if_exist',
            'label' => 'arrival date'
        ],
        'arrival_time' => [
            'rules' => 'required|if_exist',
            'label' => 'arrival time'
        ],
        'error_description' => [
            'rules' => 'required|if_exist',
            'label' => 'error description'
        ],
        'corrective_action' => [
            'rules' => 'required|if_exist',
            'label' => 'corrective action'
        ],
        'remarks' => [
            'rules' => 'permit_empty'
        ],
        'labor' => [
            'rules' => 'required|if_exist',
            'label' => 'labor'
        ],
        'travel_time' => [
            'rules' => 'required|if_exist',
            'label' => 'travel time'
        ],
        'time_out' => [
            'rules' => 'required|if_exist',
            'label' => 'time out'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses   = ['accepted', 'filed'];

    /**
     * Fetch records
     * 
     * @param string|int|array $id
     */
    public function fetch($id, $joinJobOrders = false): array|null
    {
        $joModel = new JobOrderModel();
        $columns = array_merge([$this->primaryKey], $this->allowedFields);

        if ($joinJobOrders) {
            $columns = array_map(function ($column) {  return "{$this->table}.{$column}"; }, $columns);
            $columns = implode(',', $columns);
            $columns .= ",
                {$joModel->view}.client_id,
                {$joModel->view}.client_name,
                {$joModel->view}.client_branch_id,
                {$joModel->view}.client_branch_name,
            ";
        }

        $builder = $this->select($columns);

        if ($joinJobOrders) $this->joinJobOrders($builder, $joModel, true);

        $builder->where("{$this->table}.deleted_at IS NULL");

        if (is_array($id)) {
            $builder->whereIn("{$this->table}.id", $id);
            
            return $this->findAll();
        }

        $builder->where("{$this->table}.id", $id);

        return $builder->first();
    }

    /**
     * Join table with job_orders table or view
     */
    public function joinJobOrders($builder = null, $model = null, $view = true)
    {
        $model ??= new JobOrderModel();
        $table = $view ? $model->view : $model->table;
        $field = $view ? 'job_order_id' : $model->primaryKey;

        ($builder ?? $this)
            ->join($table, "{$table}.{$field} = {$this->table}.job_order_id", 'left');

        return $this;
    }

    /**
     * For DataTable columns format
     */
    public function dtColumns($is_print = false): string
    {
        $joModel    = new JobOrderModel();
        $status     = $is_print ? "UPPER({$this->table}.status)" : "{$this->table}.status";
        $columns    = "
            {$status},
            {$this->table}.id,
            {$this->table}.job_order_id,
            {$joModel->view}.client_name,
            {$joModel->view}.client_branch_name,
            {$this->table}.serial_number,
            {$this->table}.server_type,
            {$this->table}.service_type,
            {$this->table}.area,
            ".dt_sql_datetime_format("{$this->table}.arrival_at")." AS arrival_at,
            {$this->table}.error_description,
            {$this->table}.corrective_action,
            {$this->table}.remarks,
            {$this->table}.labor,
            {$this->table}.travel_time,
            ".dt_sql_time_format("{$this->table}.time_out")." AS time_out,
            {$this->table}.remarks,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at,
            ab.employee_name AS accepted_by,
            ".dt_sql_datetime_format("{$this->table}.accepted_at")." AS accepted_at,
            fb.employee_name AS filed_by,
            ".dt_sql_datetime_format("{$this->table}.filed_at")." AS filed_at,
        ";

        return $columns;
    }

    /**
     * For DataTable
     */
    public function noticeTable(array $request): object
    {
        $builder = $this->db->table($this->table);

        $builder->select($this->dtColumns());

        $this->joinJobOrders($builder, null, true);
        $this->joinAccountView($builder, 'created_by', 'cb');
        $this->joinAccountView($builder, 'accepted_by', 'ab');
        $this->joinAccountView($builder, 'filed_by', 'fb');
        
        $this->filterParam($request, $builder);
        $this->filterParam($request, $builder, 'area', 'area');
        $this->filterParam($request, $builder, 'service_type', 'service_type');

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    /**
     * DataTable action buttons
     */
    public function buttons(array $permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $title      = 'Service Report';
        $closureFun = function($row) use($id, $permissions, $dropdown, $title) {
            $buttons        = dt_button_actions($row, $id, $permissions, $dropdown);
            $allowedToPrint = ['accepted', 'filed'];

            if ($row['status'] === 'pending') {
                if (check_permissions($permissions, 'ACCEPT')) {
                    // Accept Service Report
                    $changeTo = 'accept';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-primary',
                        'icon'      => 'fas fa-check-circle',
                        'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                    ], $dropdown);
                }
            }

            if (check_permissions($permissions, 'FILE') && $row['status'] === 'accepted') {
                // File Service Report
                $changeTo = 'file';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-archive',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (
                check_permissions($permissions, 'PRINT') && 
                in_array($row['status'], $allowedToPrint)
            ) {
                $print_url = url_to('admin.service_report.print', $row[$id]);
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-dark btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                EOF;
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
    }

    /**
     * DataTable view units & items
     */
    public function dtViewUnitNItems()
    {
         $id         = $this->primaryKey;
         $closureFun = function($row) use($id) { 
            return <<<EOF
                <button class="btn btn-sm btn-primary" onclick="view({$row[$id]})" title="View Units & Items"><i class="fas fa-eye"></i> View</button>
            EOF;
        };
        
        return $closureFun;
    }

    /**
     * DataTable status formatter
     */
    public function dtStatusFormat()
    {
        $closureFun = function($row) {
            $text    = ucwords($row['status']);
            $color   = $row['status'] === 'filed' ? 'success' : dt_status_color($row['status']);
            
            return text_badge($color, $text);
        };
        
        return $closureFun;
    }
}
