<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerVtField extends Migration
{

    private CONST TABLE = 'customers_vt';


    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'customer_type' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'referred_by',
                'default' => 'Commercial'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'customer_type');
    }
}
