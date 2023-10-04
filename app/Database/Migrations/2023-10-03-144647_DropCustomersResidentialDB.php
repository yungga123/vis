<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropCustomersResidentialDB extends Migration
{
    public function up()
    {
        $this->forge->dropTable('customers_residential');
    }

    public function down()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'forecast' => [
                'type' => 'BOOLEAN',
                'default' => true
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'contact_person' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_province' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_city' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_brgy' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_sub' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'email_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 100
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
        $this->forge->createTable('customers_residential');
    }
}
