<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskleadStatusAddFieldPercent extends Migration
{
    public function up()
    {
        $fields = [
            'preferences' => [
                'type' => 'INT',
                'after' => 'id'
            ],
        ];
        $this->forge->addColumn('tasklead_status', $fields);
    }

    public function down()
    {
        $this->forge->dropTable('tasklead_status');
    }
}
