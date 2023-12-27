<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;

class PayrollModel extends Model
{
    /* Declare trait here to use */
    use HRTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'payroll';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'cutoff_start',
        'cutoff_end',
        'gross_pay',
        'net_pay',
        'salary_type',
        'basic_salary',
        'cutoff_pay',
        'daily_rate',
        'hourly_rate',
        'working_days',
        'notes',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'employee_id'     => [
            'rules' => 'required|max_length[100]',
            'label' => 'employee name',
        ],
        'cutoff_start'   => [
            'rules' => 'required',
            'label' => 'cut-off start date',
        ],
        'cutoff_end'   => [
            'rules' => 'required',
            'label' => 'cut-off end date',
        ],
        'gross_pay'   => [
            'rules' => 'required',
            'label' => 'gross pay',
        ],
        'net_pay'   => [
            'rules' => 'required',
            'label' => 'net pay',
        ],
        'salary_type'   => [
            'rules' => 'required',
            'label' => 'salary type',
        ],
        'basic_salary'   => [
            'rules' => 'required',
            'label' => 'basic salary',
        ],
        'cutoff_pay'   => [
            'rules' => 'required',
            'label' => 'cut-off pay',
        ],
        'daily_rate'   => [
            'rules' => 'required',
            'label' => 'daily rate',
        ],
        'hourly_rate'   => [
            'rules' => 'required',
            'label' => 'hourly rate',
        ],
        'notes'   => [
            'rules' => 'permit_empty|max_length[500]',
            'label' => 'notes',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set the value for created_by before inserting
     */
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');

        return $data;
    }

    /**
     * For fetching single data
     * 
     * @param bool $byPKId  Fetch by primary id - default true
     */
    public function fetch(string|int $id, string|array $columns = '', $byPKId = true): array|null
    {
        $columns    = $columns ? $columns :  [$this->primaryKey] + $this->allowedFields;
        $field      = $byPKId ? 'id' : 'employee_id';
        
        $this->select($columns);
        $this->where("{$this->table}.{$field}", $id);
        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->first();
    }

    /**
     * For fetching multiple data
     * 
     * @param string|array $columns
     * @param bool $byPKId  Fetch by primary id - default true
     */
    public function fetchAll(array $id = [], $columns = '', $byPKId = true, int $limit = 0, int $offset = 0): array
    {
        $columns = $columns ? $columns : [$this->primaryKey] + $this->allowedFields;
        
        $this->select($columns);

        if (! empty($id)) {
            $field = $byPKId ? 'id' : 'employee_id';
            $this->whereIn("{$this->table}.{$field}", $id);
        }

        $this->where("{$this->table}.deleted_at IS NULL");

        return $this->findAll($limit, $offset);
    }

    /**
     * Check if save payroll already exist
     */
    public function checkPayroll(string $employee_id, string $start_date, string $end_date): bool
    {
        $this->select('id');
        $this->where("{$this->table}.employee_id", $employee_id);
        $this->where("{$this->table}.cutoff_start", $start_date);
        $this->where("{$this->table}.cutoff_end", $end_date);
        $this->where("{$this->table}.deleted_at IS NULL");

        return (! empty($this->first()));
    }

    /**
     * For DataTable
     */
    public function noticeTable(array $request, $permissions): object
    {
        $model      = new EmployeeViewModel();
        $start      = dt_sql_date_format("{$this->table}.cutoff_start");
        $end        = dt_sql_date_format("{$this->table}.cutoff_end");
        $columns    = "
            {$this->table}.id,
            {$this->table}.employee_id,
            {$model->table}.employee_name,
            {$model->table}.position,
            CONCAT_WS(' - ', {$start}, {$end}) AS cutoff_period,
            ".dt_sql_number_format("{$this->table}.gross_pay")." AS gross_pay,
            ".dt_sql_number_format("{$this->table}.net_pay")." AS net_pay,
            ".dt_sql_number_format("{$this->table}.cutoff_pay")." AS cutoff_pay,
            {$this->table}.salary_type,
            CONCAT({$this->table}.working_days, ' Days') AS working_days,
            {$this->table}.notes,
            cb.employee_name AS processed_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS processed_at
        ";
        $builder    = $this->db->table($this->table);

        $builder->select($columns);

        $this->traitJoinEmployees($builder, 'employee_id', "{$this->table}.employee_id", '', 'left', true);
        $this->joinAccountView($builder, 'created_by', 'cb');

        // Not include dev record
        if (! is_developer()) {
            $builder->where("{$this->table}.employee_id !=", DEVELOPER_ACCOUNT);
        }

        if (! in_array(ACTION_VIEW_ALL, $permissions)) {
            $builder->where("{$this->table}.employee_id", session('employee_id'));
        }
        
        // Date range filter
        $start_date = $request['params']['start_date'] ?? '';
        $end_date   = $request['params']['end_date'] ?? '';

        if (! empty($start_date) && ! empty($end_date)) {
            $start_date     = format_date($start_date, 'Y-m-d');
            $end_date       = format_date($end_date, 'Y-m-d');
            $between_start  = "{$this->table}.cutoff_start BETWEEN '%s' AND '%s'";
            $between_end    = "{$this->table}.cutoff_end BETWEEN '%s' AND '%s'";

            $builder->where(sprintf($between_start, $start_date, $start_date));
            $builder->where(sprintf($between_end, $end_date, $end_date));
        }

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    /**
     * DataTable action buttons
     */
    public function buttons(array $permissions, $can_view_all)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions, $can_view_all) {
            $buttons = '';

            if (check_permissions($permissions, ACTION_EDIT)) {
                // Edit
                $buttons .= dt_button_html([
                    'text'      => '',
                    'button'    => 'btn-warning',
                    'icon'      => 'fas fa-edit',
                    'condition' => '',
                    'link'      => url_to('payroll.computation.home'). "?id={$row[$id]}",
                ]);
            }

            // Delete
            $buttons .= dt_button_actions($row, $id, $permissions, false, ['exclude_edit']);
            
            // Print
            $print_url  = site_url('payroll/payslip/print/') . $row[$id];
            $print      = <<<EOF
                <a href="$print_url" class="btn btn-success btn-sm" target="_blank" title="Print Payslip"><i class="fas fa-print"></i></a>
            EOF;

            // If can't view all print only
            if (! $can_view_all) return $print;

            return $buttons . $print;
        };
        
        return $closureFun;
    }
}