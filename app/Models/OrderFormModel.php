<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;
use App\Traits\InventoryTrait;

class OrderFormModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait, InventoryTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'order_forms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'customer_id',
        'customer_branch_id',
        'payment_method',
        'purchase_at',
        'total_amount',
        'total_discount',
        'with_vat',
        'vat_amount',
        'grand_total',
        'remarks',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'status' => [
            'rules' => 'permit_empty',
            'label' => 'status'
        ],
        'customer_id' => [
            'rules' => 'required|if_exist',
            'label' => 'client'
        ],
        'customer_branch_id' => [
            'rules' => 'permit_empty',
            'label' => 'client branch'
        ],
        'payment_method' => [
            'rules' => 'permit_empty|if_exist',
            'label' => 'payment method'
        ],
        'purchase_date' => [
            'rules' => 'required|if_exist',
            'label' => 'purchase date'
        ],
        'purchase_time' => [
            'rules' => 'required|if_exist',
            'label' => 'purchase time'
        ],
        'total_amount' => [
            'rules' => 'required|if_exist',
            'label' => 'total amount'
        ],
        'with_vat' => [
            'rules' => 'permit_empty',
            'label' => 'with vat'
        ],
        'vat_amount' => [
            'rules' => 'permit_empty',
            'label' => 'vat amount'
        ],
        'grand_total' => [
            'rules' => 'required|if_exist',
            'label' => 'grand total'
        ],
        'remarks' => [
            'rules' => 'permit_empty'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses   = ['rejected', 'item_out', 'received', 'filed'];

    /**
     * Set the value for created_by before inserting
     */
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');

        return $data;
    }

    /**
     * Set the value for 'status_' by and at before updating status
     */
    protected function setStatusByAndAt(array $data)
    {
        if (isset($data['data']['status'])) {
            $status = $data['data']['status'];

            $data['data'][$status .'_by'] = session('username');
            $data['data'][$status .'_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    /**
     * Count records
     */
    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
        
    }

    /**
     * Check if record exist
     */
    public function exists($id)
    {
        $builder = $this->select('id');
        $builder->where('deleted_at', null);

        return !empty($builder->find($id));
    }

    /**
     * Fetch records
     * 
     * @param string|int|array $id
     */
    public function fetch($id, $joinCustomers = false): array|null
    {
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $columns        = array_merge([$this->primaryKey], $this->allowedFields);

        if ($joinCustomers) {
            $columns    = array_map(function ($column) {  return "{$this->table}.{$column}"; }, $columns);
            $columns    = implode(',', $columns);
            $columns    .= ",
                {$customerModel->table}.name AS customer_name,
                {$customerModel->table}.type AS customer_type,
                {$branchModel->table}.branch_name AS customer_branch_name,
            ";
        }

        $builder        = $this->select($columns);

        if ($joinCustomers) $this->joinCustomers($builder, $customerModel, '', true);

        $builder->where("{$this->table}.deleted_at IS NULL");

        if (is_array($id)) {
            $builder->whereIn("{$this->table}.id", $id);
            
            return $this->findAll();
        }

        $builder->where("{$this->table}.id", $id);

        return $builder->first();
    }

    /**
     * Join with customers
     */
    public function joinCustomers($builder, $model = null, $type = 'left', $branch = false)
    {      
        $model ??= new CustomerModel();
        $table  = $builder->getTable();
        $table  = in_array($table, [$this->table]) ? $table : $this->table;
        $type   = empty($type) ? 'left' : $type;

        $builder->join($model->table, "{$table}.customer_id = {$model->table}.id", $type);

        if ($branch) {
            $branchModel = new CustomerBranchModel();

            $builder->join($branchModel->table, "({$table}.customer_branch_id = {$branchModel->table}.id AND {$table}.customer_branch_id IS NOT NULL)", 'left');
        }

        return $this;
    }

    /**
     * Update inventory stock after item out
     */
    public function updateInventoryStock($id)
    {
        $itemModel  = new OrderFormItemModel();
        $invModel   = new InventoryModel();
        $columns    = "
            {$itemModel->table}.inventory_id,
            {$itemModel->table}.quantity,
            {$invModel->table}.stocks
        ";
        $record     = $itemModel->getItems($id, true, $columns);
        $action     = 'ITEM_OUT';

        if (! empty($record)) {
            $logs_data = [];

            foreach ($record as $val) {
                $quantity = $val['quantity'];

                if (! empty($quantity)) {
                    $logs_data[]    = [
                        'inventory_id'  => $val['inventory_id'],
                        'stocks'        => $quantity,
                        'parent_stocks' => $val['stocks'],
                        'action'        => $action,
                        'status'        => 'SOLD',
                        'created_by'    => session('username'),
                    ];
    
                    $this->traitUpdateInventoryStock($val['inventory_id'], $quantity, $action);
                }
            }

            // Add inventory logs
            $this->saveInventoryLogs($logs_data);
        }
    }

    /**
     * For DataTable
     */
    public function noticeTable(array $request): object
    {
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $columns        = "
            {$this->table}.id,
            {$this->table}.status,
            {$customerModel->table}.name AS client_name,
            {$branchModel->table}.branch_name AS client_branch_name,
            ".dt_sql_datetime_format("{$this->table}.purchase_at")." AS purchase_at,
            ".dt_sql_number_format("{$this->table}.total_amount")." AS total_amount,
            ".dt_sql_number_format("{$this->table}.total_discount")." AS total_discount,
            IF({$this->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            ".dt_sql_number_format("{$this->table}.vat_amount")." AS vat_amount,
            ".dt_sql_number_format("{$this->table}.grand_total")." AS grand_total,
            {$this->table}.remarks,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at,
            ab.employee_name AS accepted_by,
            ".dt_sql_datetime_format("{$this->table}.accepted_at")." AS accepted_at,
            rb.employee_name AS rejected_by,
            ".dt_sql_datetime_format("{$this->table}.rejected_at")." AS rejected_at,
            ib.employee_name AS item_out_by,
            ".dt_sql_datetime_format("{$this->table}.item_out_at")." AS item_out_at,
            fb.employee_name AS filed_by,
            ".dt_sql_datetime_format("{$this->table}.filed_at")." AS filed_at,
        ";
        $builder    = $this->db->table($this->table);

        $builder->select($columns);

        $this->joinCustomers($builder, $customerModel, '', true);
        $this->joinAccountView($builder, 'created_by', 'cb');
        $this->joinAccountView($builder, 'accepted_by', 'ab');
        $this->joinAccountView($builder, 'rejected_by', 'rb');
        $this->joinAccountView($builder, 'item_out_by', 'ib');
        $this->joinAccountView($builder, 'filed_by', 'fb');
        
        $this->filterParam($request, $builder);
        $this->filterParam($request, $builder, 'with_vat', 'with_vat');
        
        $start_date = $request['params']['start_date'] ?? '';
        $end_date   = $request['params']['end_date'] ?? '';

        if (! empty($start_date) && ! empty($end_date)) {
            $start_date = format_date($start_date, 'Y-m-d');
            $end_date   = format_date($end_date, 'Y-m-d');
            $between    = "{$this->table}.purchase_at BETWEEN '%s' AND '%s'";

            $builder->where(sprintf($between, $start_date, $end_date));
        }

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    /**
     * DataTable action buttons
     */
    public function buttons(array $permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $title      = 'Order Form';
        $closureFun = function($row) use($id, $permissions, $dropdown, $title) {
            $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

            if ($row['status'] === 'pending') {
                if (check_permissions($permissions, 'ACCEPT')) {
                    // Accept Order Form
                    $changeTo = 'accept';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-primary',
                        'icon'      => 'fas fa-check-circle',
                        'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                    ], $dropdown);
                }

                if (check_permissions($permissions, 'REJECT')) {
                    // Reject Order Form
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
                // Item Out Order Form
                $changeTo = 'item_out';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? 'Item Out' : '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-file-import',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'RECEIVE') && $row['status'] === 'item_out') {
                // Item Out Order Form
                $changeTo = 'receive';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-primary',
                    'icon'      => 'fas fa-calendar-check',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'FILE') && $row['status'] === 'received') {
                // File Order Form
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
                $print_url = url_to('inventory.order_form.print', $row[$id]);
                $buttons .= <<<EOF
                    <a href="$print_url" class="btn btn-dark btn-sm" target="_blank"><i class="fas fa-print"></i></a>
                EOF;
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
    }

    /**
     * DataTable view order form items
     */
    public function dtViewOrderFormItems()
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
                <button class="btn btn-sm btn-primary" onclick="view({$row[$id]}, '{$changeTo}', '{$status}')"><i class="fas fa-eye"></i> View</button>
            EOF;
        };
        
        return $closureFun;
    }

    /**
     * DataTable status formatter
     */
    public function dtOrderFormStatusFormat()
    {
        $closureFun = function($row) {
            $text    = $row['status'] === 'item_out' ? 'Item Out' : ucwords(set_prf_status($row['status']));
            $color   = $row['status'] === 'received' ? 'info' : dt_status_color($row['status']);
            
            return text_badge($color, $text);
        };
        
        return $closureFun;
    }
}
