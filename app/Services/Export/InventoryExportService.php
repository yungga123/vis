<?php

namespace App\Services\Export;

use App\Models\InventoryModel;
use App\Models\ProjectRequestFormModel;
use App\Models\PRFItemModel;
use App\Models\JobOrderModel;
use App\Models\CustomerModel;
use App\Models\TaskLeadView;

class InventoryExportService extends ExportService
{
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
            ".dt_sql_date_format("{$model->table}.date_of_purchase")." AS date_of_purchase,
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
            "Dealer's Price",
            'Total Price',
            'Retail Price',
            'Project Price',
            'Date of Purchase',
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
    public function prf($filters = [])
    {
        $model          = new ProjectRequestFormModel();
        $joModel        = new JobOrderModel();
        $customerModel  = new CustomerModel();
        $tlBookedModel  = new TaskLeadView();
        $columns    = "
            UPPER({$model->table}.status) AS status,
            {$model->table}.id,
            {$model->table}.job_order_id,
            IF({$joModel->table}.is_manual = 0, {$tlBookedModel->table}.quotation_num, {$joModel->table}.manual_quotation) AS quotation,
            IF({$joModel->table}.is_manual = 0, {$tlBookedModel->table}.tasklead_type, {$joModel->table}.manual_quotation_type) AS quotation_type,
            IF({$joModel->table}.is_manual = 0, {$tlBookedModel->table}.customer_name, {$customerModel->table}.name) AS client,
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
}