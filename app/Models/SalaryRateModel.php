<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

class SalaryRateModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'salary_rates';
    protected $primaryKey       = 'employee_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['employee_id', 'rate_type', 'salary_rate', 'is_current'];

    // Dates
    protected $useTimestamps = true;
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
        'salary_rate'   => [
            'rules' => 'required',
            'label' => 'salary rate',
        ],
        'rate_type'   => [
            'rules' => 'required|max_length[50]',
            'label' => 'rate type',
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
     */
    public function fetch(string $employee_id, string|array $columns = ''): array|null
    {
        $columns = $columns ? $columns : $this->allowedFields;
        
        $this->select($columns);
        $this->where("{$this->table}.deleted_at IS NULL");
        $this->where("{$this->table}.employee_id", $employee_id);

        return $this->first();
    }

    /**
     * For fetching multiple data
     * 
     * @param string|array $columns
     */
    public function fetchAll(array $employee_id = [], $columns = '', int $limit = 0, int $offset = 0): array
    {
        $columns = $columns ? $columns : $this->allowedFields;
        
        $this->select($columns);
        $this->where("{$this->table}.deleted_at IS NULL");

        if (! empty($employee_id)) 
            $this->whereIn("{$this->table}.employee_id", $employee_id);

        return $this->findAll($limit, $offset);
    }

    /**
     * For updating data
     */
    public function modify(string|array $employee_id, array $data): bool
    {
        $data   = clean_param($data);
        $where  = is_array($employee_id) ? 'whereIn' : 'where'; 
        
        $this->set($data)->{$where}("{$this->table}.employee_id", $employee_id);

        return $this->update();
    }

    /**
     * For deleting data
     */
    public function remove(string|array $employee_id)
    {
        $where = is_array($employee_id) ? 'whereIn' : 'where'; 
        
        $this->{$where}("{$this->table}.employee_id", $employee_id);

        return $this->delete();
    }

    /**
     * For joining with employees table
     * 
     * @param object|null $builder 
     * @param object|null|\Model $model 
     * @param string $type  Join type
     */
    public function joinEmployees($builder = null, $model = null, $type = 'left'): self
    {      
        $model ??= new EmployeeModel();

        ($builder ?? $this)
            ->join($model->table, "{$this->table}.employee_id = {$model->table}.employee_id", $type);

        return $this;
    }

    /**
     * For joining with employees_view view
     * 
     * @param object|null $builder 
     * @param object|null|\Model $model 
     * @param string $type  Join type
     */
    public function joinEmployeesView($builder = null, $model = null, $type = 'left'): self
    {      
        $model ??= new EmployeeViewModel();
        
        ($builder ?? $this)
            ->join($model->table, "{$this->table}.employee_id = {$model->table}.employee_id", $type);

        return $this;
    }

    /**
     * For DataTable
     */
    public function noticeTable(array $request = []): object
    {
        $model      = new EmployeeViewModel();
        $columns    = "
            {$this->table}.employee_id,
            {$model->table}.employee_name,
            {$model->table}.position,
            {$model->table}.employment_status,
            {$this->table}.rate_type,
            ".dt_sql_number_format("{$this->table}.salary_rate")." AS salary_rate,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at
        ";
        $builder    = $this->db->table($this->table);

        $builder->select($columns);     

        $this->joinEmployeesView($builder, $model);
        $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');
        $this->filterParam($request, $builder, 'rate_type', 'rate_type');
        
        $builder->orderBy("{$model->table}.employee_name", 'ASC');

        return $builder;
    }

    /**
     * DataTable action buttons
     */
    public function buttons(array $permissions)
    {
        $id         = 'employee_id';
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions);
            return $buttons;
        };
        
        return $closureFun;
    }
}
