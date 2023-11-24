<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Services\Mail\AdminMailService;

class ScheduleModel extends Model
{
    /* Declare trait here to use */
    use HRTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'job_order_id',
        'title',
        'description',
        'type',
        'start',
        'end',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'date_range' => [
            'rules' => 'required',
            'label' => 'date & time range'
        ],
        'title' => [
            'rules' => 'required|string|min_length[2]|max_length[200]',
            'label' => 'title'
        ],
        'description' => [
            'rules' => 'required|string|min_length[5]',
            'label' => 'description'
        ],
        'type' => [
            'rules' => 'required',
            'label' => 'type'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = ['mailNotif'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Mail notif after Schedule created
    protected function mailNotif(array $data)
    {
        if ($data['result']) {
            $id         = $data['id'];
            $columns    = "
                {$this->table}.id,
                {$this->table}.job_order_id,
                {$this->table}.title,
                {$this->table}.description,
                {$this->table}.type,
                {$this->table}.start,
                {$this->table}.end,
                {$this->table}.created_at,
                cb.employee_name AS created_by
            ";
            $builder    = $this->select($columns);
            $builder->where("{$this->table}.id", $id);
            $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');

            $record     = $builder->first();

            // Send mail notification
            $service = new AdminMailService();
            $service->sendScheduleMailNotif($record);
        }
        
        return $data;
    }

    // For fetching schedules
    public function getSchedules($id = null, $columns = '*')
    {
        $builder = $this->select($columns);
        $builder->where("{$this->table}.deleted_at IS NULL");
        
        return $id ? $builder->find($id) : $builder->findAll();
    }

    // Get the current schedules
    public function getSchedulesForToday($count = false)
    {
        $where      = "(start BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59') OR '".date("Y-m-d")." 00:00:00' BETWEEN start AND end";
        $builder    = $this->select('title, description, type, start, end');
        $builder->where($where);
        $builder->where("{$this->table}.deleted_at IS NULL");

        if ($count) return $builder->countAllResults();

        return $builder->findAll();
    }
}
