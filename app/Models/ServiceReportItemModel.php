<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceReportItemModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'service_report_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'service_report_id',
        'item_no',
        'item_description',
        'item_qty',
        'item_unit_price',
        'item_total_price',
        'order',
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

    /**
     * Fetch service report items
     */
    public function fetchItems($service_report_id) 
    {
        return $this->where('service_report_id', $service_report_id)->findAll();
    }

    /**
     * Save the service report item
     */
    public function saveItems($request, $service_report_id) 
    {
        $item_no            = $request['item_no'] ?? null;
        $item_description   = $request['item_description'] ?? null;
        $item_qty           = $request['item_qty'] ?? null;
        $item_unit_price    = $request['item_unit_price'] ?? null;
        $item_total_price   = $request['item_total_price'] ?? null;

        if (! empty($request) && count($item_no)) {
            $arr = [];

            if (empty($item_no[0]) && empty($item_description[0])) {
                log_msg('empty item');
                return;
            }
            
            // Delete items first
            $this->deleteItems($service_report_id);

            for ($i=0; $i < count($item_no); $i++) {
                if (! empty($item_no[$i]) && ! empty($item_description[$i])) {
                    $arr[] = [
                        'service_report_id'     => (int) $service_report_id,
                        'item_no'               => $item_no[$i] ?? null,
                        'item_description'      => $item_description[$i] ?? null,
                        'item_qty'              => $item_qty[$i] ?? null,
                        'item_unit_price'       => $item_unit_price[$i] ?? null,
                        'item_total_price'      => $item_total_price[$i] ?? null,
                        'order'                 => $i + 1,
                    ];
                }
            }

            if (! empty($arr)) $this->db->table($this->table)->insertBatch($arr);
        }
    }

    /**
     * Delete the service report items
     */
    public function deleteItems($service_report_id) 
    {
        $this->where('service_report_id', $service_report_id)->delete();
    }
}
