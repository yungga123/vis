<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    /**
     * Set the value for created_by before inserting
     */
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');

        return $data;
    }

    /**
     * Set the value for 'status_' by and at before updating status
     */
    protected function setStatusByAndAt(array $data)
    {
        if (isset($data['data']['status'])) {
            $status = $data['data']['status'];

            $data['data'][$status .'_by'] = session('username');
            $data['data'][$status .'_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    /**
     * Check if record exists via primary id/key
     */
    public function exists($id)
    {
        $builder = $this->select('id');

        $builder->where('deleted_at', null);

        return !empty($builder->find($id));
    }

    /**
     * Count records
     */
    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();

        return $builder->where('status', strtolower($param))->countAllResults();
        
    }
    /**
     * Join other table with customers 
     * based on the params pass
     * 
     * @param object $builder           Database builder or model object
     * @param string|null $table        Table/view name (either with column name) to join with customers table
     * @param string|boolean $branch    Whether to inculde customer branch or not (possible to pass table with column name)
     * @param string $type              Type of join - default left
     * 
     * @return object|array|null        Return the builder
     */
    public function joinCustomers($builder, $table = null, $branch = false, $type = 'left')
    {      
        $cmodel = new CustomerModel();
        $table  ??= $builder->getTable();
        $table  = empty(explode('.', $table)) ? "{$table}.customer_id" : $table;

        $builder->join($cmodel->table, "{$table} = {$cmodel->table}.id", $type);

        if ($branch) {
            $branchModel = new CustomerBranchModel();
            $branchTable = is_string($branch) && ! empty(explode('.', $table))
                ? $branch : "{$table}.customer_branch_id";

            $builder->join($branchModel->table, "({$branchTable} = {$branchModel->table}.id AND {$branchTable} IS NOT NULL)", 'left');
        }

        return $builder;
    }

}