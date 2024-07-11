<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServiceReportItemsTable extends Migration
{
    private CONST TABLE = 'service_report_items';

    public function up()
    {
        $this->forge->addField([
            'service_report_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customer_supports table',
            ],
            'item_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'item_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'item_qty' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'item_unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
            ],
            'item_total_price' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
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
