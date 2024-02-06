<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\InventoryTrait;
use App\Traits\HRTrait;

class InventoryLogsModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait, HRTrait;

    protected $DBGroup          = 'default';
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
    public function joinInventory(object $bui)
    {
        $inventoryModel = new InventoryModel();

        $bui->join($inventoryModel->table, "{$inventoryModel->table}.id = {$this->table}.inventory_id", 'left');
        $bui->join($inventoryModel->view, "{$inventoryModel->view}.inventory_id = {$this->table}.inventory_id", 'left');
    }

    // Set/format columns to include in select
    public function columns()
    {
        $inventoryModel = new InventoryModel();
        $columns        = "
            {$this->table}.inventory_logs_id,
            {$this->table}.inventory_id,
            {$this->table}.status,
            (UPPER({$this->table}.status)) AS cap_status,
            {$this->table}.action,
            {$this->table}.stocks,
            {$this->table}.parent_stocks,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->table}.stocks AS current_stocks,
            {$inventoryModel->view}.supplier_name,
            {$inventoryModel->view}.category_name,
            {$inventoryModel->view}.subcategory_name,
            {$inventoryModel->view}.brand,
            {$inventoryModel->view}.size,
            {$inventoryModel->view}.unit,
            {$inventoryModel->view}.created_by_name,
            ".dt_sql_date_format("{$this->table}.status_date")." AS status_date_formatted,
            ".dt_sql_date_format("{$this->table}.created_at")." AS created_at_formatted,
            cb.employee_name AS encoder
        ";

        return $columns;
    }
    
    // For DataTables
    public function noticeTable($request) 
    {
        $inventoryModel = new InventoryModel();
        $builder        = $this->db->table($this->table);

        $builder->select($this->columns());

        $this->joinInventory($builder);
        $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');

        if (isset($request['params'])) {
            $params = $request['params'];

            if (! empty($params['action']) && $params['action'] !== 'all') 
                $builder->where("{$this->table}.action", $params['action']);

            if (! empty($params['category'])) {
                $category_ids   = array_filter($params['category'], function($val) {
                    if (!str_contains($val, 'other__')) return $val;
                });
    
                if (! empty($category_ids)) $builder->whereIn("{$inventoryModel->table}.category", $category_ids);
            }

            if (! empty($params['sub_dropdown'])) {
                $ids    = implode(',', $params['sub_dropdown']);
                $in     = "IN({$ids})";
                $where  = "({$inventoryModel->table}.sub_category {$in} OR {$inventoryModel->table}.item_brand {$in} OR {$inventoryModel->table}.item_size {$in} OR {$inventoryModel->table}.stock_unit {$in})";

                $builder->where($where);
            }
        }

        $builder->where("{$this->table}.deleted_at", null);
        $builder->orderBy("{$this->table}.". $this->primaryKey, 'DESC');

        return $builder;
    }

    public function actionLogs()
    {
        $closureFun = function($row) {
            $text   = get_actions($row['action'], true);
            $color  = $row['action'] === 'ITEM_IN' ? 'primary' : 'success';

            return text_badge($color, $text);
        };
        
        return $closureFun;
    }
}
