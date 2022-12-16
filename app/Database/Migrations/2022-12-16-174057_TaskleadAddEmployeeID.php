<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskleadAddEmployeeID extends Migration
{
    public function up()
    {
        $table = 'tasklead';
        $fields = [
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'id'
            ]
        ];
        $this->forge->addColumn($table, $fields);
    }

    public function down()
    {
        $table = 'tasklead';
        $fields = ['employee_id'];

        $this->forge->dropColumn($table,$fields);
    }
}
