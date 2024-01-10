<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SalaryRatesTable extends Migration
{
    private CONST TABLE = 'salary_rates';

    public function up()
    {
        $this->forge->addField([
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Connected to employees table'
            ],
            'rate_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'salary_rate' => [
                'type' => 'DECIMAL',
                'constraint' => [7,2],
            ],
            'is_current' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
                'comment' => 'Whether it is the current rate'
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

        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
