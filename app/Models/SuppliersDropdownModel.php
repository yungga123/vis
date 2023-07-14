<?php

namespace App\Models;

use CodeIgniter\Model;

class SuppliersDropdownModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'suppliers_dropdown';
    protected $view             = 'suppliers_dropdown_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'dropdown',
        'dropdown_type',
        'parent_id',
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'dropdown'      => [
            'label' => 'Suppliers Dropdown',
            'rules' => 'required'
        ],
        'dropdown_type'      => [
            'label' => 'Dropdown Type',
            'rules' => 'required'
        ],
        'created_by'      => [
            'label' => 'Created By',
            'rules' => 'required'
        ]
    ];
    protected $validationMessages   = [

    ];
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

    public function noticeTable()
    {
        $builder    = $this->db->table($this->view);
        $builder->select('*');

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
