<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SupplierBrands extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'supplier_id' => [
                'type' => 'int',
                'constraint' => 11
            ],
            'brand_name' => [
                'type' => 'varchar',
                'constraint' => 200
            ],
            'product' => [
                'type' => 'varchar',
                'constraint' => 1000
            ],
            'warranty' => [
                'type' => 'int',
                'constraint' => 11
            ],
            'sales_person' => [
                'type' => 'varchar',
                'constraint' => 1000
            ],
            'sales_contact_number' => [
                'type' => 'varchar',
                'constraint' => 500
            ],
            'technical_support' => [
                'type' => 'varchar',
                'constraint' => 1000
            ],
            'technical_contact_number' => [
                'type' => 'varchar',
                'constraint' => 1000
            ],
            'remarks' => [
                'type' => 'varchar',
                'constraint' => 1000
            ],
            'created_at' => [
                'type' => "datetime"
            ],
            'updated_at' => [
                'type' => "datetime",
                'null' => true
            ],
            'deleted_at' => [
                'type' => "datetime",
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('supplier_brands');
    }

    public function down()
    {
        $this->forge->dropTable('supplier_brands');
    }
}
