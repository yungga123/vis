<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InventoryLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'inventory_logs_id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'inventory_id' => [
                'type' => "INT",
                // 'unsigned' => true,
                'comment' => 'Foreign key of inventory primary id',
            ],
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
            'stocks' => [
                'type' => "DECIMAL",
                'constraint' => [18,2],
                'comment' => 'Quantity or stock entered by user'
            ],
            'parent_stocks' => [
                'type' => "DECIMAL",
                'constraint' => [18,2],
                'comment' => 'Current stocks in the masterlist - inventory table'
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
            'action' => [
                'type' => "ENUM",
                'constraint' => ['ITEM_IN', 'ITEM_OUT'],
                'default' => 'ITEM_IN',
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 100,
                'comment' => 'Using employee id'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('inventory_logs_id');
        $this->forge->createTable('inventory_logs');
    }

    public function down()
    {
        $this->forge->dropTable('inventory_logs');
    }
}
