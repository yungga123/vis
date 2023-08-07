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
    protected $tableJobOrder    = 'job_orders';
    protected $tableInventory   = 'inventory';
    protected $viewInventory    = 'inventory_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'job_order_id',
        'inventory_id',
        'quantity_out',
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
            'rules' => 'required',
            'label' => 'job order'
        ],
        'inventory_id' => [
            'rules' => 'required',
            'label' => 'masterlist item'
        ],
        'quantity_out' => [
            'rules' => 'required|numeric',
            'label' => 'quantity out'
        ],
        'process_date' => [
            'rules' => 'required',
            'label' => 'process date'
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
    protected function updateInventoryStock(array $data)
    {
        if ($data['result'] && isset($data['data']['status'])) {
            if ($data['data']['status'] === 'item_out') {
                $record = $this->getProjectRequestForms($data['id'], false, 'inventory_id, quantity_out');
    
                $this->traitUpdateInventoryStock(
                    $record[0]['inventory_id'], 
                    doubleval($record[0]['quantity_out']),
                    'ITEM_OUT'
                );
            }
        }

        return $data;
    }

    // Set columns depending on arguments
    protected function columns($date_format = false, $joinInventory = false, $joinView = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.job_order_id,
            {$this->table}.inventory_id,
            {$this->table}.quantity_out,
            {$this->table}.process_date,
            {$this->table}.status
        ";

        if ($date_format) {
            $columns .= ",
                DATE_FORMAT({$this->table}.process_date, '%b %e, %Y at %h:%i %p') AS process_date_formatted,
                DATE_FORMAT({$this->table}.created_at, '%b %e, %Y at %h:%i %p') AS created_at_formatted,
                DATE_FORMAT({$this->table}.accepted_at, '%b %e, %Y at %h:%i %p') AS accepted_at_formatted,
                DATE_FORMAT({$this->table}.rejected_at, '%b %e, %Y at %h:%i %p') AS rejected_at_formatted,
                DATE_FORMAT({$this->table}.item_out_at, '%b %e, %Y at %h:%i %p') AS item_out_at_formatted
            ";
        }

        if ($joinInventory) {
            $columns .= ",
                {$this->tableInventory}.item_model,
                {$this->tableInventory}.item_description,
                {$this->tableInventory}.stocks,
                {$this->viewInventory}.category_name,
                {$this->viewInventory}.subcategory_name,
                {$this->viewInventory}.brand
            ";
        }

        if ($joinView) {
            $columns .= ",
                {$this->view}.created_by_name,
                {$this->view}.accepted_by_name,
                {$this->view}.rejected_by_name,
                {$this->view}.item_out_by_name
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

    // Join with inventory table and inventory_view
    protected function joinInventory($builder)
    {
        $builder->join($this->tableInventory, "{$this->table}.inventory_id = {$this->tableInventory}.id", 'left');
        $builder->join($this->viewInventory, "{$this->table}.inventory_id = {$this->viewInventory}.inventory_id", 'left');
    }

    // Join with prf_view
    protected function joinJobOrder($builder)
    {
        $joModel = new JobOrderModel();
        // Join with job_orders table
        $builder->join($this->tableJobOrder, "{$this->table}.job_order_id = {$this->tableJobOrder}.id", 'left');
        // Then join job_orders with task_lead_booked view and employees table
        $joModel->_join($builder);
    }

    // Get project request forms
    public function getProjectRequestForms($id = null, $join = false, $columns = '')
    {
        $columns = $columns ? $columns : $this->columns($join);
        $builder = $this->select($columns);

        if ($join) $this->joinInventory($builder);
        $builder->where("{$this->table}.deleted_at", null);

        return $id ? $builder->find($id) : $builder->findAll();
    }
    
    // For DataTables
   public function noticeTable($request) 
   {
       $builder = $this->db->table($this->table);
       $columns = $this->columns(true, true, true);
       // Include JO columns
       $columns .= ','. $this->jobOrderColumns();

       $builder->select($columns);
       $this->joinInventory($builder);
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
            $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

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
                    'condition' => $this->_statusDTOnchange($row[$id], $changeTo, $row['status'], $row['stocks'], $row['quantity_out']),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'PRINT') && $row['status'] === 'item_out') {
                // Print PRF
                $print_url = site_url('prf/print/') . $row[$id];
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                EOF;
            }

            $buttons = ($row['status'] === 'rejected' && !is_admin()) 
                ? '~~N/A~~' : dt_buttons_dropdown($buttons);
                
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
   private function _statusDTOnchange($id, $changeTo, $status, $stocks = '', $q_out = '')
   {
       $title  = ucwords($changeTo);
       $title  = $changeTo === 'item_out' ? 'Item Out' : $title;
       return <<<EOF
           onclick="change({$id}, '{$changeTo}', '{$status}', '{$stocks}', '{$q_out}')" title="{$title} PRF"
       EOF;
   }
}
