<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterFieldsInPRFItemsTable extends Migration
{
    private CONST TABLE = 'prf_items';

    public function up()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'quantity_out' => [
                'type' => 'DECIMAL',
                'constraint' => [10,2],
            ],
            'returned_q' => [
                'type' => 'DECIMAL',
                'constraint' => [10,2],
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn(self::TABLE, [
            'quantity_out' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
            ],
            'returned_q' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
            ],
        ]);
    }
}
