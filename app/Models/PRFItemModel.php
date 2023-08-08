<?php

namespace App\Models;

use CodeIgniter\Model;

class PRFItemModel extends Model
{
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

    // Saving the prf items
    public function savePrfItems($data, $prf_id) 
    {
        $inventory_ids  = $data['inventory_id'];
        $quantity_outs  = $data['inventory_id'];
        $returned_qs    = isset($data['returned_q']) ? $data['returned_q'] : null;
        $returned_date  = isset($data['returned_date']) ? $data['returned_date'] : null;

        if (! empty($data) && count($inventory_ids)) {
            $arr = [];
            
            // Delete items first
            $this->deletePrfItems($prf_id);

            for ($i=0; $i < count($inventory_ids); $i++) { 
                $arr[] = [
                    "prf_id"        => (int)$prf_id,
                    "inventory_id"  => $inventory_ids[$i],
                    "quantity_out"  => $quantity_outs[$i],
                    "returned_q"    => $returned_qs ? $returned_qs[$i] : $returned_qs,
                    "returned_date" => $returned_date ? $$returned_date[$i] : $returned_date,
                ];
            }

            if (! empty($arr)) $this->db->table($this->table)->insertBatch($arr);
        }
    }

    // Delete prf items
    public function deletePrfItems($prf_id) 
    {
        $this->where('prf_id', $prf_id)->delete();
    }
}
