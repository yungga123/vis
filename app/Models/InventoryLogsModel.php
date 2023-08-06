<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\InventoryTrait;

class InventoryLogsModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait;

    protected $DBGroup          = 'default';
    protected $inventoryTable   = 'inventory';
    protected $inventoryView    = 'inventory_view';
    protected $invDropdownTable = 'inventory_dropdowns';
    protected $table            = 'inventory_logs';
    protected $primaryKey       = 'inventory_logs_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [        
        'inventory_id',
        'item_size',
        'item_sdp',
        'item_srp',
        'project_price',
        'total',
        'stocks',
        'parent_stocks',
        'stock_unit',
        'date_of_purchase',
        'supplier',
        'location',
        'action',
        'status',
        'status_date',
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
            'rules' => 'required',
            'label' => 'inventory number'
        ],
        'stocks' => [
            'rules' => 'required|numeric',
            'label' => 'quantity'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = ['updateInventoryStock'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Set the value for created_by before inserting
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Set the value for created_by before inserting
    protected function updateInventoryStock(array $data)
    {
        if ($data['result']) {
            $this->traitUpdateInventoryStock(
                $data['data']['inventory_id'], 
                doubleval($data['data']['quantity'] ?? $data['data']['stocks']),
                $data['data']['action']
            );
        }

        return $data;
    }

    // Join to main table inventory
    protected function joinInventory($builder)
    {
        $builder->join($this->inventoryTable, "{$this->inventoryTable}.id = {$this->table}.inventory_id", 'left');
        $builder->join($this->inventoryView, "{$this->inventoryView}.inventory_id = {$this->table}.inventory_id", 'left');
    }

    // Set/format columns to include in select
    public function columns()
    {
        $columns        = "
            {$this->table}.inventory_logs_id,
            {$this->table}.inventory_id,
            {$this->table}.status,
            (UPPER({$this->table}.status)) AS cap_status,
            {$this->table}.action,
            {$this->table}.stocks,
            {$this->table}.parent_stocks,
            {$this->inventoryTable}.item_model,
            {$this->inventoryTable}.item_description,
            {$this->inventoryTable}.stocks AS current_stocks,
            {$this->inventoryView}.category_name,
            {$this->inventoryView}.subcategory_name,
            {$this->inventoryView}.brand,
            {$this->inventoryView}.size,
            {$this->inventoryView}.unit,
            {$this->inventoryView}.created_by_name,
            DATE_FORMAT({$this->table}.status_date, '%b %e, %Y') AS status_date_formatted,
            DATE_FORMAT({$this->table}.created_at, '%b %e, %Y at %h:%i %p') AS created_at_formatted,
            (SELECT employee_name FROM accounts_view AS av WHERE {$this->table}.created_by = av.username) AS encoder
        ";

        return $columns;
    }
    
    // For DataTables
    public function noticeTable($request) 
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->columns());
        $this->joinInventory($builder);        

        if (isset($request['params'])) {
            $params = $request['params'];

            if (! empty($params['action']) && $params['action'] !== 'all') 
                $builder->where("{$this->table}.action", $params['action']);

            if (! empty($params['category'])) {
                $category_ids   = array_filter($params['category'], function($val) {
                    if (!str_contains($val, 'other__')) return $val;
                });
    
                if (! empty($category_ids)) $builder->whereIn("{$this->inventoryTable}.category", $category_ids);
            }

            if (! empty($params['sub_dropdown'])) {
                $ids    = implode(',', $params['sub_dropdown']);
                $in     = "IN({$ids})";
                $where  = "({$this->inventoryTable}.sub_category {$in} OR {$this->inventoryTable}.item_brand {$in} OR {$this->table}.item_size {$in} OR {$this->table}.stock_unit {$in})";

                $builder->where($where);
            }
        }

        $builder->where('inventory_logs.deleted_at', null);
        $builder->orderBy('inventory_logs.'. $this->primaryKey, 'DESC');
        return $builder;
    }

    public function actionLogs()
    {
        $closureFun = function($row) {
            $text   = get_actions($row['action'], true);
            $class  = $row['action'] === 'ITEM_IN' ? 'bg-primary' : 'bg-success';
            $class  .= ' rounded text-sm text-white pl-2 pr-2 pt-1 pb-1';

            return <<<EOF
                <span class="{$class}">{$text}</span>
            EOF;

        };
        
        return $closureFun;
    }
}
