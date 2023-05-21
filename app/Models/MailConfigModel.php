<?php

namespace App\Models;

use CodeIgniter\Model;

class MailConfigModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mail_config';
    protected $primaryKey       = 'mail_config_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'email_name',
        'email',
        'password',
        'oauth_client_id',
        'oauth_client_secret',
        'oauth_scope',
        'redirect_uri',
        'hostname',
        'access_type',
        'recepients',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'email_name'            => 'required|string|min_length[2]',
        'email'                 => 'required|valid_email',
        'password'              => 'required|min_length[10]',
        'oauth_client_id'       => 'permit_empty|min_length[10]',
        'oauth_client_secret'   => 'permit_empty|min_length[10]',
        'oauth_scope'           => 'permit_empty|valid_url',
        'redirect_uri'          => 'permit_empty|valid_url',
        'hostname'              => 'required',
        'access_type'           => 'permit_empty|string',
        'recepients'            => 'permit_empty|valid_emails',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getMailConfig($id = null)
    {
        return $id ? $this->find($id) : $this->first();
    }
    
    public function saveRefreshToken($refresh_token, $id = 1)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table($this->table);

        if (! empty($refresh_token))
            $builder->set(['refresh_token' => $refresh_token])
                ->where($this->primaryKey, $id ?? 1)
                ->update();
    }
}
