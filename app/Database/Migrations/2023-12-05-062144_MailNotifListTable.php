<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MailNotifListTable extends Migration
{
    private CONST TABLE = 'mail_notifs';

    public function up()
    {
        $this->forge->addField([
            'module_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'has_mail_notif' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
                'comment' => 'Identifier where module has mail notification',
            ],
            'is_mail_notif_enabled' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
                'comment' => 'Whether mail notif is enabled',
            ],
            'updated_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Use username - connected to accounts table',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addPrimaryKey('module_code');
        $this->forge->addUniqueKey('module_code');
        $this->forge->createTable(self::TABLE);
    }

    public function down()
    {
        $this->forge->dropTable(self::TABLE);
    }
}
