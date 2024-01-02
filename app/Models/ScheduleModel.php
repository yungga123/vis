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
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'job_order_id' => [
            'rules' => 'permit_empty',
            'label' => 'job order'
        ],
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
    protected $beforeInsert   = ['setCreatedBy'];
    protected $afterInsert    = ['mailNotif'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Set the value for created_by before inserting
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Mail notif after Schedule created
    protected function mailNotif(array $data)
    {
        if ($data['result']) {
            $record = [
                'id'            => $data['id'],
                'job_order_id'  => $data['data']['job_order_id'] ?? '',
                'title'         => $data['data']['title'],
                'description'   => $data['data']['description'],
                'type'          => $data['data']['type'],
                'start'         => $data['data']['start'],
                'end'           => $data['data']['end'],
                'created_at'    => $data['data']['created_at'],
                'created_by'    => session('name'),
            ];

            // Send mail notification
            $service = new AdminMailService();
            $service->sendScheduleMailNotif($record);
        }
        
        return $data;
    }

    // Join schedules with ob_orders_view
    public function joinJobOrders($builder = null, $model = null, $view = true)
    {
        $model ??= new JobOrderModel();
        $table = $view ? $model->view : $model->table;
        $field = $view ? 'job_order_id' : $model->primaryKey;

        ($builder ?? $this)
            ->join($table, "{$table}.{$field} = {$this->table}.job_order_id", 'left');

        return $this;
    }

    // For fetching schedules
    public function getSchedules($id = null, $columns = '*')
    {
        $this->select($columns);
        $this->where("{$this->table}.deleted_at IS NULL");
        
        return $id ? $this->find($id) : $this->findAll();
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

    // Where between dates
    public function whereBetween($start, $end, $builder = null)
    {
        $start      = format_date($start, 'Y-m-d');
        $end        = format_date($end, 'Y-m-d');
        $between    = "(
            {$this->table}.start BETWEEN '{$start}' AND '{$end}' OR 
            {$this->table}.end BETWEEN '{$start}' AND '{$end}'
        )";

        ($builder ?? $this)->where($between);

        return $this;
    }
}
