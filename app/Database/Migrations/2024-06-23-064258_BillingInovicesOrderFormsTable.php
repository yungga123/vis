<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BillingInovicesOrderFormsTable extends Migration
{
    private CONST TABLE = 'billing_invoices_order_forms';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'order_form_id' => [
                'type' => 'INT',
                'comment' => 'Connected to order_forms table',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'due_date' => [
                'type' => 'DATE',
            ],
            'bill_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'billing_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'billing_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
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
            'withholding_tax_percent' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'withholding_tax' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
            ],
            'date_paid' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'paid_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'receipt_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
            'vat_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
            ],
            'overdue_interest' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
            ],
            'grand_total' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'comment' => 'billing_amount + vat_amount + overdue_interest',
            ],
            'additional_description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'grand_total',
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'approved_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'approved_at datetime default null',
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
