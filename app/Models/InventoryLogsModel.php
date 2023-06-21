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

    // Join to main table inventory
    protected function joinInventory($builder, $alias = null, $joinType = 'left')
    {
        $table = $alias ? $this->inventoryTable . ' AS '. $alias: $this->inventoryTable;
        $id = $alias ? "{$alias}.id" : 'id';
        return $builder->join($table, "inventory_id = $id", $joinType);
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
        $invAlias = 'inv';
        $builder = $this->db->table($this->table);
        $builder->select("
            inventory_logs_id,
            inventory_id,
            (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE $invAlias.category = db.dropdown_id) AS category,
            (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE $invAlias.sub_category = db.dropdown_id) AS sub_category,
            IF($invAlias.item_brand IS NULL or TRIM($invAlias.item_brand) = '', 'N/A', (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE $invAlias.item_brand = db.dropdown_id)) AS item_brand,
            $invAlias.item_model,
            $invAlias.item_description,
            IF($invAlias.item_size IS NULL or TRIM($invAlias.item_size) = '', 'N/A', (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE $invAlias.item_size = db.dropdown_id)) AS item_size,
            {$this->table}.item_sdp,
            {$this->table}.stocks,
            IF($invAlias.stock_unit IS NULL or TRIM($invAlias.stock_unit) = '', 'N/A', (SELECT dropdown FROM {$this->invDropdownTable} AS db WHERE $invAlias.stock_unit = db.dropdown_id)) AS stock_unit,
            status,
            DATE_FORMAT(status_date, '%b %e, %Y') AS status_date,
            action,
            (SELECT CONCAT(firstname, ' ', lastname) FROM employees AS ev WHERE created_by = ev.employee_id) AS encoder
        ");

        $this->joinInventory($builder, $invAlias);        

        if (isset($request['params'])) {
            $params = $request['params'];

            if (! empty($params['action']) && $params['action'] !== 'all') 
                $builder->where("{$this->table}.action", $params['action']);

            if (! empty($params['category'])) {
                $category_ids   = array_filter($params['category'], function($val) {
                    if (!str_contains($val, 'other__')) return $val;
                });
    
                if (! empty($category_ids)) $builder->whereIn("$invAlias.category", $category_ids);
            }

            if (! empty($params['sub_dropdown'])) {
                $ids    = implode(',', $params['sub_dropdown']);
                $in     = "IN({$ids})";
                $where  = "($invAlias.sub_category {$in} OR $invAlias.item_brand {$in} OR {$this->table}.item_size {$in} OR {$this->table}.stock_unit {$in})";

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
            $text = get_actions($row['action'], true);
            $class = $row['action'] === 'ITEM_IN' ? 'badge-primary' : 'badge-secondary';

            return <<<EOF
                <span class="badge {$class}" style="font-size: 90%; font-weight: 500;">{$text}</span>
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
