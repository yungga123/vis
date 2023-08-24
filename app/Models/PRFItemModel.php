<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\InventoryTrait;

class PRFItemModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'prf_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'prf_id',
        'invetory_id',
        'quantity_out',
        'returned_q',
        'returned_date'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    // Set columns
    public function columns($concat = false)
    {
        $columns = "
            {$this->table}.inventory_id,
            {$this->table}.quantity_out,
            {$this->table}.returned_q,
            {$this->table}.returned_date,
            DATE_FORMAT({$this->table}.returned_date, '%b %e, %Y') AS returned_date_formatted,
            CASE 
                WHEN {$this->table}.returned_q IS NOT NULL
                THEN ({$this->table}.quantity_out - {$this->table}.returned_q) 
                ELSE 0 
            END AS consumed
        ";

        if ($concat) {
            $columns = "
                GROUP_CONCAT(inventory_id) AS inventory_id,
                GROUP_CONCAT(quantity_out) AS quantity_out,
                GROUP_CONCAT(
                    CASE 
                    WHEN returned_q IS NOT NULL
                    THEN returned_q 
                    ELSE 0 
                END
                ) AS returned_q,
                GROUP_CONCAT(
                    CASE 
                    WHEN returned_q IS NOT NULL
                    THEN (quantity_out - returned_q) 
                    ELSE 0 
                END
                ) AS consumed
            ";
        }

        return $columns;
    }
    
    // Selected inventory columns
    public function inventoryColumns($withView = false)
    {
        $inventoryModel = new InventoryModel();  
        $columns = "
            {$inventoryModel->table}.category,
            {$inventoryModel->table}.sub_category,
            {$inventoryModel->table}.item_brand,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->table}.stocks
        ";

        if ($withView) {
            $columns .= ",
                {$inventoryModel->view}.category_name,
                {$inventoryModel->view}.subcategory_name,
                {$inventoryModel->view}.brand,
                {$inventoryModel->view}.created_by_name
            ";
        }

        return $columns;
    }

    // Join with inventory table and inventory_view
    public function joinInventory($builder, $withView = false)
    {
        $inventoryModel = new InventoryModel();        
        // Join with inventory table
        $builder->join($inventoryModel->table, "{$this->table}.inventory_id = {$inventoryModel->table}.id", 'left');
        // Then join inventory with inventory_View
        if ($withView) $inventoryModel->joinView($builder);
    }

    // Get prf items using prf_id
    public function getPrfItemsByPrfId($prf_id, $columns = '', $concat = false, $joinInventory = false) 
    {
        $columns = $columns ? $columns : $this->columns($concat);
        $builder = $this->select($columns);
        $builder->where('prf_id', $prf_id);

        if ($concat) $builder->groupBy('prf_id');
        if ($joinInventory) $builder->joinInventory($builder);
        return $builder->findAll();
    }

    // Saving the prf items
    public function savePrfItems($data, $prf_id) 
    {
        $inventory_ids  = $data['inventory_id'];
        $quantity_outs  = $data['quantity_out'];
        $returned_qs    = isset($data['returned_q']) ? $data['returned_q'] : null;
        $returned_date  = isset($data['returned_date']) ? $data['returned_date'] : null;

        if (! empty($data) && count($inventory_ids)) {
            $arr = [];
            
            // Delete items first
            $this->deletePrfItems($prf_id);

            for ($i=0; $i < count($inventory_ids); $i++) { 
                $arr[] = [
                    'prf_id'        => (int)$prf_id,
                    'inventory_id'  => $inventory_ids[$i],
                    'quantity_out'  => $quantity_outs[$i],
                    'returned_q'    => $returned_qs ? $returned_qs[$i] : $returned_qs,
                    'returned_date' => $returned_date ? $$returned_date[$i] : $returned_date,
                ];
            }

            if (! empty($arr)) $this->db->table($this->table)->insertBatch($arr);
        }
    }

    // Saving the prf items
    public function updatePrfItems($data, $prf_id) 
    {
        $inventory_id   = $data['inventory_id'];
        $returned_q     = $data['returned_q'];
        $returned_date  = $data['returned_date'];
        $stocks         = $data['stocks'];
        $quantity_out   = $data['quantity_out'];

        if (! empty($data) && count($inventory_id)) {
            $arr        = [];
            $action     = 'ITEM_IN';
            $logs_data  = [];
            for ($i=0; $i < count($inventory_id); $i++) { 
                $arr[] = [
                    'prf_id'        => (int)$prf_id,
                    'inventory_id'  => $inventory_id[$i],
                    'returned_q'    => $returned_q[$i],
                    'returned_date' => $returned_date[$i],
                    'quantity_out'  => $quantity_out[$i],
                ];

                if (floatval($returned_q[$i]) > 0) {
                    $logs_data[] = [
                        'inventory_id'  => $inventory_id[$i],
                        'stocks'        => $returned_q[$i],
                        'parent_stocks' => $stocks[$i],
                        'action'        => $action,
                        'status'        => 'RETURN',
                        'status_date'   => current_date(),
                        'created_by'    => session('username'),
                    ];

                    $this->traitUpdateInventoryStock(
                        $inventory_id[$i],
                        $returned_q[$i],
                        $action
                    );
                }
            }

            if (! empty($arr)) {
                $constraint = ['prf_id', 'inventory_id', 'quantity_out'];
                $this->db->table($this->table)->updateBatch($arr, $constraint);
            }
            // Add inventory logs
            $this->saveInventoryLogs($logs_data);
        }
    }

    // Delete prf items
    public function deletePrfItems($prf_id) 
    {
        $this->where('prf_id', $prf_id)->delete();
    }
}
