<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PayrollTable extends Migration
{
    private CONST TABLE = 'payroll';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Connected to employees table'
            ],
            'cutoff_start' => [
                'type' => 'DATE',
            ],
            'cutoff_end' => [
                'type' => 'DATE',
            ],
            'gross_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2],
            ],
            'net_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2],
            ],
            'salary_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'basic_salary' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2],
            ],
            'cutoff_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2],
            ],
            'daily_rate' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2],
            ],
            'hourly_rate' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2],
            ],
            'working_days' => [
                'type' => 'DECIMAL',
                'constraint' => [2,1],
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
