<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewFieldInPrfItemsTable extends Migration
{
    private CONST TABLE = 'prf_items';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'is_filed' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['is_filed']);
    }
}