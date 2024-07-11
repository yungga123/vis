<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServiceReportUnitConditionsTable extends Migration
{
    private CONST TABLE = 'service_report_units';

    public function up()
    {
        $this->forge->addField([
            'service_report_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customer_supports table',
            ],
            'item' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'defective_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'order' => [
                'type' => 'SMALLINT',
            ],
        ]);

        $this->forge->addForeignKey('service_report_id', 'service_reports', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
