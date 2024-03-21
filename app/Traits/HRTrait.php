<?php

namespace App\Traits;

use App\Models\AccountModel;
use App\Models\EmployeeModel;
use App\Models\EmployeeViewModel;
use App\Models\SalaryRateModel;

trait HRTrait
{
    /**
     * Initialize the AccountModel
     * 
     * @return \App\Models\AccountModel
     */
    public function initAcountModel()
    {
        $model = new AccountModel();
        return $model;
    }

    /**
     * Initialize the EmployeeModel
     * 
     * @return \App\Models\EmployeeModel
     */
    public function initEmployeeModel()
    {
        $model = new EmployeeModel();
        return $model;
    }

    /**
     * Join specified table to accounts_view
     *
     * @param mixed $builder    The database builder or model
     * @param string $fieldName The column name with the prefix table (eg. table.column)
     * @param string $alias     The alias that will be used for the accounts table
     * @param string $type      The join type (eg. 'left' for left join)
     * 
     * @return $builder
     */
    public function joinAccountView($builder, $fieldName, $alias = '', $type = 'left')
    {
        $model  = $this->initAcountModel();
        $table  = empty($alias) ? $model->view : "{$model->view} AS $alias";
        $column = empty($alias) ? "{$model->view}.username" : "{$alias}.username";
        $_table = $builder->getTable();
        $_field = strpos($fieldName, '.') === false ? $_table .'.'. $fieldName : $fieldName;

        $builder->join($table, "{$column} = {$_field}", $type);
        return $builder;
    }

    /**
     * Join specified table to employees or employees_view
     *
     * @param mixed $builder        The database builder or model
     * @param string $columnName    The column name to compare with - the foreign key/column
     * @param string $fieldName     The column name with the prefix table (eg. table.column)
     * @param string $alias         The alias that will be used for the accounts table
     * @param string $type          The join type (eg. 'left' for left join)
     * @param bool $is_view         The identifier whether to use table or view
     * 
     * @return $builder
     */
    public function traitJoinEmployees(
        $builder, 
        $columnName, 
        $fieldName = '', 
        $alias = '', 
        $type = 'left', 
        $is_view = false
    )
    {
        $model  = $this->initEmployeeModel();
        $table  = $is_view ? $model->view : $model->table;
        $table  = empty($alias) ? $table : "{$table} AS $alias";
        $column = empty($alias) ? "{$table}.{$columnName}" : "{$alias}.{$columnName}";
        $_table = $builder->getTable();
        $_field = empty($fieldName) ? $_table .'.'. $columnName : $fieldName;

        $builder->join($table, "{$column} = {$_field}", $type);
        return $builder;
    }

    /**
     * Fetch employees
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchEmployees($q, $options = [], $fields = '')
    {
        $model  = new EmployeeModel();
        $modelV = new EmployeeViewModel();
        $fields = $fields ? $fields : "
            {$modelV->table}.employee_id AS id, {$modelV->table}.employee_name AS text
        ";
        $salary = $options['is_salary_rate'] ?? null;

        if (
            $salary || ($computation = $options['is_payroll_computation'] ?? null)
        ) {
            $srModel    = new SalaryRateModel();
            $fields     .= ",
                {$modelV->table}.employment_status,
                {$modelV->table}.position,
                {$srModel->table}.rate_type,
                {$srModel->table}.salary_rate,
                {$srModel->table}.payout
            ";
            
            $modelV->join($srModel->table, "{$srModel->table}.employee_id = {$modelV->table}.employee_id", 'left');
        }

        $modelV->select($fields);
        $model->withOutResigned($modelV);

        if (! empty($q)) {
            if (empty($options)) {
                $modelV->where("{$modelV->table}.employee_id", $q);
                
                return $modelV->first();
            }

            $modelV->like("{$modelV->table}.employee_id", $q);
            $modelV->orLike("{$modelV->table}.employee_name", $q);
        }

        if ($salary) {
            $modelV->where("({$srModel->table}.salary_rate IS NULL OR {$srModel->table}.rate_type IS NULL)");
        }

        $modelV->orderBy("{$modelV->table}.employee_name", 'ASC');

        $result = $modelV->paginate($options['perPage'], 'default', $options['page']);
        log_msg((string) $modelV->getLastQuery());
        $total  = $modelV->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
        ];
    }
}