<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Suppliers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'supplier_name' => [
                'type'       => 'varchar',
                'constraint' => 500
            ],
            'supplier_type' => [
                'type'       => 'varchar',
                'constraint' => 100
            ],
            'contact_person' => [
                'type'       => 'varchar',
                'constraint' => 500
            ],
            'contact_number' => [
                'type'       => 'varchar',
                'constraint' => 100
            ],
            'viber' => [
                'type'       => 'varchar',
                'constraint' => 100
            ],
            'payment_terms' => [
                'type'       => 'int',
                'constraint' => 11
            ],
            'payment_mode' => [
                'type'       => 'varchar',
                'constraint' => 100
            ],
            'product' => [
                'type'       => 'varchar',
                'constraint' => 1000
            ],
            'remarks' => [
                'type'       => 'varchar',
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
        $this->forge->createTable('suppliers');
    }

    public function down()
    {
        $this->forge->dropTable('suppliers');
    }
}
