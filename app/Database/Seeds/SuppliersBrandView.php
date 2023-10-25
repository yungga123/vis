<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuppliersBrandView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                suppliers_brand_view
        ");
        $db->query(
            "CREATE VIEW 
                suppliers_brand_view
            AS SELECT
                supplier_brands.id,
                supplier_brands.supplier_id,
                supplier_brands.brand_name,
                supplier_brands.product,
                supplier_brands.warranty,
                supplier_brands.sales_person,
                supplier_brands.sales_contact_number,
                supplier_brands.technical_support,
                supplier_brands.technical_contact_number,
                supplier_brands.remarks AS supplier_brands_remark,
                suppliers.supplier_name,
                suppliers.supplier_type,
                suppliers.others_supplier_type,
                suppliers.contact_person,
                suppliers.contact_number AS supplier_contact_number,
                suppliers.viber,
                suppliers.payment_terms,
                suppliers.payment_mode,
                suppliers.others_payment_mode,
                suppliers.remarks AS suppliers_remark
            FROM
                supplier_brands
            LEFT JOIN
                suppliers
            ON
                supplier_brands.supplier_id=suppliers.id
            WHERE
                supplier_brands.deleted_at IS NULL
            "
        );
    }
}
