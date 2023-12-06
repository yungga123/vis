<?php

namespace App\Models;

use CodeIgniter\Model;

class MailNotifModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mail_notifs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'module_code',
        'has_mail_notif',
        'is_mail_notif_enabled',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'module_code'   => 'required',
        'column'        => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get the mail notifs list
     * 
     * @param string|array|null $module_code
     * @param string|array $columns
     * 
     * @return array|object
     */
    public function getMailNotifs($module_code = null, $columns = '')
    {
        $columns = $columns ? $columns : $this->allowedFields;
        $builder = $this->select($columns);

        if ($module_code) {
            if (is_array($module_code)) {
                $builder->whereIn('module_code', $module_code);
                return $builder->findAll();
            }

            $builder->where('module_code', $module_code);
            return $builder->first();
        }

        return $builder->findAll();
    }

    /**
     * Check record if exists via module_code
     * 
     * @param string $module_code
     * 
     * @return bool
     */
    public function exists($module_code)
    {
        $result = $this->getMailNotifs($module_code);
        return !empty($result);
    }

    /**
     * Check module if has mail notif and is enabled via module_code
     * 
     * @param string $module_code
     * 
     * @return bool
     */
    public function isMailNotifEnabled($module_code)
    {
        $result = $this->getMailNotifs($module_code);
        log_message('info', '$result: '. json_encode($result));
        return !empty($result) && $result['is_mail_notif_enabled'] == 1;
    }
}
