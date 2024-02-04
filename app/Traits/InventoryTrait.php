<?php

namespace App\Traits;

use App\Models\PRFItemModel;
use App\Models\InventoryModel;
use App\Models\InventoryLogsModel;
use App\Models\JobOrderModel;

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
        $fields     = $fields ? $fields : $model->selectedColumns(true);
        $builder    = $model->select($fields);

        $builder->joinWithOtherTables($builder);

        if (! empty($q)) {
            if (empty($options)) {                
                $builder->where("{$table}.id", $q);
                return $builder->first();
            }

            if (is_numeric($q)) {
                $builder->like("{$table}.id", $q);
            } else {
                $builder->orLike("{$model->view}.quotation", $q);
            }            
        }

        $builder->whereIn("{$table}.status", ['accepted', 'filed']);
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
            {$model->table}.id,
            CONCAT_WS(' | ', {$model->table}.id, {$model->table}.item_model, {$model->table}.item_description, IF({$model->view}.size IS NULL, 'N/A', {$model->view}.size)) AS text,
            {$model->table}.item_model,
            {$model->table}.item_description,
            {$model->table}.stocks,
            {$model->view}.category_name,
            {$model->view}.subcategory_name,
            {$model->view}.brand,
            {$model->view}.unit,
            {$model->view}.size,
            {$model->view}.supplier_name
        ";
        $builder = $model->select($fields);
        
        $model->joinView($builder);

        if (! empty($q)) {
            if (empty($options)) {                
                $builder->where('id', $q);
                return $builder->find();
            }

            if (is_numeric($q)) {
                $builder->like("{$model->table}.id", $q);
            } else {
                $builder->like("{$model->table}.item_model", $q);
                $builder->orLike("{$model->table}.item_description", $q);
                $builder->orLike("{$model->view}.supplier_name", $q);
            }
        }

        $builder->orderBy("{$model->table}.id", 'ASC');

        $result = $builder->paginate($options['perPage'], 'default', $options['page']);
        $total  = $builder->countAllResults();

        return [
            'data'  => $result,
            'total' => $total
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
    public function traitFetchPrfItems($id, $join = false, $with_view = false, $fields = '')
    {
        $model      = new PRFItemModel();
        $fields     = $fields ? $fields : $model->columns();
        $fields     = $join && $fields ? $fields .','. $model->inventoryColumns($with_view) : $fields;
        $builder    = $model->select($fields);

        if ($join) $this->joinInventory($model->table, $builder, $with_view);
        if ($id && is_array($id)) 
            return $builder->whereIn($model->table.'.prf_id', $id)->findAll();

        return $builder->where($model->table.'.prf_id', $id)->findAll();
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
        if ($id && ! empty($stock)) {
            $model      = new InventoryModel();
            $sign       = $action === 'ITEM_OUT' ? '-' : '+';
            $builder    = $model->db->table($model->table);
    
            $builder->set('stocks', "stocks {$sign} ". $stock, false);
            $builder->where('id', $id)->update();
        }
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

    /**
     * Save the inventory logs
     *
     * @param array $data   The data to be inserted
     * @return void            
     */
    public function saveInventoryLogs($data)
    {
        if (! empty($data)) {
            // Add inventory logs
            $invLogModel = new InventoryLogsModel();
            // Check if $data is multi-dimesional array
            // and use insert batch, otherwise
            is_array_multi_dimen($data) ? $invLogModel->insertBatch($data) 
                    : $invLogModel->insert($data);
        }
    }

    /**
     * Get inventory items via primary id
     *
     * @param int|array|null $id    The id(s) to be search
     * @param array $columns        Columns to be displayed
     * @param array $joinView       Identifier if join with inventory_view
     * @return void            
     */
    public function getInventoryItems($id = null, $columns = '', $joinView = false)
    {
        $model      = new InventoryModel();
        $columns    = $columns ? $columns : $model->columns($joinView, true);
        $builder    = $model->select($columns);

        if ($joinView) $model->joinView($builder);
        if ($id && is_array($id)) return $builder->whereIn('id', $id)->findAll();
        return $id ? $builder->find($id) : $builder->findAll();
    }

    /**
     * Check if prf items quantity out is greater than the current stocks
     *
     * @param int|array $id    The id(s) to be search
     * @return bool            
     */
    public function checkPrfItemsOutNStocks($id)
    {
        $columns    = "prf_items.quantity_out, inventory.stocks";
        $items      = $this->traitFetchPrfItems($id, true, false, $columns);

        if (! empty($items)) {
            foreach ($items as $val) {
                if (floatval($val['stocks']) < floatval($val['quantity_out']))
                    return true;
            }
        }

        return false;
    }

    /**
     * Join with inventory table and inventory_view
     *
     * @param string $table     Table to be joined with inventory
     * @param object $builder   The builder (db/model object) to use to join the tables
     * @param bool $withView    Identifier if also join with inventory_view
     * @return void            
     */
    public function joinInventory($table, $builder, $withView = false)
    {
        $inventoryModel = new InventoryModel();        
        // Join with inventory table
        $builder->join($inventoryModel->table, "{$table}.inventory_id = {$inventoryModel->table}.id", 'left');
        // Then join inventory with inventory_View
        if ($withView) $inventoryModel->joinView($builder);
    }
}