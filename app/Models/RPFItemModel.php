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
        'quantity_in',
        'received_q',
        'received_date',
        'delivery_date',
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
            {$this->table}.delivery_date,
            DATE_FORMAT({$this->table}.received_date, '%b %e, %Y') AS received_date_formatted,
            DATE_FORMAT({$this->table}.delivery_date, '%b %e, %Y') AS delivery_date_formatted
        ";

        return $columns;
    }

    // Get rpf items using rpf_id
    public function getRpfItemsByPrfId($rpf_id, $columns = '', $joinInventory = false) 
    {
        $columns = $columns ? $columns : $this->columns();
        $builder = $this->select($columns);
        $builder->where('rpf_id', $rpf_id);

        // From InventoryTrait
        if ($joinInventory) $this->joinInventory($this->table, $builder);
        return $builder->findAll();
    }

    // Saving the rpf items
    public function saveRpfItems($data, $rpf_id) 
    {
        $inventory_id   = $data['inventory_id'];
        $quantity_in    = $data['quantity_in'];
        $delivery_date  = $data['delivery_date'] ?? null;

        if (! empty($data) && count($inventory_id)) {
            $arr = [];
            
            // Delete items first
            $this->deleteRpfItems($rpf_id);

            for ($i=0; $i < count($inventory_id); $i++) { 
                $arr[] = [
                    'rpf_id'        => (int)$rpf_id,
                    'inventory_id'  => $inventory_id[$i],
                    'quantity_in'   => $quantity_in[$i],
                    'delivery_date' => $delivery_date ? $delivery_date[$i] : $delivery_date,
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
        $received_date  = $data['received_date'];
        $stocks         = $data['stocks'];

        if (! empty($data) && count($inventory_id)) {
            $arr        = [];
            $action     = 'ITEM_IN';
            $logs_data  = [];
            for ($i=0; $i < count($inventory_id); $i++) { 
                $arr[] = [
                    'rpf_id'        => (int)$rpf_id,
                    'inventory_id'  => $inventory_id[$i],
                    'quantity_in'    => $quantity_in[$i],
                    'received_date' => $received_date[$i],
                ];

                if (floatval($quantity_in[$i]) > 0) {
                    $logs_data[] = [
                        'inventory_id'  => $inventory_id[$i],
                        'stocks'        => $quantity_in[$i],
                        'parent_stocks' => $stocks[$i],
                        'action'        => $action,
                        'status'        => 'PURCHASE',
                        'status_date'   => current_date(),
                        'created_by'    => session('username'),
                    ];

                    $this->traitUpdateInventoryStock(
                        $inventory_id[$i],
                        $quantity_in[$i],
                        $action
                    );
                }
            }

            if (! empty($arr)) {
                $constraint = ['rpf_id', 'inventory_id', 'quantity_in'];
                $this->db->table($this->table)->updateBatch($arr, $constraint);
            }
            // Add inventory logs
            $this->saveInventoryLogs($logs_data);
        }
    }

    // Delete rpf items
    public function deleteRpfItems($rpf_id) 
    {
        $this->where('rpf_id', $rpf_id)->delete();
    }
}
