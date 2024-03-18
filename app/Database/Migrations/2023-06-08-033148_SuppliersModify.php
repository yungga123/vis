<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SuppliersModify extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('suppliers',[
            'payment_terms' => [
                'name' => 'payment_terms',
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('suppliers',[
            'payment_terms' => [
                'name' => 'payment_terms',
                'type' => 'int'
            ]
        ]);
    }
}
