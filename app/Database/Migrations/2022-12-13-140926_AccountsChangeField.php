<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AccountsChangeField extends Migration
{
    public function up()
    {
        $fields = [
            'employee_id' => [
                'name' => 'employee_id',
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
        ];
        $this->forge->modifyColumn('accounts', $fields);
    }

    public function down()
    {
        $fields = [
            'employee_id' => [
                'name' => 'employee_id',
                'type' => 'INT'
            ],
        ];
        $this->forge->modifyColumn('accounts', $fields);
    }
}
