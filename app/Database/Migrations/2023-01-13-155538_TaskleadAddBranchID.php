<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskleadAddBranchID extends Migration
{
    public function up()
    {
        $table = 'tasklead';
        $fields = [
            'branch_id' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'after' => 'customer_id'
            ]
        ];
        $this->forge->addColumn($table, $fields);
    }

    public function down()
    {
        $table = 'tasklead';
        $fields = ['branch_id'];

        $this->forge->dropColumn($table,$fields);
    }
}
