<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Permission extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'permission_id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'role_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'module_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'permissions' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'added_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'updated_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'created_at' => [
                'type' => 'datetime'
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('permission_id');
        $this->forge->createTable('permissions');
    }

    public function down()
    {
        $this->forge->dropTable('permissions');
    }
}
