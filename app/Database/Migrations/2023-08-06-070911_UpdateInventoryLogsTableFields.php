<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInventoryLogsTableFields extends Migration
{
    private CONST TABLE = 'inventory_logs';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'stocks' => [
                'type' => "DECIMAL",
                'constraint' => [7,2]
            ],
            'parent_stocks' => [
                'type' => "DECIMAL",
                'constraint' => [7,2],
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'comment' => 'Using username'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => false,
                'default' => 'purchase',
            ],
        ]);

        $this->forge->dropColumn(self::TABLE, [
            'item_size',
            'item_sdp',
            'item_srp',
            'project_price',
            'stock_unit',
            'date_of_purchase',
            'location',
            'supplier',
        ]);
    }

    public function down()
    {
        $this->forge->addColumn(self::TABLE, [
            'item_size' => [
                'type' => "INT",
                'comment' => 'Connected to inventory_dropdowns',
            ],
            'item_sdp' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'item_srp' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'project_price' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'stock_unit' => [
                'type' => "INT",
                'comment' => 'Connected to inventory_dropdowns',
            ],
            'date_of_purchase' => [
                'type' => "DATE"
            ],
            'location' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
            'supplier' => [
                'type' => "VARCHAR",
                'constraint' => 200
            ],
        ]);

        $this->forge->modifyColumn(self::TABLE, [
            'stocks' => [
                'type' => "DECIMAL",
                'constraint' => [18,2]
            ],
            'parent_stocks' => [
                'type' => "DECIMAL",
                'constraint' => [18,2],
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 100,
                'comment' => 'Using employee id'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'supplier',
            ],
        ]);
    }
}
