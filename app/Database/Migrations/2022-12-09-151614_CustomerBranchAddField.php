<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerBranchAddField extends Migration
{
    public function up()
    {
        $fields = [
            'branch_name' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'customer_id'
            ]
        ];
        $this->forge->addColumn('customer_branch', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('customer_branch', 'branch_name');
    }
}
