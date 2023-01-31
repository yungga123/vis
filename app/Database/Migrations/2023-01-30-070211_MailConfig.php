<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MailConfig extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'mail_config_id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'email_name' => [
                'type' => "VARCHAR",
                'constraint' => 50
            ],
            'email' => [
                'type' => "VARCHAR",
                'constraint' => 50
            ],
            'password' => [
                'type' => "VARCHAR",
                'constraint' => 150
            ],
            'oauth_client_id' => [
                'type' => "VARCHAR",
                'constraint' => 255
            ],
            'oauth_client_secret' => [
                'type' => "VARCHAR",
                'constraint' => 255
            ],
            'oauth_scope' => [
                'type' => "VARCHAR",
                'constraint' => 150
            ],
            'redirect_uri' => [
                'type' => "VARCHAR",
                'constraint' => 150
            ],
            'hostname' => [
                'type' => "VARCHAR",
                'constraint' => 50
            ],
            'access_type' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => true
            ],
            'refresh_token' => [
                'type' => "VARCHAR",
                'constraint' => 500,
                'null' => true
            ],
            'recepients' => [
                'type' => "VARCHAR",
                'constraint' => 500,
                'null' => true
            ],
            'created_at' => [
                'type' => "datetime"
            ],
            'updated_at' => [
                'type' => "datetime",
                'null' => true
            ],
            'deleted_at' => [
                'type' => "datetime",
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('mail_config_id');
        $this->forge->createTable('mail_config');
    }

    public function down()
    {
        $this->forge->dropTable('mail_config');
    }
}
