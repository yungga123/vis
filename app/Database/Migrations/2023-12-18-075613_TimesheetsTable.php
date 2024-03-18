<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TimesheetsTable extends Migration
{
    private CONST TABLE = 'timesheets';

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
            'clock_date' => [
                'type' => 'DATE',
            ],
            'clock_in' => [
                'type' => 'TIME',
            ],
            'clock_out' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'total_hours' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'early_in' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'late' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'early_out' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'overtime' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'remark' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_manual' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
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
