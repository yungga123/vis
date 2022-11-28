<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Employees extends Migration
{
    public function up()
    {
        
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'firstname' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'middlename' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'employment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'date_hired' => [
                'type' => 'DATE'
            ],
            'date_resigned' => [
                'type' => 'DATE'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('employees');
    }

    public function down()
    {
        $this->forge->dropTable('employees');
    }
}
