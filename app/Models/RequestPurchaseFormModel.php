<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestPurchaseFormModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'request_purchase_forms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'remarks',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'inventory_id' => [
            'rules' => 'required|if_exist',
            'label' => 'item'
        ],
        'quantity_in' => [
            'rules' => 'required|if_exist',
            'label' => 'quantity in'
        ],
        'received_date' => [
            'rules' => 'permit_empty|if_exist',
            'label' => 'received date'
        ],
        'delivery_date' => [
            'rules' => 'permit_empty|if_exist',
            'label' => 'delivery date'
        ],
        'remarks' => [
            'rules' => 'required|if_exist'
        ],
    ];
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
}
