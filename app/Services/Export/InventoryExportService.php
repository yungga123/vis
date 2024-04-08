<?php

namespace App\Services\Export;

use App\Models\CustomerBranchModel;
use App\Models\InventoryModel;
use App\Models\ProjectRequestFormModel;
use App\Models\PRFItemModel;
use App\Models\JobOrderModel;
use App\Models\CustomerModel;
use App\Models\InventoryLogsModel;
use App\Models\OrderFormItemModel;
use App\Models\OrderFormModel;
use App\Traits\HRTrait;
use App\Traits\InventoryTrait;

class InventoryExportService extends ExportService
{
    /* Declare trait here to use */
    use HRTrait, InventoryTrait;

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function items($filters = [])
    {
        $model      = new InventoryModel();
        $columns    = "
            {$model->table}.id,
            {$model->view}.supplier_name,
            {$model->view}.category_name,
            {$model->view}.subcategory_name,
            {$model->view}.brand,
            {$model->table}.item_model,
            {$model->table}.item_description,
            {$model->view}.size,
            {$model->view}.unit,
            {$model->table}.stocks,
            ".dt_sql_number_format("{$model->table}.item_sdp")." AS item_sdp,
            ".dt_sql_number_format("({$model->table}.stocks * {$model->table}.item_sdp)")." AS total_price,
            ".dt_sql_number_format("{$model->table}.item_srp")." AS item_srp,
            ".dt_sql_number_format("{$model->table}.project_price")." AS project_price,
            {$model->table}.location,
            {$model->view}.created_by_name,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder    = $model->select($columns);

        $model->joinView($builder);
        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$model->table}.id", 'ASC');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'category');

        $data       = $builder->findAll();
        $header     = [
            'Item #',
            'Supplier',
            'Category',
            'Sub-Category',
            'Item Brand',
            'Item Model',
            'Item Description',
            'Item Size',
            'Item Unit',
            'Quantity',
            'Item Price',
            'Total Price',
            'Retail Price',
            'Project Price',
            'Location',
            'Encoder',
            'Encoded At'
        ];
        $filename   = 'Inventory Items Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function itemLogs($filters = [])
    {
        $invModel   = new InventoryModel();
        $model      = new InventoryLogsModel();
        $columns    = "
            UPPER({$model->table}.action) AS type,
            {$model->table}.inventory_id,
            {$invModel->view}.supplier_name,
            {$invModel->view}.category_name,
            {$invModel->view}.subcategory_name,
            {$invModel->view}.brand,
            {$invModel->table}.item_model,
            {$invModel->table}.item_description,
            {$invModel->view}.size,
            {$invModel->view}.unit,
            {$model->table}.stocks,
            {$model->table}.parent_stocks,
            {$invModel->table}.stocks AS current_stocks,
            ".dt_sql_number_format("{$invModel->table}.item_sdp")." AS item_sdp,
            (UPPER({$model->table}.status)) AS cap_status,
            ".dt_sql_date_format("{$model->table}.status_date")." AS status_date,
            cb.employee_name AS encoder,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder    = $model->select($columns);

        $model->joinInventory($builder);
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');

        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$model->table}.inventory_logs_id", 'DESC');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'action');

        $data       = $builder->findAll();
        $header     = [
            'Log Type',
            'Item #',
            'Supplier',
            'Category',
            'Sub-Category',
            'Item Brand',
            'Item Model',
            'Item Description',
            'Item Size',
            'Item Unit',
            'Quantity',
            'Prev Stocks',
            'Current Stocks',
            'Item Price',
            'Status',
            'Status Date',
            'Encoder',
            'Encoded At'
        ];
        $filename   = 'Inventory Logs Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function prf($filters = [])
    {
        $model          = new ProjectRequestFormModel();
        $joModel        = new JobOrderModel();
        $columns    = "
            UPPER({$model->table}.status) AS status,
            {$model->table}.id,
            {$model->table}.job_order_id,
            {$joModel->view}.quotation,
            {$joModel->view}.quotation_type,
            {$joModel->view}.client_name,
            {$joModel->table}.work_type,
            ".dt_sql_date_format("{$joModel->table}.date_requested")." AS date_requested,
            ".dt_sql_date_format("{$joModel->table}.date_committed")." AS date_committed,
            ".dt_sql_date_format("{$model->table}.process_date")." AS process_date,
            {$model->table}.remarks,
            {$model->view}.created_by_name,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
            {$model->view}.accepted_by_name,
            ".dt_sql_datetime_format("{$model->table}.accepted_at")." AS accepted_at,
            {$model->view}.rejected_by_name,
            ".dt_sql_datetime_format("{$model->table}.rejected_at")." AS rejected_at,
            {$model->view}.item_out_by_name,
            ".dt_sql_datetime_format("{$model->table}.item_out_at")." AS item_out_at,
            {$model->view}.filed_by_name,
            ".dt_sql_datetime_format("{$model->table}.filed_at")." AS filed_at
        ";
        $builder    = $model->select($columns);

        $model->joinView($builder)->joinJobOrder($builder);

        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$model->table}.id", 'ASC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'PRF #',
            'Job Order #',
            'Quotation',
            'Quotation Type',
            'Client',
            'Work Type',
            'Date Requested',
            'Date Committed',
            'Process Date',
            'Remarks',
            'Created By',
            'Created At',
            'Accepted By',
            'Accepted At',
            'Rejected By',
            'Rejected At',
            'Item Out By',
            'Item Out At',
            'Filed By',
            'Filed At'
        ];
        $filename   = 'Project Request Forms';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function prfItems($filters = [])
    {
        $model          = new ProjectRequestFormModel();
        $prfItemModel   = new PRFItemModel();
        $inventoryModel = new InventoryModel();
        $columns        = "
            {$prfItemModel->table}.prf_id,
            {$model->table}.job_order_id,
            {$prfItemModel->table}.inventory_id,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->table}.stocks,
            {$prfItemModel->table}.quantity_out,
            {$prfItemModel->table}.returned_q,
            {$prfItemModel->queryConsumed()},
            ".dt_sql_date_format("{$prfItemModel->table}.returned_date")." AS returned_date,
            UPPER({$model->table}.status) AS status,
            {$model->view}.created_by_name,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder        = $prfItemModel->select($columns);

        $prfItemModel->join($model->table, "{$model->table}.id = {$prfItemModel->table}.prf_id", 'left');
        $prfItemModel->join($model->view, "{$model->view}.prf_id = {$prfItemModel->table}.prf_id", 'left');
        $prfItemModel->join($inventoryModel->table, "{$inventoryModel->table}.id = {$prfItemModel->table}.inventory_id", 'left');
        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$prfItemModel->table}.prf_id", 'ASC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $data       = $builder->findAll();
        $header     = [
            'PRF #',
            'Job Order #',
            'Item #',
            'Item Model',
            'Item Description',
            'Current Stocks',
            'Quantity Out',
            'Quantity Returned',
            'Consumed Qty',
            'Date Returned',
            'Status',
            'Created By',
            'Created At',
        ];
        $filename   = 'PRF Items';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function orderForms($filters = [])
    {
        $model          = new OrderFormModel();
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $columns        = "
            UPPER({$model->table}.status) AS status,
            {$model->table}.id,
            {$customerModel->table}.name AS client_name,
            {$branchModel->table}.branch_name AS client_branch_name,
            ".dt_sql_datetime_format("{$model->table}.purchase_at")." AS purchase_at,
            ".dt_sql_number_format("{$model->table}.total_amount")." AS total_amount,
            ".dt_sql_number_format("{$model->table}.total_discount")." AS total_discount,
            IF({$model->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            ".dt_sql_number_format("{$model->table}.vat_amount")." AS vat_amount,
            ".dt_sql_number_format("{$model->table}.grand_total")." AS grand_total,
            {$model->table}.remarks,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
            ab.employee_name AS accepted_by,
            ".dt_sql_datetime_format("{$model->table}.accepted_at")." AS accepted_at,
            rb.employee_name AS rejected_by,
            ".dt_sql_datetime_format("{$model->table}.rejected_at")." AS rejected_at,
            ib.employee_name AS item_out_by,
            ".dt_sql_datetime_format("{$model->table}.item_out_at")." AS item_out_at,
            rcb.employee_name AS received_by,
            ".dt_sql_datetime_format("{$model->table}.received_at")." AS received_at,
            fb.employee_name AS filed_by,
            ".dt_sql_datetime_format("{$model->table}.filed_at")." AS filed_at,
        ";
        $builder    = $model->select($columns);

        $model->joinCustomers($builder, $customerModel, '', true);

        $this->joinAccountView($builder, 'created_by', 'cb');
        $this->joinAccountView($builder, 'accepted_by', 'ab');
        $this->joinAccountView($builder, 'rejected_by', 'rb');
        $this->joinAccountView($builder, 'item_out_by', 'ib');
        $this->joinAccountView($builder, 'received_by', 'rcb');
        $this->joinAccountView($builder, 'filed_by', 'fb');

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'DESC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'Order Form #',
            'Client',
            'Client Branch',
            'Purchased At',
            'Total Amount',
            'Total Discount',
            'With Vat?',
            'Vat Amount',
            'Grand Total (w/ Vat)',
            'Remarks',
            'Created By',
            'Created At',
            'Item Out By',
            'Item Out At',
            'Received By',
            'Received At',
            'Filed By',
            'Filed At',
            'Rejected By',
            'Rejected At'
        ];
        $filename   = 'Order Forms';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function orderFormItems($filters = [])
    {
        $model          = new OrderFormModel();
        $ofItemModel    = new OrderFormItemModel();
        $inventoryModel = new InventoryModel();
        $columns        = "
            {$ofItemModel->table}.order_form_id,
            {$ofItemModel->table}.inventory_id,
            {$inventoryModel->view}.supplier_name,
            {$inventoryModel->view}.category_name,
            {$inventoryModel->table}.item_model,
            {$inventoryModel->table}.item_description,
            {$inventoryModel->view}.brand,
            {$inventoryModel->view}.size,
            {$inventoryModel->view}.unit,
            {$inventoryModel->table}.stocks,
            ".dt_sql_number_format("{$inventoryModel->table}.item_sdp")." AS item_price,
            ".dt_sql_number_format("{$ofItemModel->table}.quantity")." AS quantity,
            ".dt_sql_number_format("{$ofItemModel->table}.discount")." AS discount,
            ".dt_sql_number_format("{$ofItemModel->table}.discount")." AS total_price,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at,
        ";
        $builder        = $ofItemModel->select($columns);

        $ofItemModel->join($model->table, "{$model->table}.id = {$ofItemModel->table}.order_form_id", 'left');
        
        $this->joinInventory($ofItemModel->table, $builder, true);
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');

        $builder->where("{$model->table}.deleted_at", null);
        $builder->orderBy("{$ofItemModel->table}.order_form_id", 'ASC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters);

        $data       = $builder->findAll();
        $header     = [
            'Order Form #',
            'Item #',
            'Supplier',
            'Category',
            'Item Model',
            'Item Description',
            'Item Brand',
            'Item Unit',
            'Item Size',
            'Current Stocks',
            'Item Price',
            'Quantity',
            'Discount',
            'Total Price',
            'Created By',
            'Created At',
        ];
        $filename   = 'Order Form Items';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
}