<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RPFItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rpf_id' => [
                'type' => 'INT',
                'comment' => 'Connected to project_request_forms table',
            ],
            'inventory_id' => [
                'type' => 'INT',
                'comment' => 'Connected to inventory table',
            ],
            'quantity_in' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
                'comment' => 'Item quantity to be in (purchase)',
            ],
            'received_q' => [
                'type' => 'DECIMAL',
                'constraint' => [5,2],
                'comment' => 'Received quantity',
                'null' => true
            ],
            'received_date' => [
                'type' => 'date',
                'null' => true
            ],
            'delivery_date' => [
                'type' => 'date',
                'null' => true
            ],
        ]);

        $this->forge->addForeignKey('rpf_id', 'project_request_forms', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('inventory_id', 'inventory', 'id', '', 'CASCADE');
        $this->forge->createTable('rpf_items');
    }

    public function down()
    {
        $this->forge->dropTable('rpf_items');
    }
}
