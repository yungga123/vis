<?php

namespace App\Traits;

use App\Models\SuppliersModel;
use App\Models\RequestPurchaseFormModel;

trait PurchasingTrait
{
    /**
     * Fetching/searching suppliers
     *
     * @param string $q         The query to search for
     * @param array $options    Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchSuppliers($q, $options = [], $fields = '')
    {
        $model      = new SuppliersModel();
        $fields     = $fields ? $fields : 'id, supplier_name AS text';
        $builder    = $model->select($fields);

        if (! empty($q)) {
            if (empty($options)) {                
                $builder->where('id', $q);
                return $builder->find();
            }

            $builder->like('supplier_name', $q);
        }

        $builder->orderBy('supplier_name', 'ASC');
        $result = $builder->paginate($options['perPage'], 'default', $options['page']);
        $total  = $builder->countAllResults();

        return [
            'data'   => $result,
            'total'  => $total
        ];     
    }

    /**
     * Fetching/searching RPF by id
     *
     * @param string $q         The query to search for
     * @param array $options    Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchRpf($q, $options = [], $fields = '')
    {
        $status     = ['reviewed'];
        $model      = new RequestPurchaseFormModel();
        $fields     = $fields ? $fields : "
            {$model->table}.id AS id, 
            CONCAT_WS(' | ', {$model->table}.id, {$model->view}.created_by_name) AS text
        ";
        $builder    = $model->select($fields);

        $model->join($model->view, "{$model->view}.rpf_id = {$model->table}.id", 'left');

        if (! empty($q)) {
            $builder->like("{$model->table}.id", $q);
        }

        $builder->whereIn("{$model->table}.status", $status);
        $builder->orderBy("{$model->table}.id", 'DESC');

        $result = $builder->paginate($options['perPage'], 'default', $options['page']);
        $total  = $builder->countAllResults();

        return [
            'data'   => $result,
            'total'  => $total
        ];     
    }
}