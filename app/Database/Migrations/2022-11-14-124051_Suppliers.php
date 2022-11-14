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
            'supplier_id' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ],
            'supplier_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('suppliers');
    }

    public function down()
    {
        $this->forge->dropTable('suppliers');
    }
}
