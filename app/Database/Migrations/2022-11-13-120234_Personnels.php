<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Personnels extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'employee_id' => [
                'type' => "VARCHAR",
                'constraint' => 100
            ],
            'firstname' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'middlename' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'lastname' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'birthdate' => [
                'type' => "DATE"
            ],
            'contact_number' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'position' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'address' => [
                'type' => "TEXT"
            ],
            'sss_number' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'tin_number' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'pagibig_number' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'philhealth_number' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'status' => [
                'type' => "VARCHAR",
                'constraint' => 500
            ],
            'date_probation' => [
                'type' => "DATE"
            ],
            'date_hired' => [
                'type' => "DATE"
            ],
            'daily_rate' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'sss_rate' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'pagibig_rate' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'philhealth_rate' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'tax' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'sl_credit' => [
                'type' => "INT"
            ],
            'vl_credit' => [
                'type' => "INT"
            ],
            'remarks' => [
                'type' => "TEXT"
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('personnels');
    }

    public function down()
    {
        $this->forge->dropTable('personnels');
    }
}
