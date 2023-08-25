<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInMailConfigTable extends Migration
{
    private CONST TABLE = 'mail_config';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'is_enable' => [
                'type' => 'ENUM',
                'constraint' => ['YES', 'NO'],
                'default' => 'YES',
                'after' => 'recepients',
                'comment' => 'Indicator if mail sending is enabled',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, 'is_enable');
    }
}
