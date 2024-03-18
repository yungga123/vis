<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountFieldInRPFItemsTable extends Migration
{
    private CONST TABLE = 'rpf_items';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'discount' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
                'null' => true
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'discount');
    }
}
