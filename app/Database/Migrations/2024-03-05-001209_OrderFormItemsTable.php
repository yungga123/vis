<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrderFormItemsTable extends Migration
{
    private CONST TABLE = 'order_form_items';

    public function up()
    {
        $this->forge->addField([
            'order_form_id' => [
                'type' => 'INT',
                'comment' => 'Connected to order_forms table',
            ],
            'inventory_id' => [
                'type' => 'INT',
                'comment' => 'Connected to inventory table',
            ],
            'quantity' => [
                'type' => 'FLOAT',
            ],
            'discount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
            ],
        ]);

        $this->forge->addForeignKey('order_form_id', 'order_forms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}