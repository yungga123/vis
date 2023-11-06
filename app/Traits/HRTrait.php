<?php

namespace App\Traits;

use App\Models\AccountModel;

trait HRTrait
{
    /**
     * Join specified table to accounts
     *
     * @param mixed $builder    The database builder or model
     * @param string $fieldName The column name with the prefix table (eg. table.column)
     * @param string $alias     The alias that will be used for the accounts table
     * @param string $type      The join type (eg. 'left' for left join)
     * 
     * @return void
     */
    public function joinAccountView($builder, $fieldName, $alias, $type = 'left')
    {
        $model = new AccountModel();
        $builder->join("{$model->view} AS $alias", "{$alias}.username = {$fieldName}", $type);
    }
}