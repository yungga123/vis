<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FundsHistoryTable extends Migration
{
    private CONST TABLE = 'funds_history';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'billing_invoice_id' => [
                'type' => 'INT',
                'comment' => 'Connected to billing_invoices table',
            ],
            'transaction_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'incoming',
            ],
            'transaction_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'comment' => 'Incoming or outgoing amount',
            ],
            'current_funds' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'comment' => 'Current funds before the transaction',
            ],
            'coming_from' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Billing Invoice',
            ],
            'expenses' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'remarks' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
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
