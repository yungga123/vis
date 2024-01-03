<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFieldsDecimalInPayrollTable extends Migration
{
    private CONST TABLE = 'payroll';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'gross_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'net_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'basic_salary' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'cutoff_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'daily_rate' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
            'hourly_rate' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'gross_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'net_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'basic_salary' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'cutoff_pay' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'daily_rate' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
            'hourly_rate' => [
                'type' => 'DECIMAL',
                'constraint' => [6,2],
            ],
        ]);
    }
}
