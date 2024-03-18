<?php

namespace App\Traits;

use App\Models\CustomerModel;
use App\Models\CustomerBranchModel;

trait ClientTrait
{
    /**
     * Fetch customers
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchCustomers($q, $options = [], $fields = '')
    {
        $model  = new CustomerModel();
        $type   = strtoupper($options['customer_type']);
        $fields = $fields ? $fields : "{$model->table}.id, {$model->table}.name AS text";

        $model->select($fields);
        $model->join('customer_branches AS cb', "cb.customer_id = {$model->table}.id", 'left');
        $model->where('type', $type);
        $model->where("{$model->table}.deleted_at IS NULL");

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            if (is_numeric($q)) {
                $model->where("{$model->table}.id", $q);
            } else {
                $model->like("LOWER({$model->table}.name)", strtolower($q));
            }
        }

        $model->groupBy("{$model->table}.id, {$model->table}.name");
        $model->orderBy("{$model->table}.id", 'DESC');

        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total  = $model->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
        ];
    }

    /**
     * Fetch customer branches either all or via customer id
     * 
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchCustomerBranches($q, $options = [], $fields = '')
    {
        $model  = new CustomerBranchModel();
        $fields = $fields ? $fields : 'id, branch_name AS text';

        $model->select($fields);
        $model->where('customer_id', $options['customer_id']);
        $model->where('deleted_at IS NULL');

        if (isset($options['not_select2_ajax'])) {
            return json_encode(['data' => $model->findAll()]);
        }

        if (! empty($q)) {
            if (empty($options)) return $model->find($q);

            if (is_numeric($q)) {
                $model->where('id', $q);
            } else {
                $model->like('LOWER(branch_name)', strtolower($q));
            }
        }
        
        $model->orderBy('branch_name', 'ASC');
        
        $result = $model->paginate($options['perPage'], 'default', $options['page']);
        $total  = $model->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
        ];
    }

    /**
     * Join with customers
     * 
     * @param object $builder       The database builder or model
     * @param object|null $model    Customer model class or null
     * @param boolean $branch       Whether to join with customer_branches table or not
     * @param string $type          The join type (eg. 'left' for left join)
     * 
     * @return $this
     */
    public function joinCustomers($builder, $model = null, $branch = false, $type = 'left')
    {      
        $model ??= new CustomerModel();
        $table  = $builder->getTable();
        $type   = empty($type) ? 'left' : $type;

        $builder->join($model->table, "{$table}.customer_id = {$model->table}.id", $type);

        if ($branch) {
            $branchModel = new CustomerBranchModel();

            $builder->join($branchModel->table, "({$table}.customer_branch_id = {$branchModel->table}.id AND {$table}.customer_branch_id IS NOT NULL)", 'left');
        }

        return $this;
    }
}