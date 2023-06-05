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
        'category' => [
            'rules' => 'required',
            'label' => 'category'
        ],
        'sub_category' => [
            'rules' => 'required',
            'label' => 'sub-category'
        ],
        'item_brand' => [
            // 'rules' => 'required|string|min_length[3]|max_length[200]',
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
    protected $beforeInsert   = ['beforeInsert'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['beforeInsert'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Set the value for encoder before inserting
    protected function beforeInsert(array $data)
    {
        if (isset($data['data']['item_size']) && $data['data']['item_size'] === 'na') {
            $data['data']['item_size'] = '';
        }
        if (isset($data['data']['stock_unit']) && $data['data']['stock_unit'] === 'na') {
            $data['data']['stock_unit'] = '';
        }
        $data['data']['encoder'] = session('employee_id');

        return $data;
    }

    // Get inventories
    public function getInventories($id = null)
    {
        $columns = implode(',', $this->allowedFields);
        $columns = $columns . "
            ,(SELECT CONCAT(ev.firstname, ' ', ev.lastname) FROM employees AS ev WHERE encoder = ev.employee_id) AS encoder_name             
        ";

        $builder = $this->select($columns);
        return $id ? $builder->find($id) : $builder->findAll();
    }
    
    // Get inventory categories - all or distinct
    public function getInvCategories($id = null, $is_distinct = false)
    {
        $columns = "inventory.category, dd.dropdown";
        $builder = $this->select($columns);
        $builder->join('inventory_dropdowns as dd', 'inventory.category = dd.dropdown_id', 'left');

        if ($is_distinct) $builder->distinct();        
        return $id ? $builder->find($id) : $builder->findAll();
    }
    
    // Get inventory sub-categories - all or distinct
    public function getInvSubCategories($id = null, $is_distinct = false)
    {
        $columns = "inventory.category, dd.dropdown";
        $builder = $this->select($columns);
        $builder->join('inventory_dropdowns as dd', 'inventory.category = dd.dropdown_id', 'left');

        if ($is_distinct) $builder->distinct();        
        return $id ? $builder->find($id) : $builder->findAll();
    }
    
     // For DataTables
     public function noticeTable($request) 
     {
        $builder = $this->db->table($this->table);
        $builder->select("
            id,
            (SELECT dropdown FROM inventory_dropdowns AS db WHERE category = db.dropdown_id) AS category,
            (SELECT dropdown FROM inventory_dropdowns AS db WHERE sub_category = db.dropdown_id) AS sub_category,
            IF(item_brand IS NULL or TRIM(item_brand) = '', 'N/A', (SELECT dropdown FROM inventory_dropdowns AS db WHERE item_brand = db.dropdown_id)) AS item_brand,
            item_model,
            item_description,
            IF(item_size IS NULL or TRIM(item_size) = '', 'N/A', (SELECT dropdown FROM inventory_dropdowns AS db WHERE item_size = db.dropdown_id)) AS item_size,
            item_sdp,
            IF(total IS NULL or TRIM(total) = '', 'N/A', total) AS total,
            stocks,
            IF(stock_unit IS NULL or TRIM(stock_unit) = '', 'N/A', (SELECT dropdown FROM inventory_dropdowns AS db WHERE stock_unit = db.dropdown_id)) AS stock_unit,
            (SELECT CONCAT(firstname, ' ', lastname) FROM employees AS ev WHERE encoder = ev.employee_id) AS encoder
        ");

        if (isset($request['params'])) {
            $params = $request['params'];
            $builder->whereIn('category', $params['category']);

            if (! empty($params['sub_dropdown'])) {
                $ids    = implode(',', $params['sub_dropdown']);
                $in     = "IN({$ids})";
                $where  = "(sub_category {$in} OR item_size {$in} OR stock_unit {$in})";

                $builder->where($where);
            }
        }

        $builder->where('deleted_at', null);
        // log_message('error', $builder->getCompiledSelect(false));
        return $builder;
    }

    public function buttons($permissions)
    {
        $id = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            if (is_admin()) {
                return <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  data-toggle="modal" title="Edit"><i class="fas fa-edit"></i> </button> 
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            $edit = '<button class="btn btn-sm btn-warning" title="Cannot edit" disabled><i class="fas fa-edit"></i> </button>';

            if (check_permissions($permissions, 'EDIT') && !is_admin()) {
                $edit = <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})"  data-toggle="modal" title="Edit"><i class="fas fa-edit"></i> </button> 
                EOF;
            }

            $delete = '<button class="btn btn-sm btn-danger" title="Cannot delete" disabled><i class="fas fa-trash"></i> </button>';

            if (check_permissions($permissions, 'DELETE') && !is_admin()) {
                $delete = <<<EOF
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            return $edit. $delete;
        };
        
        return $closureFun;
    }
}
