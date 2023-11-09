<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PurchaseOrders extends Migration
{
    private CONST TABLE = 'purchase_orders';

    public function up()
    {
        if ($this->db->tableExists(self::TABLE)) {
            $query = $this->db->table(self::TABLE)->get();

            if (empty($query->getResultArray()) || $this->db->fieldExists('request_form_number', self::TABLE)) {
                $this->db->query('DROP TABLE IF EXISTS '. self::TABLE);
            }
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'rpf_id' => [
                'type' => 'INT',
                'comment' => 'Connected to request_purchase_forms table'
            ],
            'supplier_id' => [
                'type' => 'INT',
                'comment' => 'Connected to suppliers table'
            ],
            'attention_to' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Manual type who will receive the PO Generated.'
            ],
            'requestor' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Connected to Employees'
            ],
            'with_vat' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
            ],
            'sub_total' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2],
                'null' => true,
                'comment' => 'Total amount of items without VAT.'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
            ],
            'approved_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'filed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true
            ],
            'created_at datetime default current_timestamp',
            'approved_at datetime default null',
            'filed_at datetime default null',
            'updated_at datetime default null on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('rpf_id', 'request_purchase_forms', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', '', 'CASCADE');
        $this->forge->createTable(self::TABLE, true);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE, true);
    }
}
