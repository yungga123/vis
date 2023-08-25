<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PRFItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'prf_id' => [
                'type' => 'INT',
                'comment' => 'Connected to project_request_forms table',
            ],
            'inventory_id' => [
                'type' => 'INT',
                'comment' => 'Connected to inventory table',
            ],
            'quantity_out' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
                'comment' => 'Item quantity to be out',
            ],
            'returned_q' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
                'comment' => 'Returned quantity',
                'null' => true
            ],
            'returned_date' => [
                'type' => 'date',
                'null' => true
            ],
        ]);

        $this->forge->addForeignKey('prf_id', 'project_request_forms', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('inventory_id', 'inventory', 'id', '', 'CASCADE');
        $this->forge->createTable('prf_items');
    }

    public function down()
    {
        $this->forge->dropTable('prf_items');
    }
}
