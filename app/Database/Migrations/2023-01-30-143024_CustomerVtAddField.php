<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerVtAddField extends Migration
{

    public function up()
    {
        $this->forge->addColumn(
            'customers_vt',
            [
                'customer_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'after' => 'id'
                ]
            ]
        );
    }

    public function down()
    {
        $this->forge->dropColumn(
            'customers_vt',
            [
                'customer_type'
            ]
        );
    }
}
