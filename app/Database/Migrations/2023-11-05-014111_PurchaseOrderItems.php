<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PurchaseOrderItems extends Migration
{
    private CONST TABLE = 'purchase_order_items';

    public function up()
    {
        $this->forge->dropTable('purchase_orders_items', true);
        $this->forge->addField([
            'po_id' => [
                'type' => 'INT',
                'comment' => 'Connected to purchase_orders table'
            ],
            'inventory_id' => [
                'type' => 'INT',
                'comment' => 'Connected to inventory table using the primary id'
            ],
            'is_generated' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
                'comment' => 'Identifier if included in generation of Purchase Order'
            ],
        ]);

        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('inventory_id', 'inventory', 'id', '', 'CASCADE');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
