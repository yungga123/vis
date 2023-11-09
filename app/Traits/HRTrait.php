<?php

namespace App\Traits;

use App\Models\AccountModel;

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
}