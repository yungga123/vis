<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVatAmountFieldInPurchaseOrdersTable extends Migration
{
    private CONST TABLE = 'purchase_orders';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'vat_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'after' => 'with_vat',
            ],
            'total_discount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'after' => 'sub_total',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['vat_amount', 'total_discount']);
    }
}
