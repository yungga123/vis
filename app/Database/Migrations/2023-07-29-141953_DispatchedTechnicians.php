<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DispatchedTechnicians extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'dispatch_id' => [
                'type' => 'INT',
                'comment' => 'Connected to dispatch table',
            ],
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Connected to employees table',
            ],
        ]);

        $this->forge->addForeignKey('dispatch_id', 'dispatch', 'id', '', 'CASCADE');
        $this->forge->createTable('dispatched_technicians');
    }

    public function down()
    {
        $this->forge->dropTable('dispatched_technicians');
    }
}
