<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceReportUnitModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'service_report_units';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'service_report_id',
        'item',
        'status',
        'defective_description',
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
     * Fetch service report units
     */
    public function fetchUnits($service_report_id) 
    {
        return $this->where('service_report_id', $service_report_id)->findAll();
    }

    /**
     * Save the service report units
     */
    public function saveUnits($request, $service_report_id) 
    {
        $item                   = $request['item'] ?? null;
        $status                 = $request['item_status'] ?? null;
        $defective_description  = $request['defective_description'] ?? null;

        if (! empty($request) && count($item)) {
            $arr = [];
            
            // Delete units first
            $this->deleteUnits($service_report_id);

            for ($i=0; $i < count($item); $i++) {
                if (! empty($item[$i])) {
                    $arr[] = [
                        'service_report_id'     => (int) $service_report_id,
                        'item'                  => $item[$i] ?? null,
                        'status'                => $status[$i] ?? null,
                        'defective_description' => $defective_description[$i] ?? null,
                        'order'                 => $i + 1,
                    ];
                }
            }

            if (! empty($arr)) $this->db->table($this->table)->insertBatch($arr);
        }
    }

    /**
     * Delete the service report units
     */
    public function deleteUnits($service_report_id) 
    {
        $this->where('service_report_id', $service_report_id)->delete();
    }
}
