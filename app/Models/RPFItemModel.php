<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\InventoryTrait;

class RPFItemModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'rpf_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'inventory_id',
        'supplier_id',
        'quantity_in',
        'received_q',
        'received_date',
        'purpose',
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
    public function columns()
    {
        $columns = "
            {$this->table}.inventory_id,
            {$this->table}.quantity_in,
            {$this->table}.received_q,
            {$this->table}.received_date,
            DATE_FORMAT({$this->table}.received_date, '%b %e, %Y') AS received_date_formatted
        ";

        return $columns;
    }
    
    // Selected inventory columns
    public function inventoryColumns($withView = false)
    {
        $inventoryModel = new InventoryModel();  
        $columns = "
            {$inventoryModel->table}.supplier_id,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->table}.stocks,
            {$inventoryModel->table}.item_sdp
        ";

        if ($withView) {
            $columns .= ",
                TRIM({$inventoryModel->view}.category_name) AS category_name,
                TRIM({$inventoryModel->view}.subcategory_name) AS subcategory_name,
                TRIM({$inventoryModel->view}.brand) AS brand,
                TRIM({$inventoryModel->view}.unit) AS unit,
                {$inventoryModel->view}.supplier_name,
                {$inventoryModel->view}.created_by_name
            ";
        } else {
            $columns .= ",
                {$inventoryModel->table}.category,
                {$inventoryModel->table}.sub_category,
                {$inventoryModel->table}.item_brand
            ";
        }

        return $columns;
    }

    // Join with inventory table
    public function joinInventoryOnly($type = 'left') 
    {
        $inventoryModel = new InventoryModel();  
        $this->join($inventoryModel->table, "{$inventoryModel->table}.id = {$this->table}.inventory_id", $type);
        return $this;
    }

    // Get rpf items using rpf_id
    public function getRpfItemsByRpfId($rpf_id, $joinInventory = false, $withView = false, $columns = '') 
    {
        $columns = $columns ? $columns : $this->columns();
        $columns = $joinInventory ? $columns .',' . $this->inventoryColumns($withView) : $columns;
        $builder = $this->select($columns);

        is_array($rpf_id) 
            ? $builder->whereIn('rpf_id', $rpf_id)
            : $builder->where('rpf_id', $rpf_id);

        // From InventoryTrait
        if ($joinInventory) $this->joinInventory($this->table, $builder, $withView);
        return $builder->findAll();
    }

    // Saving the rpf items
    public function saveRpfItems($data, $rpf_id) 
    {
        $inventory_id   = $data['inventory_id'];
        $supplier_id    = $data['supplier_id'] ?? 0;
        $quantity_in    = $data['quantity_in'];
        $purpose        = $data['purpose'];

        if (! empty($data) && count($inventory_id)) {
            $arr = [];
            
            // Delete items first
            $this->deleteRpfItems($rpf_id);

            for ($i=0; $i < count($inventory_id); $i++) { 
                $arr[] = [
                    'rpf_id'        => (int)$rpf_id,
                    'inventory_id'  => $inventory_id[$i],
                    'supplier_id'   => $supplier_id[$i] ?? 0,
                    'quantity_in'   => $quantity_in[$i],
                    'purpose'       => $purpose[$i] ?? null,
                ];
            }

            if (! empty($arr)) $this->db->table($this->table)->insertBatch($arr);
        }
    }

    // Saving the rpf items
    public function updateRpfItems($data, $rpf_id) 
    {
        $inventory_id   = $data['inventory_id'];
        $quantity_in    = $data['quantity_in'];
        $received_q     = $data['received_q'];
        $received_date  = $data['received_date'];

        if (! empty($data) && count($inventory_id)) {
            $arr        = [];
            for ($i=0; $i < count($inventory_id); $i++) { 
                $arr[] = [
                    'rpf_id'        => (int)$rpf_id,
                    'inventory_id'  => $inventory_id[$i],
                    'quantity_in'   => $quantity_in[$i],
                    'received_q'    => $received_q[$i],
                    'received_date' => $received_date[$i],
                ];
            }

            if (! empty($arr)) {
                $constraint = ['rpf_id', 'inventory_id', 'quantity_in'];
                $this->db->table($this->table)->updateBatch($arr, $constraint);
            }
        }
    }

    // Delete rpf items
    public function deleteRpfItems($rpf_id) 
    {
        $this->where('rpf_id', $rpf_id)->delete();
    }
}
