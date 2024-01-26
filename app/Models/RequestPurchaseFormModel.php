<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Events\Events;
use App\Traits\InventoryTrait;
use App\Traits\FilterParamTrait;
use App\Traits\HRTrait;

class RequestPurchaseFormModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait, FilterParamTrait, HRTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'request_purchase_forms';
    protected $view             = 'rpf_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'remarks',
        'date_needed',
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
        'inventory_id' => [
            'rules' => 'required|if_exist',
            'label' => 'item'
        ],
        'supplier_id' => [
            'rules' => 'required|if_exist',
            'label' => 'supplier'
        ],
        'quantity_in' => [
            'rules' => 'required|if_exist',
            'label' => 'quantity in'
        ],
        'received_date' => [
            'rules' => 'permit_empty|if_exist',
            'label' => 'received date'
        ],
        'delivery_date' => [
            'rules' => 'required|if_exist',
            'label' => 'delivery date'
        ],
        'remarks' => [
            'rules' => 'permit_empty|if_exist'
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
    protected $restrictedStatuses   = ['rejected', 'reviewed'];
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
            $data['data'][$status .'_at'] = current_datetime();
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
                {$this->table}.status,
                {$this->table}.date_needed,
                {$this->table}.created_at,
                cb.employee_name AS created_by
            ";
            $builder    = $this->select($columns);
            $builder->where("{$this->table}.id", $id);
            $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');

            $record     = $builder->first();

            // Send mail notification
            Events::trigger('send_mail_notif_rpf', $record);
        }
        
        return $data;
    }

    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
        
    }

    // Set columns depending on arguments
    public function columns($date_format = false, $joinView = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.status,
            {$this->table}.date_needed,
            {$this->table}.created_at
        ";

        if ($date_format) {
            $columns .= ",
                ".dt_sql_date_format("{$this->table}.date_needed") ." AS date_needed_formatted,
                ".dt_sql_datetime_format("{$this->table}.created_at") ." AS created_at_formatted,
                ".dt_sql_datetime_format("{$this->table}.accepted_at") ." AS accepted_at_formatted,
                ".dt_sql_datetime_format("{$this->table}.rejected_at") ." AS rejected_at_formatted,
                ".dt_sql_datetime_format("{$this->table}.reviewed_at") ." AS reviewed_at_formatted
            ";
        }

        if ($joinView) {
            $columns .= ",
                {$this->view}.created_by_name,
                {$this->view}.accepted_by_name,
                {$this->view}.rejected_by_name,
                {$this->view}.reviewed_by_name
            ";
        }

        return $columns;
    }

    // Join with rpf_view
    public function joinView($builder = null)
    {
        $builder = $builder ? $builder : $this;
        $builder->join($this->view, "{$this->table}.id = {$this->view}.rpf_id");
        return $this;
    }

    // Check if record exist
    public function exists($id)
    {
        $builder = $this->select('id');
        $builder->where('deleted_at', null);

        return !empty($builder->find($id));
    }

    // Get project request forms
    public function getRequestPurchaseForms($id = null, $joinView = false, $columns = '')
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

       $builder->select($columns);
       $this->joinView($builder);

       $this->filterParam($request, $builder, "{$this->table}.status");

       $builder->where("{$this->table}.deleted_at", null);
       $builder->orderBy("{$this->table}.id", 'DESC');

       return $builder;
   }

   // DataTable action buttons
   public function buttons($permissions)
   {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $title      = 'RPF';
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

            if (check_permissions($permissions, 'REVIEW') && $row['status'] === 'accepted') {
                // Review PRF
                $changeTo = 'review';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-check-double',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'PRINT') && in_array($row['status'], ['reviewed'])) {
                // Print PRF
                $print_url  = site_url('rpf/print/') . $row[$id];
                $buttons    .= <<<EOF
                    <a href="$print_url" class="btn btn-dark btn-sm" target="_blank" title="Print {$title}"><i class="fas fa-print"></i></a>
                EOF;
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
   }

   // DataTable status formatter
   public function dtViewRpfItems()
   {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id) {
            $status     = $row['status'];
            $changeTo   = '';

            switch ($status) {
                case 'pending':
                    $changeTo = 'accept';
                    break;
                case 'accepted':
                    $changeTo = 'review';
                    break;
            }

            return <<<EOF
                <button class="btn btn-sm btn-primary" onclick="view({$row[$id]}, '{$changeTo}', '{$row['status']}')"><i class="fas fa-eye"></i> View</button>
            EOF;
       };
       
       return $closureFun;
   }

   // DataTable status formatter
   public function dtRpfStatusFormat()
   {
       $closureFun = function($row) {
           $text    = ucwords($row['status']);
           $color   = dt_status_color($row['status']);
           return text_badge($color, $text);
       };
       
       return $closureFun;
   }
}
