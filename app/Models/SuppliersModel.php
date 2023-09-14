<?php

namespace App\Models;

use CodeIgniter\Model;

class SuppliersModel extends Model
{
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
        "supplier_name",
        "supplier_type",
        "others_supplier_type",
        "contact_person",
        "contact_number",
        "viber",
        "payment_terms",
        "payment_mode",
        "others_payment_mode",
        "product",
        "email_address",
        "bank_name",
        "bank_number",
        "remarks",
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

    public function noticeTable()
    {
        $builder    = $this->db->table($this->view);
        $builder->select('*');

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

    public function supplierType() {
        $closureFun = function($row) {

            if ($row['supplier_type'] == 'Others') {
                return $row['supplier_type'].' - '.$row['others_supplier_type'];
            } else {
                return $row['supplier_type'];
            }
        };
        return $closureFun;
    }

    public function paymentTerms() {
        $closureFun = function($row) {

            if ($row['payment_terms'] == '0') {
                return 'No';
            } else {
                return $row['payment_terms'].' day/s';
            }
        };
        return $closureFun;
    }

    public function paymentMode() {
        $closureFun = function($row) {

            if ($row['payment_mode'] == 'Others') {
                return $row['payment_mode'].' - '.$row['others_payment_mode'];
            } else {
                return $row['payment_mode'];
            }
        };
        return $closureFun;
    }
}
