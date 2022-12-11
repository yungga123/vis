<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EmployeesModifyField extends Migration
{
    public function up()
    {
        $fields = [
            'email_address' => [
                'name' => 'email_address',
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true
            ],
        ];
        $this->forge->modifyColumn('employees', $fields);
    }

    public function down()
    {
        $fields = [
            'email_address' => [
                'name' => 'email_address',
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false
            ],
        ];
        $this->forge->modifyColumn('employees', $fields);
    }
}
