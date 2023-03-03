<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tasklead extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'quarter' => [
                'type' => 'INT'
            ],
            'status' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2]
            ],
            'customer_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'existing_customer' => [
                'type' => 'BOOLEAN',
            ],
            'customer_id' => [
                'type' => 'INT'
            ],
            'branch_id' => [
                'type' => 'VARCHAR',
                'constraint' => 10
            ],
            'project' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'project_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [18,2]
            ],
            'quotation_num' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'forecast_close_date' => [
                'type' => 'DATE',
                'default' => null
            ],
            'remark_next_step' => [
                'type' => 'TEXT'
            ],
            'close_deal_date' => [
                'type' => 'DATE',
                'default' => null
            ],
            'project_start_date' => [
                'type' => 'DATE',
                'default' => null
            ],
            'project_finish_date' => [
                'type' => 'DATE',
                'default' => null
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('tasklead');
    }

    public function down()
    {
        $this->forge->dropTable('tasklead');
    }
}
