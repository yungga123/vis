<?php

namespace App\Traits;

use App\Models\InventoryModel;
use App\Models\JobOrderModel;

trait InventoryTrait
{
    /**
     * Fetching/searching job order by quotation number
     *
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchJobOrders($q, $options = [], $fields = '')
    {
        $model      = new JobOrderModel();
        $table      = $model->table;
        $join       = $model->tableJoined;
        $fields     = $fields ? $fields : $this->selectedColumns();
        $builder    = $model->select($fields);

        $builder->_join($builder);
        $builder->whereIn("{$table}.status", ['accepted', 'filed']);

        if (! empty($q)) {
            if (empty($options)) {                
                $builder->where("{$table}.id", $q);
                return $builder->find();
            }

            $builder->like("{$join}.quotation_num", $q);
        }

        $builder->orderBy("{$table}.id", 'DESC');
        $result = $builder->paginate($options['perPage'], 'default', $options['page']);
        $total  = $builder->countAllResults();

        return [
            'data'   => $result,
            'total'  => $total
        ];     
    }

    /**
     * Fetching/searching items (inventory) by model & description
     *
     * @param string $q         The query to search for
     * @param string $options   Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchMatestlist($q, $options = [], $fields = '')
    {
        $model  = new InventoryModel();
        $fields = $fields ? $fields : "
            id,
            CONCAT(id, ' | ', item_model, ' | ',item_description) AS text,
            item_model,
            item_description,
            stocks,
            category_name,
            subcategory_name,
            brand
        ";
        $builder = $model->select($fields);
        $model->joinView($builder);
        $builder->where('stocks !=', 0);

        if (! empty($q)) {
            if (empty($options)) {                
                $builder->where('id', $q);
                return $builder->find();
            }

            $builder->like('item_model', $q);
            $builder->like('item_description', $q);
        }

        $builder->orderBy('id', 'ASC');
        $result = $builder->paginate($options['perPage'], 'default', $options['page']);
        $total = $builder->countAllResults();

        return [
            'data'  => $result,
            'total'  => $total
        ];     
    }

    /**
     * To update the inventory stock/quantity
     *
     * @param int $id           The inventory primary id
     * @param int|double $stock Stock/quantity to add or minus
     * @param string $action    Either 'ITEM_IN' or 'ITEM_OUT'
     * @return void            
     */
    public function traitUpdateInventoryStock($id, $stock, $action)
    {
        $model      = new InventoryModel();
        $sign       = $action === 'ITEM_OUT' ? '-' : '+';
        $builder    = $model->db->table($model->table);

        $builder->where('id', $id);
        $builder->set('stocks', "stocks $sign ". $stock, false);
        $builder->update();
    }
}