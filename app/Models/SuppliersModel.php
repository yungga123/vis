<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\FilterParamTrait;
use App\Traits\HRTrait;

class SuppliersModel extends Model
{
    /* Declare trait here to use */
    use FilterParamTrait, HRTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'suppliers';
    protected $view             = 'suppliers_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'supplier_name',
        'supplier_type',
        'others_supplier_type',
        'address',
        'contact_person',
        'contact_number',
        'viber',
        'payment_terms',
        'payment_mode',
        'others_payment_mode',
        'product',
        'email_address',
        'bank_name',
        'bank_account_name',
        'bank_number',
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
        'supplier_name'      => [
            'label' => 'Supplier Name',
            'rules' => 'required'
        ],
        'supplier_type'      => [
            'label' => 'Type of Supplier',
            'rules' => 'required'
        ],
        'address'      => [
            'label' => 'Address',
            'rules' => 'required'
        ],
        'contact_person'      => [
            'label' => 'Contact Person',
            'rules' => 'required'
        ],
        'contact_number'      => [
            'label' => 'Contact Number',
            'rules' => 'required'
        ],
        'payment_terms'      => [
            'label' => 'Payment Terms',
            'rules' => 'required'
        ],
        'payment_mode'      => [
            'label' => 'Payment Mode',
            'rules' => 'required'
        ],
        'product'      => [
            'label' => 'Product',
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
            {$this->table}.id,
            {$this->table}.supplier_name,
            IF(UPPER({$this->table}.supplier_type) = 'OTHERS', CONCAT('Others - ', {$this->table}.supplier_type), {$this->table}.supplier_type) AS supplier_type,
            {$this->table}.address,
            {$this->table}.contact_person,
            {$this->table}.contact_number,
            {$this->table}.viber,
            {$this->table}.email_address,
            IF({$this->table}.payment_terms = 0, 'N/A', CONCAT({$this->table}.payment_terms, ' DAYS')) AS payment_terms,
            IF(UPPER({$this->table}.others_payment_mode) = 'OTHERS', CONCAT('Others - ', {$this->table}.payment_mode), {$this->table}.payment_mode) AS payment_mode,
            {$this->table}.product,
            {$this->table}.bank_name,
            {$this->table}.bank_account_name,
            {$this->table}.bank_number,
            {$this->table}.remarks,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at
        ";
    }

    // Get suppliers
    public function getSuppliers($id = null, $columns = '')
    {
        $columns = $columns ? $columns : [$this->primaryKey] + $this->allowedFields;
        $builder = $this->select($columns);
        $builder->where('deleted_at IS NULL');

        if ($id && is_array($id))
            return $builder->whereIn($this->primaryKey, $id)->findAll();

        return $id ? $builder->find($id) : $builder->findAll();        
    }

    // Join with supplier_brands
    public function joinSupplierBrand($builder = null, $model = null, $type = 'left')
    {
        $builder    = $builder ?? $this;
        $model      = $model ?? new SupplierBrandsModel();
        $builder->join($model->table, "{$model->table}.id = {$this->table}.supplier_id", $type);
        return $this;
    }

    public function noticeTable($request)
    {
        $builder    = $this->db->table($this->table);
        $builder->select($this->dtColumns());

        $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');
        $this->filterParam($request, $builder, 'supplier_type', 'supplier_type');
        $this->filterParam($request, $builder, 'payment_terms', 'payment_terms');
        $this->filterParam($request, $builder, 'payment_mode', 'payment_mode');

        $builder->where('deleted_at IS NULL');
        return $builder;
    }

    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $closureFun = function($row) use($id, $permissions, $dropdown) {
            $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

            if (check_permissions($permissions, 'ADD')) {
                // Add Brand
                $buttons .= <<<EOF
                    <button class="btn btn-sm btn-success" onclick="brand_add({$row[$id]})" title="Add Brand"><i class="fas fa-plus-square"></i></button>
                EOF;
            }

            // View Brands
            $buttons .= <<<EOF
                <button class="btn btn-sm btn-primary" onclick="supplierbrandRetrieve({$row[$id]}, '{$row['supplier_name']}')" title="View Details"><i class="fas fa-eye"></i></button>
            EOF;

            return dt_buttons_dropdown($buttons);
        };
        return $closureFun;
    }
}
