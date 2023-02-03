<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inventory';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'item_name',
        'item_brand',
        'item_type',
        'item_sdp',
        'item_srp',
        'project_price',
        'stocks',
        'stock_unit',
        'date_of_purchase',
        'supplier',
        'location',
        'encoder',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'item_name' => [
            'rules' => 'required|string|min_length[3]',
            'label' => 'item name'
        ],
        'item_brand' => [
            'rules' => 'required|string|min_length[3]',
            'label' => 'brand'
        ],
        'item_type' => [
            'rules' => 'required|string|min_length[3]',
            'label' => 'item type'
        ],
        'item_sdp' => [
            'rules' => 'required|numeric',
            'label' => 'dealer price'
        ],
        'item_srp' => [
            'rules' => 'required|numeric',
            'label' => 'retail price'
        ],
        'project_price' => [
            'rules' => 'required|numeric',
            'label' => 'project price'
        ],
        'stocks' => [
            'rules' => 'required|numeric',
            'label' => 'quantity'
        ],
        'stock_unit' => [
            'rules' => 'required',
            'label' => 'unit'
        ],
        'date_of_purchase' => [
            'rules' => 'required',
            'label' => 'date of purchase'
        ],
        'location' => [
            'rules' => 'required|min_length[3]',
            'label' => 'lacation'
        ],
        'supplier' => [
            'rules' => 'required|string|min_length[3]',
            'label' => 'supplier'
        ],
        'encoder' => [
            'rules' => 'required',
            'label' => 'encoder'
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
    
     // For DataTables
     public function noticeTable() {
        $builder = $this->db->table($this->table)
            ->select("
                id,
                item_name,
                item_brand,
                item_type,
                item_sdp,
                item_srp,
                project_price,
                stocks,
                stock_unit,
                DATE_FORMAT(date_of_purchase, '%b %e, %Y') AS date_of_purchase,
                supplier,
                location,
                encoder,
                DATE_FORMAT(created_at, '%b %e, %Y | %l:%i %p') AS created_at
            ")
            ->where('deleted_at', null);
            
        return $builder;
    }

    public function button(){
        $closureFun = function($row){
            return <<<EOF
                <button class="btn btn-sm btn-warning" onclick="edit({$row["id"]})"  data-toggle="modal" data-target="#modal_inventory" title="Edit"><i class="fas fa-edit"></i> </button> 
                <button class="btn btn-sm btn-danger" onclick="remove({$row["id"]})" title="Delete"><i class="fas fa-trash"></i></button> 
            EOF;
        };
        return $closureFun;
    }
}
