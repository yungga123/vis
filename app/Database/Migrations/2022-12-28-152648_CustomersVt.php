<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomersVt extends Migration
{
    public function up()
    {
        $table = 'customers_vt';
        $fields = [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'forecast' => [
                'type' => 'BOOLEAN'
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
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ];
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable($table);
    }

    public function down()
    {
        $table = 'customers_vt';
        $this->forge->dropTable($table);
    }
}
