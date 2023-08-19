<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierBrandsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'supplier_brands';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "brand_name",
        "product",
        "warranty",
        "sales_person",
        "sales_contact_number",
        "technical_support",
        "technical_contact_number",
        "remarks",
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'brand_name'      => [
            'label' => 'Brand',
            'rules' => 'required'
        ],
        'product'      => [
            'label' => 'Product',
            'rules' => 'required'
        ],
        'warranty'      => [
            'label' => 'Warranty',
            'rules' => 'required'
        ],
        'sales_person'      => [
            'label' => 'Sales Person',
            'rules' => 'required'
        ],
        'sales_contact_number' => [
            'label' => 'Sales Contact Number',
            'rules' => 'required'
        ],
        'technical_support'      => [
            'label' => 'Technical Support',
            'rules' => 'required'
        ],
        'technical_contact_number'      => [
            'label' => 'Technical Contact Number',
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

    public function noticeTable($supplier_id)
    {
        $builder    = $this->db->table($this->table);
        $builder->select('
            brand_name,
            product,
            warranty,
            sales_person,
            sales_contact_number,
            technical_support,
            technical_contact_number,
            remarks
        ');

        $builder->where('deleted_at', null);
        $builder->where('supplier_id',$supplier_id);
        return $builder;
    }

    public function buttons()
    {
        $closureFun = function($row) {
            return <<<EOF
                

                <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                    Action 
                    <span class="sr-only">Toggle Dropdown</span>
                </button>

                <div class="dropdown-menu" role="menu" style="">
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["id"]})"  data-toggle="modal" data-target="#modal_add_supplier" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["id"]})" title="Delete"><i class="fas fa-trash"></i></button>
                </div>
                
            EOF;
        };
        return $closureFun;
    }
}
