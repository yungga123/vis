<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropCustomersVTTable extends Migration
{
    private CONST TABLE = 'customers_vt';

    public function up()
    {
        $this->forge->dropTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->addField([
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
                'constraint' => 500,
                'null' => true,
            ],
            'address_sub' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'email_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'referred_by' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
            ],
            'customer_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable(self::TABLE);
    }
}
