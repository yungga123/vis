<?php

namespace App\Traits;

use App\Models\AccountModel;
use App\Models\EmployeeModel;

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

        $builder->join($table, "{$column} = {$fieldName}", $type);
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
    public function joinEmployee(
        $builder, 
        $columnName, 
        $fieldName, 
        $alias = '', 
        $type = 'left', 
        $is_view = false
    )
    {
        $model  = $this->initEmployeeModel();
        $table  = $is_view ? $model->view : $model->table;
        $table  = empty($alias) ? $table : "{$table} AS $alias";
        $column = empty($alias) ? "{$table}.{$columnName}" : "{$alias}.{$columnName}";

        $builder->join($table, "{$column} = {$fieldName}", $type);
        return $builder;
    }
}