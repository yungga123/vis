<?php

namespace App\Traits;

use App\Models\SuppliersModel;

trait PurchasingTrait
{
    /**
     * Fetching/searching job order by quotation number
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
}