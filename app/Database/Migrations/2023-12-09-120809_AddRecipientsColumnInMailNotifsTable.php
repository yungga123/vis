<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRecipientsColumnInMailNotifsTable extends Migration
{
    private CONST TABLE = 'mail_notifs';

    public function up()
    {
        $this->forge->addColumn(self::TABLE, [
            'cc_recipients' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'is_mail_notif_enabled',
                'comment' => 'Email CC recipients'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(self::TABLE, ['cc_recipients']);
    }
}
