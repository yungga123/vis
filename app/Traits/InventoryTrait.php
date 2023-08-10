<?php

namespace App\Traits;

use App\Models\PRFItemModel;
use App\Models\InventoryModel;
use App\Models\JobOrderModel;
use CodeIgniter\Database\RawSql;

trait InventoryTrait
{
    /**
     * Fetching/searching job order by quotation number
     *
     * @param string $q         The query to search for
     * @param array $options    Identifier for the options - pagination or not
     * @param string $fields    Columns or fields in the select
     * @return array            The results of the search
     */
    public function fetchJobOrders($q, $options = [], $fields = '')
    {
        $model      = new JobOrderModel();
        $table      = $model->table;
        $join       = $model->tableJoined;
        $fields     = $fields ? $fields : $model->selectedColumns(true);
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
     * @param array $options    Identifier for the options - pagination or not
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
     * Fetch prf items (inventory)
     *
     * @param int $prf_id       The prf_id to search for
     * @param bool $join        Identifier if join with inventory
     * @param bool $with_view   Identifier if join with inventory_view
     * @param string $fields    Columns or fields in the select
     * @return array            The items result
     */
    public function traitFetchPrfItems($prf_id, $join = false, $with_view = false, $fields = '')
    {
        $model      = new PRFItemModel();
        $fields     = $fields ? $fields : $model->columns();
        $fields     = $join ? $fields .','. $model->inventoryColumns($with_view) : $fields;
        $builder    = $model->select($fields);

        if ($join) $model->joinInventory($builder, $with_view);
        $builder->where($model->table.'.prf_id', $prf_id);
        return $builder->findAll();
    }

    /**
     * To update the inventory stock/quantity
     *
     * @param int|array $id             The inventory primary id(s)
     * @param int|double|array $stock   Stock/quantity to add or minus
     * @param string $action            Either 'ITEM_IN' or 'ITEM_OUT'
     * @return void            
     */
    public function traitUpdateInventoryStock($id, $stock, $action)
    {
        $model      = new InventoryModel();
        $sign       = $action === 'ITEM_OUT' ? '-' : '+';
        $builder    = $model->db->table($model->table);

        $builder->set('stocks', "stocks $sign ". $stock, false);
        $builder->where('id', $id)->update();
    }

    /**
     * Check if available stocks is less than the quantity out
     *
     * @param mixed $available      The current available stocks
     * @param mixed $quantity_out   The items quantity to be out
     * @return bool            
     */
    public function traitIsStocksLessThanQuantityOut($available, $quantity_out)
    {
       if (is_array($available)) {
            for ($i=0; $i < count($available); $i++) { 
                if (floatval($available[$i]) < floatval($quantity_out[$i]))
                    return true;
            }
       } 
       
       return floatval($available) < floatval($quantity_out);
    }
}