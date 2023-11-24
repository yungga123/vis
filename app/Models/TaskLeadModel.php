<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\FilterParamTrait;
use App\Services\Mail\SalesMailService;

class TaskLeadModel extends Model
{
    /* Declare trait here to use */
    use FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'tasklead';
    protected $view             = 'task_lead';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "employee_id",
        "quarter", 
        "status",
        "customer_type",
        "existing_customer",
        "customer_id",
        "branch_id",
        "project", 
        "project_amount", 
        "quotation_num", 
        "forecast_close_date",
        "remark_next_step", 
        "close_deal_date", 
        "project_start_date", 
        "project_finish_date",
        "tasklead_type",
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        "customer_type" => ["label" => "Customer Type", "rules" => "required"],
        "existing_customer" => ["label" => "Existing Customer", "rules" => "required"],
        "customer_id" => ["label" => "Customer", "rules" => "required"],
        "quotation_num" => "max_length[100]",
        "quarter" => 'required',
        "status" => 'required',
        "customer_id" => 'required',
        "project" => 'max_length[500]',
        "project_amount" => 'permit_empty|decimal|max_length[18]',
        "remark_next_step" => 'required',
    ];
    protected $validationMessages   = [
        "quotation_num" => [
            "max_length" => "Max of 100 characters."
        ],
        "quarter" => [
            "required" => "This field is required.",
        ],
        "status" => [
            "required" => "This field is required."
        ],
        "customer_id" => [
            "required" => "This field is required.",
        ],
        "project" => [
            "max_length" => "Maximum of 500 characters."
        ],
        "project_amount" => [
            "required" => "This field is required.",
            "decimal" => "Numbers or decimals only.",
            "max_length" => "Maximum of 18 characters."
        ],
        "remark_next_step" => [
            "required" => "This field is required.",
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedBy'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = ['mailNotif'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variable(s)
    // DataTable default columns     
    protected $dtColumns      = [
        'id',
        'employee_name',
        'quarter',
        'status',
        'status_percent',
        'customer_name',
        'customer_type',
        'branch_name',
        'contact_number',
        'project',
        'project_amount',
        'quotation_num',
        'tasklead_type',
        'forecast_close_date',
        'min_forecast_date',
        'max_forecast_date',
        'status1',
        'remark_next_step',
        'close_deal_date',
        'project_start_date',
        'project_finish_date',
        'project_duration',
        'created_by',
        'created_at',
    ];
    protected $booked         = '100.00%';

    // Set the value for created_by before inserting
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Mail notif after record created
    protected function mailNotif(array $data)
    {
        $id = $data['id'];

        if ($data['result']) {
            $tlViewModel    = new TaskLeadView();
            $columns        = "
                id,
                status,
                status1 AS hit_or_missed,
                quotation_num,
                tasklead_type,
                employee_name AS manager,
                customer_name AS client,
                customer_type AS client_type,
                branch_name AS client_branch,
                quarter,
                project,
                project_amount,
                close_deal_date,
                project_start_date,
                project_finish_date,
                project_duration,
                remark_next_step
            ";
            $builder    = $tlViewModel->select($columns);
            $record     = $builder->where('id', $id)->first();

            if (! empty($record)) {
                // Send mail notification
                $service = new SalesMailService();
                $service->sendTaskleadMailNotif($record);
            }
        }
        
        return $data;
    }

    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
    }

    public function dtGetTaskLeads($booked = false)
    {
        $builder    = $this->db->table($this->view);
        $builder->select($this->dtColumns);

        if ($booked) $builder->where('status', $this->booked);
        return $builder;
    }

    public function noticeTable($request)
    {
        $builder    = $this->dtGetTaskLeads();
        $builder->where('status !=', $this->booked);

        if (is_admin() || is_executive() || is_manager()) {
        } else {
            $builder->where('employee_id', session('employee_id'));
        }

        $this->filterParam($request, $builder);
        $this->filterParam($request, $builder, 'customer_type', 'client_type');
        $this->filterParam($request, $builder, 'quarter', 'quarter');

        return $builder;
    }

    public function noticeTableExistingCustomer()
    {
        $builder = $this->db->table('task_lead_existing_customer');
        return $builder;
    }


    public function noticeTableWhere($employee_id)
    {
        $builder = $this->db->table('task_lead');
        $builder->where('employee_id', $employee_id);
        return $builder;
    }

    public function noticeTableWhereExistingCustomer($employee_id)
    {
        $builder = $this->db->table('task_lead_existing_customer');
        $builder->where('employee_id', $employee_id);
        return $builder;
    }

    public function noticeTableBooked()
    {
        $builder = $this->db->table('task_lead_booked');
        $builder;
        return $builder;
    }
    public function noticeTableBookedWhere($employee_id)
    {
        $builder = $this->db->table('task_lead_booked');
        $builder->where('employee_id', $employee_id);
        return $builder;
    }

    public function buttons($permissions)
    {
        $id = 'id';
        $closureFun = function($row) use($id, $permissions) {
            if (is_admin()) {
                return <<<EOF
                    <button class="btn btn-sm btn-success" onclick="edit({$row["id"]})"  data-toggle="modal" data-target="#modal_tasklead" title="Update Tasklead"><i class="fas fa-arrow-up"></i> </button> 

                    <button class="btn btn-sm btn-danger" onclick="remove({$row["id"]})" title="Delete"><i class="fas fa-trash"></i></button> 
                EOF;
            }

            $edit = '<button class="btn btn-sm btn-warning" title="Cannot edit" disabled><i class="fas fa-edit"></i> </button>';

            if (check_permissions($permissions, 'EDIT') && !is_admin()) {
                $edit = <<<EOF
                    <button class="btn btn-sm btn-warning" onclick="edit({$row["$id"]})" data-toggle="modal" data-target="#modal_tasklead" title="Update Tasklead"><i class="fas fa-edit"></i> </button> 
                EOF;
            }

            $delete = '<button class="btn btn-sm btn-danger" title="Cannot delete" disabled><i class="fas fa-trash"></i> </button>';

            if (check_permissions($permissions, 'DELETE') && !is_admin()) {
                $delete = <<<EOF
                    <button class="btn btn-sm btn-danger" onclick="remove({$row["$id"]})" title="Delete"><i class="fas fa-trash"></i></button>  
                EOF;
            }

            return $edit . $delete;
                        
        };

        return $closureFun;
    }
}
