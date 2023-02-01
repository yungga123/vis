<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MailConfigSeeder extends Seeder
{
    public function run()
    {
        $table = 'mail_config';
        $data = [
            'email_name'            => getenv('GMAIL_NAME'),
            'email'                 => getenv('GMAIL_EMAIL'),
            'password'              => getenv('GMAIL_APP_PASSWORD'),
            'oauth_client_id'       => getenv('OAUTH2_CLIENTID'),
            'oauth_client_secret'   => getenv('OAUTH2_CLIENTSECRET'),
            'oauth_scope'           => getenv('OAUTH2_SCOPE'),
            'redirect_uri'          => getenv('OAUTH2_REDIRECTURI'),
            'hostname'              => getenv('GMAIL_HOST'),
            'access_type'           => getenv('OAUTH2_ACCESSTYPE'),
            'created_at'            => date('Y-m-d H:i:s'),
        ];

        $this->db->table($table)->insert($data);
    }
}