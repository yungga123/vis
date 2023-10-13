<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\InventoryTrait;

class RequestPurchaseFormModel extends Model
{
    /* Declare trait here to use */
    use InventoryTrait;

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
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = ['updateInventoryStock'];
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
            if ($data['data']['status'] === 'received') {
                $rpfItemModel   = new RPFItemModel();
                $columns        = "
                    {$rpfItemModel->table}.inventory_id, 
                    {$rpfItemModel->table}.quantity_in,
                    {$rpfItemModel->table}.received_date,
                    inventory.stocks
                ";
                log_message('error', 'data => '. json_encode($data));
                $record         = $rpfItemModel->getRpfItemsByPrfId($data['id'], true, false, $columns);
                $action         = 'ITEM_IN';

                if (! empty($record)) {
                    $logs_data = [];
                    foreach ($record as $val) {
                        $this->traitUpdateInventoryStock($val['inventory_id'], $val['quantity_in'], $action);
                        $logs_data[] = [
                            'inventory_id'  => $val['inventory_id'],
                            'stocks'        => $val['quantity_in'],
                            'parent_stocks' => $val['stocks'],
                            'status_date'   => $val['received_date'],
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
            {$this->table}.status,
            {$this->table}.date_needed
        ";

        if ($date_format) {
            $columns .= ",
                DATE_FORMAT({$this->table}.date_needed, '%b %e, %Y') AS date_needed_formatted,
                DATE_FORMAT({$this->table}.created_at, '%b %e, %Y at %h:%i %p') AS created_at_formatted,
                DATE_FORMAT({$this->table}.accepted_at, '%b %e, %Y at %h:%i %p') AS accepted_at_formatted,
                DATE_FORMAT({$this->table}.rejected_at, '%b %e, %Y at %h:%i %p') AS rejected_at_formatted,
                DATE_FORMAT({$this->table}.reviewed_at, '%b %e, %Y at %h:%i %p') AS reviewed_at_formatted,
                DATE_FORMAT({$this->table}.received_at, '%b %e, %Y at %h:%i %p') AS received_at_formatted
            ";
        }

        if ($joinView) {
            $columns .= ",
                {$this->view}.created_by_name,
                {$this->view}.accepted_by_name,
                {$this->view}.rejected_by_name,
                {$this->view}.reviewed_by_name,
                {$this->view}.received_by_name
            ";
        }

        return $columns;
    }

    // Join with rpf_view
    public function joinView($builder)
    {
        $builder->join($this->view, "{$this->table}.id = {$this->view}.rpf_id");
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

            if (check_permissions($permissions, 'REVIEW') && $row['status'] === 'accepted') {
                // Review PRF
                $changeTo = 'review';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-info',
                    'icon'      => 'fas fa-check-double',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'RECIEVE') && $row['status'] === 'reviewed') {
                // Receive PRF
                $changeTo = 'receive';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-sign-in-alt',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'PRINT') && in_array($row['status'], ['reviewed', 'received'])) {
                // Print PRF
                $print_url = site_url('rpf/print/') . $row[$id];
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-dark btn-sm" target="_blank" title="Print {$title}"><i class="fas fa-print"></i></a>
                EOF;
            }

            $buttons = (in_array($row['status'], ['rejected', 'reviewed', 'received']) && !is_admin()) 
                ? ($buttonView ?? '~~N/A~~') : dt_buttons_dropdown($buttons);
                
            return $buttons;
        };
        
        return $closureFun;
   }

   // DataTable status formatter
   public function dtViewRpfItems()
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
   public function dtRpfStatusFormat()
   {
       $closureFun = function($row) {
           $text    = ucwords(set_rpf_status($row['status']));
           $color   = dt_status_color($row['status']);
           return text_badge($color, $text);
       };
       
       return $closureFun;
   }
}
