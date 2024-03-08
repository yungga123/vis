<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrderFormsTable extends Migration
{
    private CONST TABLE = 'order_forms';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'customer_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customers table',
            ],
            'customer_branch_id' => [
                'type' => 'INT',
                'comment' => 'Connected to customer_branches table',
                'null' => true,
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'purchase_at' => [
                'type' => 'DATETIME',
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'comment' => 'Not include vat and with discount',
            ],
            'total_discount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
            ],
            'with_vat' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
            ],
            'vat_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
            ],
            'grand_total' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'comment' => 'Include vat',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'accepted_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username - connected to accounts table',
                'null' => true
            ],
            'rejected_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username - connected to accounts table',
                'null' => true
            ],
            'item_out_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username - connected to accounts table',
                'null' => true
            ],
            'filed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username - connected to accounts table',
                'null' => true
            ],
            'created_at datetime default current_timestamp',
            'accepted_at datetime default null',
            'rejected_at datetime default null',
            'item_out_at datetime default null',
            'filed_at datetime default null',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}