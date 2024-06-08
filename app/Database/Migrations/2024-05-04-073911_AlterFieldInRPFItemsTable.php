<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterFieldInRPFItemsTable extends Migration
{
    private CONST TABLE = 'rpf_items';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'quantity_in' => [
                'type' => 'DECIMAL',
                'constraint' => [10,2],
            ],
            'received_q' => [
                'type' => 'DECIMAL',
                'constraint' => [10,2],
            ],
            'discount' => [
                'type' => 'DECIMAL',
                'constraint' => [15,2],
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'quantity_in' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
            ],
            'received_q' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
            ],
            'discount' => [
                'type' => 'DECIMAL',
                'constraint' => [9,2],
            ],
        ]);
    }
}
