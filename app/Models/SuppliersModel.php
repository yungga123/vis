<?php

namespace App\Models;

use CodeIgniter\Model;

class SuppliersModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'supplier_name',
        'supplier_type',
        'contact_person',
        'contact_number',
        'viber',
        'payment_terms',
        'payment_mode',
        'product',
        'remarks',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'supplier_name'      => [
            'label' => 'Supplier Name',
            'rules' => 'required'
        ],
        'supplier_type'      => [
            'label' => 'Type of Supplier',
            'rules' => 'required'
        ],
        'contact_person'      => [
            'label' => 'Contact Person',
            'rules' => 'required'
        ],
        'contact_number'      => [
            'label' => 'Contact Number',
            'rules' => 'required'
        ],
        'payment_terms'      => [
            'label' => 'Payment Terms',
            'rules' => 'required'
        ],
        'payment_mode'      => [
            'label' => 'Payment Mode',
            'rules' => 'required'
        ],
        'product'      => [
            'label' => 'Product',
            'rules' => 'required'
        ],
        'remarks'      => [
            'label' => 'Remarks',
            'rules' => 'required'
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
