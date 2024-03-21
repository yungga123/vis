<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Events\Events;
use App\Traits\InventoryTrait;
use App\Traits\FilterParamTrait;
use App\Traits\HRTrait;

class ProjectRequestFormModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait, FilterParamTrait, HRTrait;

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
    protected $afterInsert    = ['mailNotif'];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses   = ['rejected', 'item_out', 'received', 'filed'];
    // Get inserted ID
    protected $insertedID           = 0;

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

    // Mail notif after record created
    protected function mailNotif(array $data)
    {
        $id                 = $data['id'];
        $this->insertedID   = $id;

        if ($data['result']) {
            $columns    = "
                {$this->table}.id,
                {$this->table}.job_order_id,
                {$this->table}.status,
                {$this->table}.process_date,
                {$this->table}.created_at,
                cb.employee_name AS created_by
            ";
            $builder    = $this->select($columns);
            $builder->where("{$this->table}.id", $id);
            $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');

            $record     = $builder->first();

            // Send mail notification
            Events::trigger('send_mail_notif_prf', $record);
        }
        
        return $data;
    }

    // Check if record exist
    public function exists($id)
    {
        $builder = $this->select('id');
        $builder->where('deleted_at', null);

        return !empty($builder->find($id));
    }

    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
        
    }

    // Update inventory stock after item out
    public function updateInventoryStock($prf_id, $status)
    {
        $prfItemModel   = new PRFItemModel();
        $columns        = "
            {$prfItemModel->table}.inventory_id, 
            {$prfItemModel->table}.quantity_out,
            {$prfItemModel->table}.returned_q,
            inventory.stocks
        ";
        $record         = $prfItemModel->getPrfItemsByPrfId($prf_id, $columns, false, true);
        $action         = strtoupper($status);
        $item_out       = $action === 'ITEM_OUT';

        if (! empty($record)) {
            $logs_data = [];
            foreach ($record as $val) {
                $quantity = $val['quantity_out'];
                $return_q = $val['returned_q'] ?? 0;

                if (! empty($val['returned_q']) && $return_q > 0) {
                    $quantity       = $quantity - $return_q;
                    $action         = 'ITEM_IN';
                    $logs_data[]    = [
                        'inventory_id'  => $val['inventory_id'],
                        'stocks'        => $return_q,
                        'parent_stocks' => $val['stocks'],
                        'action'        => $action,
                        'status'        => 'return',
                        'status_date'   => current_date(),
                        'created_by'    => session('username'),
                    ];

                    $this->traitUpdateInventoryStock($val['inventory_id'], $return_q, $action);
                }

                if ($item_out && $val['quantity_out'] > $return_q) {
                    $action         = 'ITEM_OUT';
                    $logs_data[]    = [
                        'inventory_id'  => $val['inventory_id'],
                        'stocks'        => $quantity,
                        'parent_stocks' => $val['stocks'],
                        'action'        => $action,
                        'status'        => '',
                        'created_by'    => session('username'),
                    ];
    
                    $this->traitUpdateInventoryStock($val['inventory_id'], $quantity, $action);
                }
            }

            // Add inventory logs
            $this->saveInventoryLogs($logs_data);
        }
    }

    // Set columns depending on arguments
    public function columns($date_format = false, $joinView = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.job_order_id,
            {$this->table}.process_date,
            {$this->table}.status,
            {$this->table}.remarks,
            {$this->table}.created_at
        ";

        if ($date_format) {
            $columns .= ",
                DATE_FORMAT({$this->table}.process_date, '%b %e, %Y') AS process_date_formatted,
                DATE_FORMAT({$this->table}.created_at, '%b %e, %Y at %h:%i %p') AS created_at_formatted,
                DATE_FORMAT({$this->table}.accepted_at, '%b %e, %Y at %h:%i %p') AS accepted_at_formatted,
                DATE_FORMAT({$this->table}.rejected_at, '%b %e, %Y at %h:%i %p') AS rejected_at_formatted,
                DATE_FORMAT({$this->table}.item_out_at, '%b %e, %Y at %h:%i %p') AS item_out_at_formatted,
                DATE_FORMAT({$this->table}.received_at, '%b %e, %Y at %h:%i %p') AS received_at_formatted,
                DATE_FORMAT({$this->table}.filed_at, '%b %e, %Y at %h:%i %p') AS filed_at_formatted
            ";
        }

        if ($joinView) {
            $columns .= ",
                {$this->view}.created_by_name,
                {$this->view}.accepted_by_name,
                {$this->view}.rejected_by_name,
                {$this->view}.item_out_by_name,
                {$this->view}.received_by_name,
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
        $columns = $joModel->selectedColumns($with_text, $with_date) .",
            ".dt_sql_date_format("{$joModel->table}.date_requested")." AS date_requested_formatted,
            ".dt_sql_date_format("{$joModel->table}.date_committed")." AS date_committed_formatted
        ";
        return $columns;
    }

    // Join with prf_view
    public function joinView($builder)
    {
        $builder->join($this->view, "{$this->table}.id = {$this->view}.prf_id");
        return $this;
    }

    // Join with job_orders
    public function joinJobOrder($builder)
    {
        $joModel = new JobOrderModel();
        // Join with job_orders table
        $builder->join($joModel->table, "{$this->table}.job_order_id = {$joModel->table}.id", 'left');
        // Then join job_orders with task_lead_booked view and employees table
        $joModel->joinWithOtherTables($builder);
        return $this;
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

        $this->filterParam($request, $builder, "{$this->table}.status");
        
        $start_date = $request['params']['start_date'] ?? '';
        $end_date   = $request['params']['end_date'] ?? '';

        if (! empty($start_date) && ! empty($end_date)) {
            $start_date = format_date($start_date, 'Y-m-d');
            $end_date   = format_date($end_date, 'Y-m-d');
            $between    = "{$this->table}.process_date BETWEEN '%s' AND '%s'";

            $builder->where(sprintf($between, $start_date, $end_date));
        }

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

            if (check_permissions($permissions, 'RECEIVE') && $row['status'] === 'item_out') {
                // Item Out PRF
                $changeTo = 'receive';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-primary',
                    'icon'      => 'fas fa-calendar-check',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'FILE') && $row['status'] === 'received') {
                // File PRF
                $changeTo = 'file';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-dark',
                    'icon'      => 'fas fa-archive',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            $allowedToPrint = ['accepted', 'item_out', 'received', 'filed'];
            if (
                check_permissions($permissions, 'PRINT') && 
                in_array($row['status'], $allowedToPrint)
            ) {
                // Print PRF
                $print_url  = url_to('inventory.prf.print', $row[$id]);
                $buttons    .= <<<EOF
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
            $changeTo   = '';
            $status     = $row['status'];

            switch ($status) {
                case 'pending':
                    $changeTo = 'accept';
                    break;
                case 'accepted':
                    $changeTo = 'item_out';
                    break;
                case 'item_out':
                    $changeTo = 'receive';
                    break;
                case 'received':
                    $changeTo = 'file';
                    break;
            }

            return <<<EOF
                <button class="btn btn-sm btn-primary" onclick="view({$row[$id]}, '{$changeTo}', '{$status}', true)"><i class="fas fa-eye"></i> View</button>
            EOF;
       };
       
       return $closureFun;
   }

   // DataTable status formatter
   public function dtPRFStatusFormat()
   {
       $closureFun = function($row) {
           $text    = $row['status'] === 'item_out' ? 'Item Out' : ucwords(set_prf_status($row['status']));
           $color   = $row['status'] === 'received' ? 'info' : dt_status_color($row['status']);
           
           return text_badge($color, $text);
       };
       
       return $closureFun;
   }
}
