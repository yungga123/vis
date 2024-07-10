<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSellingPriceInOrderFormItemsTable extends Migration
{
    private CONST TABLE = 'order_form_items';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'selling_price' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'after' => 'inventory_id',
                'comment' => 'Final item selling price',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['selling_price']);
    }
}