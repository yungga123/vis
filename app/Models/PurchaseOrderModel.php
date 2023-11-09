<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\HRTrait;
use App\Traits\FilterParamTrait;

class PurchaseOrderModel extends Model
{
    /* Declare trait here to use */
    use HRTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rpf_id',
        'supplier_id',
        'attention_to',
        'requestor',
        'with_vat',
        'sub_total',
        'status',
    ];

    // Dates
    protected $useTimestamps = false;
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
    protected $beforeInsert   = ['setCreatedBy'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses = ['approved', 'filed'];

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

    // For counting records
    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('status', strtolower($param))->countAllResults();
        
    }

    // Get purchase orders
    public function getPurchaseOrders($id = null, $columns = '')
    {
        $columns = $columns ? $columns : [$this->primaryKey] + $this->allowedFields;
        $builder = $this->select($columns);
        $builder->where('deleted_at IS NULL');

        if ($id && is_array($id))
            return $builder->whereIn($this->primaryKey, $id)->findAll();

        return $id ? $builder->find($id) : $builder->findAll();
    }

    // Get purchase order items with joined tables
    public function getPOItems($id, $additionalColumns = '')
    {
        $poItemModel    = new POItemModel();
        $rpfItemModel   = new RPFItemModel();
        $inventoryModel = new InventoryModel();
        $supplierModel  = new SuppliersModel();
        $columns        = "
            {$this->table}.rpf_id,
            {$poItemModel->table}.inventory_id,
            {$rpfItemModel->table}.quantity_in,
        " . $rpfItemModel->inventoryColumns(true);
        $columns        = $additionalColumns ? $columns .', '. $additionalColumns : $columns;
        $builder        = $this->select($columns);

        // Joining tables
        $this->joinSupplier($builder, $supplierModel);
        $this->joinPOItem($builder, $poItemModel);
        $this->joinRpfItem($builder, $rpfItemModel);
        $poItemModel->joinInventory($builder, $inventoryModel);
        $poItemModel->joinInventory($builder, $inventoryModel, true);

        // Where
        $builder->where("{$this->table}.id", $id);
        $builder->where("{$poItemModel->table}.po_id", $id);

        return $builder->findAll();
    
    }

    // Join suppliers
    public function joinSupplier($builder, $model = null, $type = 'left')
    {      
        $model ?? $model = new SuppliersModel();
        $builder->join($model->table, "{$this->table}.supplier_id = {$model->table}.id", $type);
        return $this;
    }

    // Join request_purchase_forms / rpf_view
    public function joinRpf($builder, $model = null, $view = false, $type = 'left')
    {
        $model ?? $model = new RequestPurchaseFormModel();
        $joinTo = $model->table;
        $id     = 'id';
        if ($view) {
            $joinTo = $model->view;
            $id     = 'rpf_id';
        }
        $builder->join($joinTo, "{$this->table}.rpf_id = {$joinTo}.{$id}", $type);
        return $this;
    }

    // Join rpf_items
    public function joinRpfItem($builder, $model = null, $type = 'left')
    {   
        $model ?? $model = new RPFItemModel();
        $poItemModel = new POItemModel();
        $builder->join($model->table, "
            (
                {$this->table}.rpf_id = {$model->table}.rpf_id AND
                {$poItemModel->table}.inventory_id = {$model->table}.inventory_id
            )
        ", $type);
        return $this;
    }

    // Join purchase_order_items
    public function joinPOItem($builder, $model = null, $type = 'left')
    {   
        $model ?? $model = new POItemModel();
        $builder->join($model->table, "{$this->table}.id = {$model->table}.po_id", $type);
        return $this;
    }
    
    // For DataTables
   public function noticeTable($request) 
   {
        $datetime_format = dt_sql_datetime_format();
        $supplierModel  = new SuppliersModel();
        $rpfModel       = new RequestPurchaseFormModel();
        $builder        = $this->db->table($this->table);
        $columns        = "
            {$this->table}.id,
            {$this->table}.rpf_id,
            {$this->table}.supplier_id,
            {$this->table}.attention_to,
            {$this->table}.status,
            {$supplierModel->table}.supplier_name,
            {$rpfModel->view}.created_by_name AS requested_by,
            DATE_FORMAT({$rpfModel->table}.created_at, '{$datetime_format}') AS requested_at_formatted,
            DATE_FORMAT({$this->table}.created_at, '{$datetime_format}') AS created_at_formatted,
            DATE_FORMAT({$this->table}.approved_at, '{$datetime_format}') AS approved_at_formatted,
            DATE_FORMAT({$this->table}.filed_at, '{$datetime_format}') AS filed_at_formatted,
            cb.employee_name AS created_by_name,
            ab.employee_name AS approved_by_name,
            fb.employee_name AS filed_by_name
        ";

        $builder->select($columns);
        $this->joinSupplier($builder, $supplierModel);
        $this->joinRpf($builder, $rpfModel);
        $this->joinRpf($builder, $rpfModel, true);
        $this->joinAccountView($builder, "{$this->table}.created_by", 'cb');
        $this->joinAccountView($builder, "{$this->table}.approved_by", 'ab');
        $this->joinAccountView($builder, "{$this->table}.filed_by", 'fb');

        $this->filterParam($request, $builder, "{$this->table}.status");

        $builder->where("{$this->table}.deleted_at", null);
        $builder->orderBy('id', 'DESC');

        return $builder;
   }

   // DataTable action buttons
   public function buttons($permissions)
   {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $title      = 'PO';
        $closureFun = function($row) use($id, $permissions, $dropdown, $title) {
            $buttons = dt_button_actions($row, $id, $permissions, $dropdown, ['exclude_edit']);

            if ($row['status'] === 'pending') {
                if (check_permissions($permissions, 'APPROVE')) {
                    // Approve PO
                    $changeTo = 'approve';
                    $buttons .= dt_button_html([
                        'text'      => $dropdown ? ucfirst($changeTo) : '',
                        'button'    => 'btn-primary',
                        'icon'      => 'fas fa-check-circle',
                        'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                    ], $dropdown);
                }
            }

            if (check_permissions($permissions, 'APPROVED') && $row['status'] === 'approved') {
                // File PO
                $changeTo = 'file';
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? ucfirst($changeTo) : '',
                    'button'    => 'btn-success',
                    'icon'      => 'fas fa-sign-in-alt',
                    'condition' => dt_status_onchange($row[$id], $changeTo, $row['status'], $title),
                ], $dropdown);
            }

            if (check_permissions($permissions, 'PRINT') && in_array($row['status'], ['approved', 'filed'])) {
                // Print PO
                // $print_url  = site_url('purchase-orders/print/') . $row[$id];
                $print_url  = url_to('purchase_order.print', $row[$id]);
                $buttons    .= <<<EOF
                    <a href="$print_url" class="btn btn-dark btn-sm" target="_blank" title="Print {$title}"><i class="fas fa-print"></i></a>
                EOF;
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
   }

   // DataTable view PO items
   public function dtViewPOItems()
   {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id) {
            return <<<EOF
                <button class="btn btn-sm btn-primary" onclick="view({$row[$id]}, '{$row['status']}')"><i class="fas fa-eye"></i> View</button>
            EOF;
       };
       
       return $closureFun;
   }

   // DataTable status formatter
   public function dtPOStatusFormat()
   {
       $closureFun = function($row) {
           $text    = ucwords(set_po_status($row['status']));
           $color   = $row['status'] === 'filed' ? 'success' : dt_status_color($row['status']);
           return text_badge($color, $text);
       };
       
       return $closureFun;
   }
}