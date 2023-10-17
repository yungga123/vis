<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsInJobOrdersTable extends Migration
{
    private CONST TABLE = 'job_orders';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'accepted_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true,
                'after' => 'created_by',
            ],
            'filed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true,
                'after' => 'accepted_by',
            ],
            'discarded_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true,
                'after' => 'filed_by',
            ],
            'reverted_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Use username',
                'null' => true,
                'after' => 'discarded_by',
            ],
            'accepted_at datetime DEFAULT null AFTER created_at',
            'filed_at datetime DEFAULT null AFTER accepted_at',
            'discarded_at datetime DEFAULT null AFTER filed_at',
            'reverted_at datetime DEFAULT null AFTER discarded_at',
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, [
            'accepted_by',
            'filed_by',
            'discarded_by',
            'reverted_by',
            'accepted_at',
            'filed_at',
            'discarded_at',
            'reverted_at',
        ]);
    }
}
