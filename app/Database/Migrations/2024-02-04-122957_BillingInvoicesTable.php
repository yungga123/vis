<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BillingInvoicesTable extends Migration
{
    private CONST TABLE = 'billing_invoices';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'tasklead_id' => [
                'type' => 'INT',
                'comment' => 'Connected to task_lead_booked table',
            ],
            'due_date' => [
                'type' => 'DATE',
            ],
            'bill_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'billing_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
            ],
            'billing_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'amount_paid' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'attention_to' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Manual type who will receive the Billing Invoice.'
            ],
            'with_vat' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'created_at datetime default current_timestamp',
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