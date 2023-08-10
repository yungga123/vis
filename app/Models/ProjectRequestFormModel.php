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
    protected function columns($date_format = false, $joinView = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.job_order_id,
            {$this->table}.process_date,
            {$this->table}.status
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
    protected function jobOrderColumns()
    {
        $joModel = new JobOrderModel();
        // Get the selected columns
        return $joModel->selectedColumns();
    }

    // Join with prf_view
    protected function joinView($builder)
    {
        $builder->join($this->view, "{$this->table}.id = {$this->view}.prf_id");
    }

    // Join with prf_view
    protected function joinJobOrder($builder)
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
        $closureFun = function($row) use($id, $permissions, $dropdown) {           
            $buttonView = '';
            if (check_permissions($permissions, 'VIEW')) {
                // View prf inventory items
                $buttonView = dt_button_html([
                    'text'      => $dropdown ? 'View' : '',
                    'button'    => 'btn-info',
                    'icon'      => 'fas fa-eye',
                    'condition' => 'onclick="view('.$row[$id].')" title="View PRF Items"',
                ], $dropdown);
            }

            $buttons = $buttonView ?? '';
            $buttons .= dt_button_actions($row, $id, $permissions, $dropdown);

            if ($row['status'] === 'pending') {
                if (check_permissions($permissions, 'ACCEPT')) {
                    // Accept PRF
                    $changeTo = 'accept';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-primary',
                        'icon'      => 'fas fa-check-circle',
                        'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
                    ], $dropdown);
                }

                if (check_permissions($permissions, 'REJECT')) {
                    // Discard PRF
                    $changeTo = 'reject';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-secondary',
                        'icon'      => 'fas fa-times-circle',
                        'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
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
                    'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'PRINT') && $row['status'] === 'item_out') {
                // File PRF
                $changeTo = 'file';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-primary',
                    'icon'      => 'fas fa-file-alt',
                    'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status']),
                ], $dropdown);

                // Print PRF
                $print_url = site_url('prf/print/') . $row[$id];
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                EOF;
            }

            $buttons = ($row['status'] === 'rejected' && !is_admin()) 
                ? ($buttonView ?? '~~N/A~~') : dt_buttons_dropdown($buttons);
                
            return $buttons;
        };
        
        return $closureFun;
   }

   // DataTable status formatter
   public function dtPRFStatusFormat()
   {
       $closureFun = function($row) {
           $text   = ucwords(set_prf_status($row['status']));
           $class  = 'rounded text-sm text-white pl-2 pr-2 pt-1 pb-1';

           switch ($row['status']) {
               case 'pending':
                   $format = '<span class="bg-warning '.$class.'">'.$text.'</span>';
                   break;
               case 'accepted':
                   $format = '<span class="bg-primary '.$class.'">'.$text.'</span>';
                   break;
               case 'rejected':
                   $format = '<span class="bg-secondary '.$class.'">'.$text.'</span>';
                   break;
               case 'item_out':
                   $format = '<span class="bg-success '.$class.'">Item Out</span>';
                   break;
               default:
                   $format = '<span class="bg-secondary '.$class.'">'.$text.'</span>';
                   break;
           }

           return $format;
       };
       
       return $closureFun;
   }

   // For status onchange event
   private function _statusDTOnchange($id, $changeTo, $status)
   {
       $title  = ucwords($changeTo);
       $title  = $changeTo === 'item_out' ? 'Item Out' : $title;
       return <<<EOF
           onclick="change({$id}, '{$changeTo}', '{$status}')" title="{$title} PRF"
       EOF;
   }
}
