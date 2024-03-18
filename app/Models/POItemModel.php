<?php

namespace App\Models;

use CodeIgniter\Model;

class POItemModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'purchase_order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_id',
        'inventory_id',
        'is_generated',
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

    // Delete PO items
    public function deletePOItems($po_id, $rpf_id) 
    {
        if (! is_array($po_id)) $po_id = [$po_id];
        if (! is_array($rpf_id)) $rpf_id = [$rpf_id];

        return $this->whereIn('po_id', $po_id)
            ->whereIn('rpf_id', $rpf_id)->delete();
    }

    // Join inventory
    public function joinInventory($builder, $model = null, $view = false, $type = 'left') 
    {
        $model ?? $model = new InventoryModel();
        $joinTo = $model->table;
        $id     = 'id';
        if ($view) {
            $joinTo = $model->view;
            $id     = 'inventory_id';
        }
        $builder->join($joinTo, "{$this->table}.inventory_id = {$joinTo}.{$id}", $type);
    }
}
