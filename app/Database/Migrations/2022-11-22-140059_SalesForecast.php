<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SalesForecast extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'status_percent' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2]
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'customer' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'contact_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'project' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'project_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2]
            ],
            'quotation_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'hit' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'remark_next_step' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'close_deal_date' => [
                'type' => 'DATE'
            ],
            'project_start' => [
                'type' => 'DATE'
            ],
            'project_finish' => [
                'type' => 'DATE'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('sales_forecast');
    }

    public function down()
    {
        $this->forge->dropTable('sales_forecast');
    }
}
