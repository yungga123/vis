<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskleadStatus extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'percent' => [
                'type' => 'INT'
            ],
            'status_percent' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('tasklead_status');
    }

    public function down()
    {
        $this->forge->dropTable('tasklead_status');
    }
}
