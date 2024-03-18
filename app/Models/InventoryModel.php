<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inventory';
    protected $view             = 'inventory_view';
    protected $tableDropdowns   = 'inventory_dropdowns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category',
        'sub_category',
        'item_brand',
        'item_model',
        'item_description',
        'item_size',
        'item_sdp',
        'item_srp',
        'project_price',
        'total',
        'stocks',
        'stock_unit',
        'date_of_purchase',
        'supplier_id',
        'location',
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
        'category' => [
            'rules' => 'required',
            'label' => 'category'
        ],
        'sub_category' => [
            'rules' => 'required',
            'label' => 'sub-category'
        ],
        'item_brand' => [
            'rules' => 'required',
            'label' => 'item brand'
        ],
        'item_model' => [
            'rules' => 'required|string|min_length[3]|max_length[150]',
            'label' => 'item model'
        ],
        'item_description' => [
            'rules' => 'required|string|min_length[3]',
            'label' => 'item description'
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
            'rules' => 'permit_empty|numeric',
            'label' => 'quantity'
        ],
        'stock_unit' => [
            'rules' => 'permit_empty',
            'label' => 'item unit'
        ],
        'date_of_purchase' => [
            'rules' => 'permit_empty',
            'label' => 'date of purchase'
        ],
        'location' => [
            'rules' => 'permit_empty|min_length[3]|max_length[200]',
            'label' => 'lacation'
        ],
        'supplier_id' => [
            'rules' => 'required',
            'label' => 'supplier'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setItemFieldsValue'];
    protected $afterInsert    = ['addLogsIfHasQuantity'];
    protected $beforeUpdate   = ['setItemFieldsValue'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Set the value for created_by before inserting
    protected function setItemFieldsValue(array $data)
    {
        if (isset($data['data']['item_size']) && $data['data']['item_size'] === 'na') {
            $data['data']['item_size'] = '';
        }
        if (isset($data['data']['stock_unit']) && $data['data']['stock_unit'] === 'na') {
            $data['data']['stock_unit'] = '';
        }
        $data['data']['created_by'] = session('username');

        return $data;
    }

    // Add item in logs if has quantity or stock entered
    protected function addLogsIfHasQuantity(array $data)
    {
        if ($data['result']) {
            $id         = $this->insertID;
            $arr        = $data['data'];
            $inputs     = [
                'inventory_id'      => $id,
                'stocks'            => $arr['quantity'] ?? $arr['stocks'],
                'parent_stocks'     => $arr['quantity'] ?? $arr['stocks'],
                'created_by'        => session('username'),
            ];

            $this->db->table('inventory_logs')->insert($inputs);
        }

        return $data;
    }

    // Get the total item stocks count from the inventory
    public function getItemTotalStocks($id = null)
    {
        $this->select(new \CodeIgniter\Database\RawSql('SUM(stocks) AS total_stocks'));
        $this->where('deleted_at IS NULL');

        if (! is_null($id)) {
            $this->where($this->primaryKey, $id);
        }

        $result = $this->first();

        return $result['total_stocks'] ?? 0;
    }

    // Set/format columns to include in select
    public function columns($joinView = false, $withboth = false, $date_format = false)
    {
        $columns        = "
            {$this->table}.id,
            {$this->table}.item_model,
            {$this->table}.item_description,
            ".dt_sql_number_format("{$this->table}.item_sdp")." AS item_sdp,
            ".dt_sql_number_format("{$this->table}.item_srp")." AS item_srp,
            ".dt_sql_number_format("{$this->table}.project_price")." AS project_price,
            ".dt_sql_number_format("({$this->table}.stocks * {$this->table}.item_sdp)")." AS total_price,
            {$this->table}.stocks,
            {$this->table}.date_of_purchase,
            {$this->table}.location,
            {$this->table}.supplier_id
        ";
        $w_dropdown     = ",
            {$this->view}.category_name,
            {$this->view}.subcategory_name,
            {$this->view}.brand,
            {$this->view}.size,
            {$this->view}.unit,
            {$this->view}.created_by_name,
            {$this->view}.supplier_name
        ";
        $wo_dropdown    = ",
            {$this->table}.category,
            {$this->table}.sub_category,
            {$this->table}.item_brand,
            {$this->table}.item_size,
            {$this->table}.stock_unit,
            {$this->table}.created_by
        ";

        if ($date_format) {
            $columns .= ",
                ".dt_sql_date_format("{$this->table}.date_of_purchase")." AS date_purchase,
                ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at_formatted
            ";
        }

        if ($withboth && $joinView) {
            $columns .= $w_dropdown . $wo_dropdown;
            return $columns;
        }

        $columns .= ($joinView) ? $w_dropdown: $wo_dropdown;
        return $columns;
    }

    // Join inventory_view via id
    public function joinView($builder)
    {
        $builder->join($this->view, "{$this->table}.id = {$this->view}.inventory_id", 'left');
    }

    // Get inventories
    public function getInventories($id = null, $joinView = false, $withboth = false)
    {
        $columns = $this->columns($joinView, $withboth);
        $builder = $this->select($columns);

        if ($joinView) $this->joinView($builder);
        return $id ? $builder->find($id) : $builder->findAll();
    }
    
    // Get inventory categories - all or distinct
    public function getInvCategories($id = null, $is_distinct = false)
    {
        $columns = "TRIM(inventory.category) AS category, TRIM(dd.dropdown) AS dropdown";
        $builder = $this->select($columns);
        $builder->join('inventory_dropdowns as dd', 'inventory.category = dd.dropdown_id', 'left');

        if ($is_distinct) $builder->distinct();        
        return $id ? $builder->find($id) : $builder->findAll();
    }
    
    // Get inventory sub-categories - all or distinct
    public function getInvSubCategories($id = null, $is_distinct = false)
    {
        $columns = "TRIM(inventory.category) AS category, TRIM(dd.dropdown) AS dropdown";
        $builder = $this->select($columns);
        $builder->join('inventory_dropdowns as dd', 'inventory.category = dd.dropdown_id', 'left');

        if ($is_distinct) $builder->distinct();        
        return $id ? $builder->find($id) : $builder->findAll();
    }
    
     // For DataTables
    public function noticeTable($request) 
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->columns(true, false, true));
        $this->joinView($builder);

        if (isset($request['params'])) {
            $params         = $request['params'];
            $category_ids   = array_filter($params['category'], function($val) {
                if (!str_contains($val, 'other__')) return $val;
            });

            if (! empty($category_ids)) $builder->whereIn("{$this->table}.category", $category_ids);
            if (! empty($params['sub_dropdown'])) {
                $ids    = implode(',', $params['sub_dropdown']);
                $in     = "IN({$ids})";
                $where  = "({$this->table}.sub_category {$in} OR {$this->table}.item_brand {$in} OR {$this->table}.item_size {$in} OR {$this->table}.stock_unit {$in})";

                $builder->where($where);
            }
        }

        $builder->where("{$this->table}.deleted_at", null);
        $builder->orderBy("{$this->table}.id", 'DESC');
        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $dropdown   = false;
            $buttons    = dt_button_actions($row, $id, $permissions, $dropdown); 
            $stock      = "{$row['stocks']}";

            if (check_permissions($permissions, 'ITEM_IN')) {
                // Item In
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? 'Item In' : '',
                    'button'    => 'btn-primary',
                    'icon'      => 'fas fa-plus-circle',
                    'condition' => 'onclick="itemIn('.$row["$id"].', '.$stock.')" title="Item In"',
                ], $dropdown);
            }

            if (check_permissions($permissions, 'ITEM_OUT')) {
                // Item Out
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? 'Item Out' : '',
                    'button'    => 'btn-secondary',
                    'icon'      => 'fas fa-minus-circle',
                    'condition' => 'onclick="itemOut('.$row["$id"].', '.$stock.')" title="Item Out"',
                ], $dropdown);
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
    }
}
