<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GeneratePOItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'purchase_order_id' => [
                'type' => 'INT',
                'comment' => 'Connected to purchase_orders/generatePO'
            ],
            'item_no' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'model' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'brand' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'size' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'quantity' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'unit_amount' => [
                'type' => 'VARCHAR',
                'constraint' => 1000
            ],
            'discounted_price' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2]
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'

        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('purchase_orders_items');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_orders_items');
    }
}
