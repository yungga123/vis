<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;

class SupplierBrandsModel extends Model
{
    /* Declare trait here to use */
    use HRTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'supplier_brands';
    protected $view             = 'suppliers_brand_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'supplier_id',
        'brand_name',
        'product',
        'warranty',
        'sales_person',
        'sales_contact_number',
        'technical_support',
        'technical_contact_number',
        'remarks',
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

    // DataTable default columns 
    public function dtColumns()
    {
        return "
            {$this->table}.brand_name,
            {$this->table}.product,
            {$this->table}.warranty,
            {$this->table}.sales_person,
            {$this->table}.sales_contact_number,
            {$this->table}.technical_support,
            {$this->table}.technical_contact_number,
            {$this->table}.remarks,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at
        ";
    }

    // Join with suppliers
    public function joinSupplier($builder = null, $model = null, $type = 'left')
    {
        $builder    = $builder ?? $this;
        $model      = $model ?? new SuppliersModel();
        $builder->join($model->table, "{$model->table}.id = {$this->table}.supplier_id", $type);
        return $this;
    }

    public function noticeTable($supplier_id)
    {
        $builder = $this->db->table($this->table);
        $builder->select("{$this->table}.id, ". $this->dtColumns());

        $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');

        $builder->where("{$this->table}.supplier_id", $supplier_id);
        $builder->where("{$this->table}.deleted_at IS NULL");

        return $builder;
    }

    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $buttons = '';

            if (check_permissions($permissions, 'EDIT')) {
                // Add Brand
                $buttons .= <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="brand_edit({$row[$id]})"  data-toggle="modal" data-target="#modal_add_supplier_brand" title="Edit"><i class="fas fa-edit"></i></button>
                EOF;
            }

            if (check_permissions($permissions, 'DELETE')) {
                // Add Brand
                $buttons .= <<<EOF
                    <button class="btn btn-sm btn-danger" onclick="brand_remove({$row["id"]})" title="Delete"><i class="fas fa-trash"></i></button>
                EOF;
            }

            return $buttons ?? '~~N/A~~';
        };
        return $closureFun;
    }
}
