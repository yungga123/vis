<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SupplierBrandsModify extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('supplier_brands',[
            'warranty' => [
                'name' => 'warranty',
                'type' => 'varchar',
                'constraint' => 200,
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('supplier_brands',[
            'warranty' => [
                'name' => 'warranty',
                'type' => 'int'
            ]
        ]);
    }
}
