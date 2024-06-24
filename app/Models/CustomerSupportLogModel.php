<?php

namespace App\Models;

use App\Traits\HRTrait;
use CodeIgniter\Model;

class CustomerSupportLogModel extends Model
{
    /* Declare trait here to use */
    use HRTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'customer_support_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_support_id',
        'findings',
        'action',
        'troubleshooting',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'findings'   => [
            'rules' => 'if_exist|required',
            'label' => 'findings',
        ],
        'action'   => [
            'rules' => 'permit_empty',
            'label' => 'initial action taken by the customer',
        ],
        'troubleshooting'   => [
            'rules' => 'permit_empty',
            'label' => 'initial troubleshooting done?',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setLoggedByAndAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set the value for 'status_' by and at before updating status
     */
    protected function setLoggedByAndAt(array $data)
    {
        $logged = 'logged';

        $data['data'][$logged .'_by'] = session('username');
        $data['data'][$logged .'_at'] = date('Y-m-d H:i:s');
        
        return $data;
    }

    /**
     * For fetching data
     */
    public function fetch(string|int $id, $order_by = 'ASC'): array|null
    {
        $columns = "
            {$this->table}.customer_support_id,
            {$this->table}.findings,
            {$this->table}.action,
            {$this->table}.troubleshooting,
            lb.employee_name AS logged_by,
            ".dt_sql_datetime_format("{$this->table}.logged_at")." AS logged_at,
        ";
        $builder = $this->select($columns);

        $this->joinAccountView($builder, 'logged_by', 'lb');

        $builder->where("{$this->table}.customer_support_id", $id);
        $builder->orderBy("{$this->table}.logged_at", strtoupper($order_by));

        return $builder->findAll();
    }
}
