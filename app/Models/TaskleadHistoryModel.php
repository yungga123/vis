<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskleadHistoryModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tasklead_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "tasklead_id",
        "quarter",
        "status",
        "customer_id",
        "project",
        "project_amount",
        "quotation_num",
        "forecast_close_date",
        "min_forecast_date",
        "max_forecast_date",
        "remark_next_step",
        "close_deal_date",
        "project_start_date",
        "project_finish_date"
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
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
}
