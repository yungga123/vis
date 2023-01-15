<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskleadAddExistingCustomer extends Migration
{
    public function up()
    {
        $table = 'tasklead';
        $fields = [
            'existing_customer' => [
                'type' => 'BOOLEAN',
                'after' => 'status'
            ]
        ];
        $this->forge->addColumn($table, $fields);
    }

    public function down()
    {
        $table = 'tasklead';
        $fields = ['existing_customer'];

        $this->forge->dropColumn($table,$fields);
    }
}
