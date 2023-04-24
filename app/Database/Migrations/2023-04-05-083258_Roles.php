<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Roles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'role_id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'role_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at datetime default current_timestamp',
            'updated_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'updated_at datetime default null on update current_timestamp',
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('role_id');
        $this->forge->createTable('roles');
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
