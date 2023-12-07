<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelephoneFieldInCustomerTable extends Migration
{
    private CONST TABLE = 'customers';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'telephone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'contact_number',
            ],
            'is_cn_formatted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'contact_number',
                'comment' => 'Has contact number already formatted?',
            ],
        ]);
        $this->forge->modifyColumn(self::TABLE, [
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['telephone', 'is_cn_formatted']);
        $this->forge->modifyColumn(self::TABLE, [
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ]
        ]);
    }
}
