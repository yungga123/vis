<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\InventoryTrait;

class ProjectRequestFormModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'project_request_forms';
    protected $view             = 'prf_view';
    protected $jobOrderTable    = 'job_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'job_order_id',
        'process_date',
        'status',
        'created_by',
        'remarks',
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
            'rules' => 'required|if_exist',
            'label' => 'job order'
        ],
        'inventory_id' => [
            'rules' => 'required|if_exist',
            'label' => 'item'
        ],
        'quantity_out' => [
            'rules' => 'required|if_exist',
            'label' => 'quantity out'
        ],
        'process_date' => [
            'rules' => 'required|if_exist',
            'label' => 'process date'
        ],
        'remarks' => [
            'rules' => 'required|if_exist'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedBy'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = ['updateInventoryStock'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses = ['rejected', 'item_out', 'filed'];

    // Set the value for created_by before inserting
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Set the value for 'status_' by and at before updating status
    protected function setStatusByAndAt(array $data)
    {
        if (isset($data['data']['status'])) {
            $status = $data['data']['status'];
            $data['data'][$status .'_by'] = session('username');
            $data['data'][$status .'_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
        
    }

    // Update inventory stock after item out
    public function updateInventoryStock(array $data)
    {
        if ($data['result'] && isset($data['data']['status'])) {
            if ($data['data']['status'] === 'item_out') {
                $prfItemModel   = new PRFItemModel();
                $columns        = "
                    {$prfItemModel->table}.inventory_id, 
                    {$prfItemModel->table}.quantity_out,
                    inventory.stocks
                ";
                $record         = $prfItemModel->getPrfItemsByPrfId($data['id'], $columns, false, true);
                $action         = 'ITEM_OUT';

                if (! empty($record)) {
                    $logs_data = [];
                    foreach ($record as $val) {
                        $this->traitUpdateInventoryStock($val['inventory_id'], $val['quantity_out'], $action);
                        $logs_data[] = [
                            'inventory_id'  => $val['inventory_id'],
                            'stocks'        => $val['quantity_out'],
                            'parent_stocks' => $val['stocks'],
                            'action'        => $action,
                            'created_by'    => session('username'),
                        ];
                    }

                    // Add inventory logs
                    $this->saveInventoryLogs($logs_data);
                }
            }
        }

        return $data;
    }

    // Set columns depending on arguments
    public function columns($date_format = false, $joinView = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.job_order_id,
            {$this->table}.process_date,
            {$this->table}.status,
            {$this->table}.remarks
        ";

        if ($date_format) {
            $columns .= ",
                DATE_FORMAT({$this->table}.process_date, '%b %e, %Y') AS process_date_formatted,
                DATE_FORMAT({$this->table}.created_at, '%b %e, %Y at %h:%i %p') AS created_at_formatted,
                DATE_FORMAT({$this->table}.accepted_at, '%b %e, %Y at %h:%i %p') AS accepted_at_formatted,
                DATE_FORMAT({$this->table}.rejected_at, '%b %e, %Y at %h:%i %p') AS rejected_at_formatted,
                DATE_FORMAT({$this->table}.item_out_at, '%b %e, %Y at %h:%i %p') AS item_out_at_formatted,
                DATE_FORMAT({$this->table}.filed_at, '%b %e, %Y at %h:%i %p') AS filed_at_formatted
            ";
        }

        if ($joinView) {
            $columns .= ",
                {$this->view}.created_by_name,
                {$this->view}.accepted_by_name,
                {$this->view}.rejected_by_name,
                {$this->view}.item_out_by_name,
                {$this->view}.filed_by_name
            ";
        }

        return $columns;
    }

    // Get the Job Order selected columns
    public function jobOrderColumns($with_text = false, $with_date = false)
    {
        $joModel = new JobOrderModel();
        // Get the selected columns
        return $joModel->selectedColumns($with_text, $with_date);
    }

    // Join with prf_view
    public function joinView($builder)
    {
        $builder->join($this->view, "{$this->table}.id = {$this->view}.prf_id");
    }

    // Join with job_orders
    public function joinJobOrder($builder)
    {
        $joModel = new JobOrderModel();
        // Join with job_orders table
        $builder->join($joModel->table, "{$this->table}.job_order_id = {$joModel->table}.id", 'left');
        // Then join job_orders with task_lead_booked view and employees table
        $joModel->_join($builder);
    }

    // Get project request forms
    public function getProjectRequestForms($id = null, $joinView = false, $columns = '')
    {
        $columns = $columns ? $columns : $this->columns(true, $joinView);
        $builder = $this->select($columns);

        if ($joinView) $this->joinView($builder);
        $builder->where("{$this->table}.deleted_at", null);

        return $id ? $builder->find($id) : $builder->findAll();
    }
    
    // For DataTables
   public function noticeTable($request) 
   {
        $builder = $this->db->table($this->table);
        $columns = $this->columns(true, true);
        // Include JO columns
        $columns .= ','. $this->jobOrderColumns();

        $builder->select($columns);
        $this->joinView($builder);
        $this->joinJobOrder($builder);

        $builder->where("{$this->table}.deleted_at", null);
        $builder->orderBy('id', 'DESC');

        return $builder;
   }

   // DataTable action buttons
   public function buttons($permissions)
   {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $title      = 'PRF';
        $closureFun = function($row) use($id, $permissions, $dropdown, $title) {
            $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

            if ($row['status'] === 'pending') {
                if (check_permissions($permissions, 'ACCEPT')) {
                    // Accept PRF
                    $changeTo = 'accept';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-primary',
                        'icon'      => 'fas fa-check-circle',
                        'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                    ], $dropdown);
                }

                if (check_permissions($permissions, 'REJECT')) {
                    // Reject PRF
                    $changeTo = 'reject';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-secondary',
                        'icon'      => 'fas fa-times-circle',
                        'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                    ], $dropdown);
                }
            }

            if (check_permissions($permissions, 'ITEM_OUT') && $row['status'] === 'accepted') {
                // Item Out PRF
                $changeTo = 'item_out';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? 'Item Out' : '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-file-import',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'FILE') && $row['status'] === 'item_out') {
                // File PRF
                $changeTo = 'file';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-primary',
                    'icon'      => 'fas fa-file-alt',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'PRINT') && in_array($row['status'], ['item_out', 'filed'])) {
                // Print PRF
                $print_url = site_url('prf/print/') . $row[$id];
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-info btn-sm" target="_blank" title="Print {$title}"><i class="fas fa-print"></i></a>
                EOF;
            }
                
            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
   }

   // DataTable status formatter
   public function dtViewPrfItems()
   {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id) {
            return <<<EOF
                <button class="btn btn-sm btn-primary" onclick="view({$row[$id]})"><i class="fas fa-eye"></i> View</button>
            EOF;
       };
       
       return $closureFun;
   }

   // DataTable status formatter
   public function dtPRFStatusFormat()
   {
       $closureFun = function($row) {
           $text    = $row['status'] === 'item_out' ? 'Item Out' : ucwords(set_prf_status($row['status']));
           $color   = dt_status_color($row['status']);
           return text_badge($color, $text);
       };
       
       return $closureFun;
   }
}
