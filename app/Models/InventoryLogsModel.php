<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryLogsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $inventoryTable   = 'inventory';
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
        'item_size' => [
            'rules' => 'permit_empty',
            'label' => 'item size'
        ],
        'item_sdp' => [
            'rules' => 'required|numeric',
            'label' => 'dealer price'
        ],
        'item_srp' => [
            'rules' => 'permit_empty|numeric',
            'label' => 'retail price'
        ],
        'project_price' => [
            'rules' => 'permit_empty|numeric',
            'label' => 'project price'
        ],
        'stocks' => [
            'rules' => 'required|numeric',
            'label' => 'quantity'
        ],
        'stock_unit' => [
            'rules' => 'permit_empty',
            'label' => 'item unit'
        ],
        'date_of_purchase' => [
            'rules' => 'required',
            'label' => 'date of purchase'
        ],
        'location' => [
            'rules' => 'permit_empty|min_length[3]|max_length[200]',
            'label' => 'lacation'
        ],
        'supplier' => [
            'rules' => 'permit_empty|string|min_length[3]|max_length[200]',
            'label' => 'supplier'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeInsert'];
    protected $afterInsert    = ['afterInsert'];
    protected $beforeUpdate   = ['beforeInsert'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function joinInventory($builder, $joinType = 'left')
    {
        return $builder->join($this->inventoryTable . ' AS inv', 'inventory_id = inv.id', $joinType);
    }

    // Set the value for created_by before inserting
    protected function beforeInsert(array $data)
    {
        if (isset($data['data']['item_size']) && $data['data']['item_size'] === 'na') {
            $data['data']['item_size'] = '';
        }
        if (isset($data['data']['stock_unit']) && $data['data']['stock_unit'] === 'na') {
            $data['data']['stock_unit'] = '';
        }
        $data['data']['created_by'] = session('employee_id');

        return $data;
    }

    // Set the value for created_by before inserting
    protected function afterInsert(array $data)
    {
        if ($data['result']) {
            $this->_updateInventoryStock(
                $data['data']['inventory_id'], 
                doubleval($data['data']['quantity'] ?? $data['data']['stocks']),
                $data['data']['action']
            );
        }

        return $data;
    }
    
    // For DataTables
    public function noticeTable($request) 
    {
        $builder = $this->db->table($this->table);
        $builder->select("
            inventory_logs_id,
            inventory_id,
            (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE inv.category = db.dropdown_id) AS category,
            (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE inv.sub_category = db.dropdown_id) AS sub_category,
            IF(inv.item_brand IS NULL or TRIM(inv.item_brand) = '', 'N/A', (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE inv.item_brand = db.dropdown_id)) AS item_brand,
            inv.item_model,
            inv.item_description,
            IF(inv.item_size IS NULL or TRIM(inv.item_size) = '', 'N/A', (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE inv.item_size = db.dropdown_id)) AS item_size,
            {$this->table}.item_sdp,
            {$this->table}.stocks,
            IF(inv.stock_unit IS NULL or TRIM(inv.stock_unit) = '', 'N/A', (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE inv.stock_unit = db.dropdown_id)) AS stock_unit,
            action,
            (SELECT CONCAT(firstname, ' ', lastname) FROM employees AS ev WHERE created_by = ev.employee_id) AS encoder
        ");

        $this->joinInventory($builder);        

        if (isset($request['params'])) {
            $params = $request['params'];

            if (! empty($params['action']) && $params['action'] !== 'all') $builder->where('action', $params['action']);
            if (! empty($params['category'])) $builder->whereIn('category', $params['category']);
            if (! empty($params['sub_dropdown'])) {
                $ids    = implode(',', $params['sub_dropdown']);
                $in     = "IN({$ids})";
                $where  = "(sub_category {$in} OR item_size {$in} OR stock_unit {$in})";

                $builder->where($where);
            }
        }

        $builder->where('inventory_logs.deleted_at', null);
        return $builder;
    }

    public function actionLogs()
    {
        $closureFun = function($row) {
            $text = get_actions($row['action'], true);
            $class = $row['action'] === 'ITEM_IN' ? 'bg-primary' : 'bg-secondary';

            return <<<EOF
                <span class="text-white p-1 {$class} rounded flat">{$text}</span>
            EOF;

        };
        
        return $closureFun;
    }

    // Updating inventory stocks
    private function _updateInventoryStock($id, $stock, $action)
    {
        $sign = $action === 'ITEM_OUT' ? '-' : '+';
        $builder = $this->db->table($this->inventoryTable);
        $builder->set('stocks', "stocks $sign ". $stock, false);
        $builder->where('id', $id)->update();
    }
}
