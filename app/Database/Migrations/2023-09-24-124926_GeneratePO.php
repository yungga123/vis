<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GeneratePO extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'purchase_order_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'purchase_order_date' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'supplier' => [
                'type' => 'INT',
                'comment' => 'Connected to Suppliers'
            ],
            'ship_to' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'default' => 'VINCULUM TECHNOLOGIES CORPORATION',
                'comment' => 'Default is VINCULUM TECHNOLOGIES CORPORATION'
            ],
            'attention_to' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'comment' => 'Manual type who will receive the PO Generated.'
            ],
            'requestor' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Connected to Employees'
            ],
            'request_form_number' => [
                'type' => 'INT',
                'comment' => 'Connected to RPF' 
            ],
            'sub_total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'comment' => 'Total amount of items without VAT.'
            ],
            'mode_of_payment' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('purchase_orders');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_orders');
    }
}
