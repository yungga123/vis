<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\InventoryTrait;

class OrderFormItemModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'order_form_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_form_id',
        'inventory_id',
        'quantity',
        'discount',
        'total_price',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'order_form_id' => [
            'rules' => 'required|if_exist|numeric',
            'label' => 'order form id'
        ],
        'inventory_id' => [
            'rules' => 'required|if_exist|numeric',
            'label' => 'inventory id'
        ],
        'quantity' => [
            'rules' => 'required|if_exist|numeric',
            'label' => 'quantity'
        ],
        'discount' => [
            'rules' => 'permit_empty|numeric',
            'label' => 'discount'
        ],
        'total_price' => [
            'rules' => 'required|if_exist|numeric',
            'label' => 'total price'
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

    /**
     * Get order form items
     */
    public function getItems($order_form_id, $joinInv = false, $columns = '') 
    {
        $itemModel  = new InventoryModel();

        if (empty($columns)) {
            $columns = "
                {$this->table}.order_form_id,
                {$this->table}.inventory_id,
                {$this->table}.quantity,
                {$this->table}.discount,
                {$this->table}.total_price
            ";
            $columns = $joinInv ? $columns . ",
                {$itemModel->table}.item_model,
                {$itemModel->table}.item_description,
                {$itemModel->table}.item_sdp AS item_price,
                {$itemModel->table}.stocks,
                {$itemModel->view}.category_name,
                {$itemModel->view}.brand,
                {$itemModel->view}.unit,
                {$itemModel->view}.size,
                {$itemModel->view}.supplier_name
            " : $columns;
        }
        
        $builder    = $this->select($columns);

        $builder->where('order_form_id', $order_form_id);

        if ($joinInv) {
            $builder->joinInventory($this->table, $builder, true);
        }

        return $builder->findAll();
    }

    /**
     * Save the order form items
     */
    public function saveItems($request, $order_form_id) 
    {
        $inventory_ids  = $request['inventory_id'];
        $quantities     = $request['quantity'] ?? '';
        $discounts      = $request['discount'] ?? '';
        $total_prices   = $request['total_price'] ?? '';

        if (! empty($request) && count($inventory_ids)) {
            $arr = [];
            
            // Delete items first
            $this->deleteItems($order_form_id);

            for ($i=0; $i < count($inventory_ids); $i++) { 
                $arr[] = [
                    'order_form_id' => (int)$order_form_id,
                    'inventory_id'  => $inventory_ids[$i],
                    'quantity'      => $quantities[$i],
                    'discount'      => $discounts[$i],
                    'total_price'   => $total_prices[$i],
                ];
            }

            if (! empty($arr)) $this->db->table($this->table)->insertBatch($arr);
        }
    }

    /**
     * Update the order form items
     */
    public function updateItems($request, $order_form_id) 
    {
        $inventory_id   = $request['inventory_id'];
        $quantity       = $request['quantity'] ?? '';
        $discount       = $request['discount'] ?? '';
        $total_price    = $request['total_price'] ?? '';

        if (! empty($request) && count($inventory_id)) {
            $arr        = [];
            for ($i=0; $i < count($inventory_id); $i++) { 
                $arr[] = [
                    'order_form_id' => (int)$order_form_id,
                    'inventory_id'  => $inventory_id,
                    'quantity'      => $quantity,
                    'discount'      => $discount,
                    'total_price'   => $total_price,
                ];
            }

            if (! empty($arr)) {
                $constraint = ['order_form_id', 'inventory_id'];
                return $this->db->table($this->table)->updateBatch($arr, $constraint);
            }
        }

        return false;
    }

    /**
     * Delete the order form items
     */
    public function deleteItems($order_form_id) 
    {
        $this->where('order_form_id', $order_form_id)->delete();
    }
}
