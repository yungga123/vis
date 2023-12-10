<?php

namespace App\Services\Export;

use App\Models\PurchaseOrderModel;
use App\Models\POItemModel;
use App\Models\SuppliersModel;
use App\Models\SupplierBrandsModel;
use App\Models\RequestPurchaseFormModel;
use App\Models\RPFItemModel;
use App\Models\InventoryModel;

class PurchasingExportService extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function purchaseOrders($filters = [])
    {
        $model          = new PurchaseOrderModel();        
        $supplierModel  = new SuppliersModel();
        $rpfModel       = new RequestPurchaseFormModel();
        $columns        = "
            UPPER({$model->table}.status) AS status,
            {$model->table}.id,
            {$model->table}.rpf_id,
            {$supplierModel->table}.supplier_name,
            {$model->table}.attention_to,
            IF({$model->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            {$rpfModel->view}.created_by_name AS requested_by,
            ".dt_sql_datetime_format("{$rpfModel->table}.created_at")." AS requested_at,
            cb.employee_name AS generated_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS generated_at,
            ab.employee_name AS approved_by,
            ".dt_sql_datetime_format("{$model->table}.approved_at")." AS approved_at,
            fb.employee_name AS filed_by,
            ".dt_sql_datetime_format("{$model->table}.filed_at")." AS filed_at
        ";
        $builder    = $model->select($columns);

        $model->joinSupplier($builder, $supplierModel);
        $model->joinRpf($builder, $rpfModel);
        $model->joinRpf($builder, $rpfModel, true);
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');
        $this->joinAccountView($builder, "{$model->table}.approved_by", 'ab');
        $this->joinAccountView($builder, "{$model->table}.filed_by", 'fb');

        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$model->table}.id", 'ASC');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);
        
        $data       = $builder->findAll();
        $header     = [
            'Status',
            'PO #',
            'RPF #',
            'Supplier',
            'Attention To',
            'With Vat',
            'Requested By',
            'Requested At',
            'Generated By',
            'Generated At',
            'Approved By',
            'Approved At',
            'Filed By',
            'Filed At',
        ];
        $filename   = 'Purchase Orders';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function poItems($filters = [])
    {
        $model          = new PurchaseOrderModel();        
        $poItemModel    = new POItemModel();
        $rpfItemModel   = new RPFItemModel();
        $inventoryModel = new InventoryModel();
        $columns        = "
            {$poItemModel->table}.po_id,
            {$rpfItemModel->table}.rpf_id,
            {$rpfItemModel->table}.inventory_id,
            {$inventoryModel->view}.supplier_name,
            {$inventoryModel->view}.brand,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->table}.stocks,
            {$inventoryModel->view}.size,
            {$inventoryModel->view}.unit,
            {$rpfItemModel->table}.quantity_in,
            ".dt_sql_number_format("{$inventoryModel->table}.item_sdp")." AS item_price,
            ".dt_sql_number_format("{$inventoryModel->table}.item_sdp * {$rpfItemModel->table}.quantity_in")." AS total_price,
            UPPER({$model->table}.status) AS status,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
            ab.employee_name AS approved_by,
            ".dt_sql_datetime_format("{$model->table}.approved_at")." AS approved_at,
            fb.employee_name AS filed_by,
            ".dt_sql_datetime_format("{$model->table}.filed_at")." AS filed_at
        ";
        $builder        = $model->select($columns);

        $model->joinPOItem($builder, $poItemModel);
        $model->joinRpfItem($builder, $rpfItemModel);
        $poItemModel->joinInventory($builder, $inventoryModel);
        $poItemModel->joinInventory($builder, $inventoryModel, true);

        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');
        $this->joinAccountView($builder, "{$model->table}.approved_by", 'ab');
        $this->joinAccountView($builder, "{$model->table}.filed_by", 'fb');

        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$poItemModel->table}.po_id", 'ASC');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);
        
        $data       = $builder->findAll();
        $header     = [
            'PO #',
            'RPF #',
            'Item #',
            'Supplier',
            'Item Brand',
            'Item Model',
            'Item Description',
            'Item Size',
            'Item Unit',
            'Current Stocks',
            'Quantity In',
            'Item Price',
            'Total Price',
            'Status',
            'Generated By',
            'Generated At',
            'Approved By',
            'Approved At',
            'Filed By',
            'Filed At',
        ];
        $filename   = 'RPF Items';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv 
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function suppliers($filters = [])
    {
        $model      = new SuppliersModel();
        $builder    = $model->select($model->dtColumns());

        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');
        $builder->where("{$model->table}.deleted_at IS NULL")->orderBy('id', 'ASC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'supplier_type');

        $data       = $builder->findAll();
        $header     = [
            'Supplier ID',
            'Supplier Name',
            'Supplier Type',
            'Address',
            'Contact Person',
            'Contact Number',
            'Viber',
            'Email Address',
            'Payment Terms',
            'Mode of Payment',
            'Product',
            'Bank Name',
            'Bank Account Name',
            'Bank Number',
            'Remarks',
            'Created By',
            'Created At'
        ];
        $filename   = 'Suppliers Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv 
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function supplierBranches($filters = [])
    {
        $model          = new SupplierBrandsModel();
        $supplierModel  = new SuppliersModel();
        $columns        = "
            {$supplierModel->table}.id,
            {$supplierModel->table}.supplier_name,
            {$supplierModel->table}.supplier_type,
            {$model->table}.id AS brand_id,
        ". $model->dtColumns();
        $builder        = $model->select($columns);

        $model->joinSupplier(null, $supplierModel);
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'ASC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'supplier_type');

        $data       = $builder->findAll();
        $header     = [
            'Supplier ID',
            'Supplier Name',
            'Supplier Type',
            'Brand ID',
            'Brand Name',
            'Brand Product',
            'Warranty',
            'Sales Person',
            'Sales Contact Number',
            'Tech Support',
            'Tech Contact Number',
            'Remarks',
            'Created By',
            'Created At'
        ];
        $filename   = 'Supplier Brands Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv 
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function rpf($filters = [])
    {
        $model      = new RequestPurchaseFormModel();
        $columns    = "
            UPPER({$model->table}.status) AS status,
            {$model->table}.id,
            ".dt_sql_date_format("{$model->table}.date_needed")." AS date_needed,
            {$model->view}.created_by_name,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
            {$model->view}.accepted_by_name,
            ".dt_sql_datetime_format("{$model->table}.accepted_at")." AS accepted_at,
            {$model->view}.rejected_by_name,
            ".dt_sql_datetime_format("{$model->table}.rejected_at")." AS rejected_at,
            {$model->view}.reviewed_by_name,
            ".dt_sql_datetime_format("{$model->table}.reviewed_at")." AS reviewed_at,
            {$model->view}.received_by_name,
            ".dt_sql_datetime_format("{$model->table}.received_at")." AS received_at
        ";
        
        // Process and add filters
        $this->processFilters($model->table, $model, $filters);

        $data       = $model->getRequestPurchaseForms(null, true, $columns);
        $header     = [
            'Status',
            'RPF #',
            'Date Needed',
            'Requested By',
            'Requested At',
            'Accepted By',
            'Accepted At',
            'Reviewed By',
            'Reviewed At',
            'Received By',
            'Received At',
            'Rejected By',
            'Rejected At'
        ];
        $filename   = 'Request to Purchase Forms';

        $this->logSelectQuery($model, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv 
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function rpfItems($filters = [])
    {
        $model          = new RequestPurchaseFormModel();
        $rpfItemModel   = new RPFItemModel();
        $inventoryModel = new InventoryModel();
        $columns        = "
            {$rpfItemModel->table}.rpf_id,
            {$rpfItemModel->table}.inventory_id,
            {$inventoryModel->view}.supplier_name,
            {$inventoryModel->view}.brand,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->view}.size,
            {$inventoryModel->view}.unit,
            {$inventoryModel->table}.stocks,
            {$rpfItemModel->table}.quantity_in,
            ".dt_sql_number_format("{$inventoryModel->table}.item_sdp")." AS item_price,
            ".dt_sql_number_format("{$inventoryModel->table}.item_sdp * {$rpfItemModel->table}.quantity_in")." AS total_price,
            {$rpfItemModel->table}.received_q,
            ".dt_sql_date_format("{$rpfItemModel->table}.received_date")." AS received_date,
            UPPER({$model->table}.status) AS status,
            {$rpfItemModel->table}.purpose,
            {$model->view}.created_by_name,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder        = $rpfItemModel->select($columns);

        $rpfItemModel->join($model->table, "{$model->table}.id = {$rpfItemModel->table}.rpf_id", 'left');
        $rpfItemModel->join($model->view, "{$model->view}.rpf_id = {$rpfItemModel->table}.rpf_id", 'left');
        $rpfItemModel->join($inventoryModel->table, "{$inventoryModel->table}.id = {$rpfItemModel->table}.inventory_id", 'left');
        $rpfItemModel->join($inventoryModel->view, "{$inventoryModel->table}.id = {$inventoryModel->view}.inventory_id", 'left');

        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$rpfItemModel->table}.rpf_id", 'ASC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $data       = $builder->findAll();
        $header     = [
            'RPF #',
            'Item #',
            'Supplier',
            'Item Brand',
            'Item Model',
            'Item Description',
            'Item Size',
            'Item Unit',
            'Current Stocks',
            'Quantity In',
            'Item Price',
            'Total Price',
            'Received Qty',
            'Received Date',
            'Status',
            'Purpose',
            'Created By',
            'Created At',
        ];
        $filename   = 'RPF Items';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
}