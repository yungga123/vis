<?php

namespace App\Services\Export;

use App\Models\BillingInvoiceModel;
use App\Models\BillingInvoiceOrderFormModel;
use App\Models\CustomerBranchModel;
use App\Models\CustomerModel;
use App\Models\FundsHistoryModel;
use App\Models\TaskLeadView;

class FinanceExportService extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function billingInvoices($filters = [])
    {
        $tlVModel   = new TaskLeadView();
        $model      = new BillingInvoiceModel();
        $columns    = "
            UPPER({$model->table}.billing_status) AS billing_status,
            {$model->table}.id,
            {$model->table}.tasklead_id,
            {$tlVModel->table}.quotation_num AS quotation,
            {$tlVModel->table}.customer_name AS client,
            {$tlVModel->table}.employee_name AS manager,
            {$tlVModel->table}.project,
            " . dt_sql_number_format("{$tlVModel->table}.project_amount") . " AS project_amount,
            {$tlVModel->table}.tasklead_type AS quotation_type,
            " . dt_sql_date_format("{$model->table}.due_date") . " AS due_date,
            {$model->table}.bill_type,
            {$model->table}.payment_method,
            " . dt_sql_number_format("{$model->table}.billing_amount") . " AS billing_amount,
            " . dt_sql_number_format("{$model->table}.overdue_interest") . " AS overdue_interest,
            " . dt_sql_number_format("{$model->table}.amount_paid") . " AS amount_paid,
            " . dt_sql_datetime_format("{$model->table}.paid_at") . " AS paid_at,
            {$model->table}.attention_to,
            IF({$model->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            " . dt_sql_number_format("{$model->table}.vat_amount") . " AS vat_amount,
            cb.employee_name AS created_by,
            " . dt_sql_datetime_format("{$model->table}.created_at") . " AS created_at
        ";
        $builder    = $model->select($columns);

        // Join with other tables
        $model->joinBookedTasklead($builder, $tlVModel);
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'billing_status');

        $builder->orderBy("{$model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Billing Status',
            'Billing ID',
            'Tasklead ID',
            'Quotation Number',
            'Client',
            'Manager',
            'Project Description',
            'Project Amount',
            'Quotation Type',
            'Due Date',
            'Bill Type',
            'Payment Method',
            'Billing Amount',
            'Overdue Interest',
            'Amount Paid',
            'Paid At',
            'Attention To',
            'With Vat?',
            'Vat Amount',
            'Created By',
            'Created At'
        ];
        $filename   = 'Billing Invoices (Task/Leads)';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function billingInvoicesOrderForms($filters = [])
    {
        $model          = new BillingInvoiceOrderFormModel();
        $customerModel  = new CustomerModel();
        $branchModel    = new CustomerBranchModel();
        $columns        = "
            UPPER({$model->table}.status) AS status,
            UPPER({$model->table}.billing_status) AS billing_status,
            {$model->table}.id,
            {$model->table}.order_form_id,
            {$customerModel->table}.name AS client,
            {$branchModel->table}.branch_name AS client_branch,
            " . dt_sql_date_format("{$model->table}.due_date") . " AS due_date,
            {$model->table}.bill_type,
            {$model->table}.payment_method,
            {$model->table}.receipt_number,
            " . dt_sql_number_format("{$model->table}.billing_amount") . " AS billing_amount,
            " . dt_sql_number_format("{$model->table}.overdue_interest") . " AS overdue_interest,
            " . dt_sql_number_format("{$model->table}.vat_amount") . " AS vat_amount,
            " . dt_sql_number_format("{$model->table}.withholding_tax") . " AS withholding_tax,
            " . dt_sql_number_format("{$model->table}.amount_paid") . " AS amount_paid,
            " . dt_sql_date_format("{$model->table}.date_paid") . " AS date_paid,
            " . dt_sql_datetime_format("{$model->table}.paid_at") . " AS paid_at,
            IF({$model->table}.with_vat = 0, 'NO', 'YES') AS with_vat,
            {$model->table}.attention_to,
            {$model->table}.additional_description,
            cb.employee_name AS created_by,
            ab.employee_name AS approved_by,
            " . dt_sql_datetime_format("{$model->table}.created_at") . " AS created_at,
            " . dt_sql_datetime_format("{$model->table}.approved_at") . " AS approved_at
        ";
        $builder        = $model->select($columns);

        // Join with other tables
        $model->joinOrderForms($builder, null, true);
        $this->joinAccountView($builder, 'created_by', 'cb');
        $this->joinAccountView($builder, 'approved_by', 'ab');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'billing_status');

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'Status',
            'Billing Status',
            'Billing ID',
            'Order Form ID',
            'Client',
            'Client Branch',
            'Due Date',
            'Bill Type',
            'Payment Method',
            'Receipt Number',
            'Billing Amount',
            'Overdue Interest',
            'Vat Amount',
            'Withholding Tax',
            'Amount Paid',
            'Date Paid',
            'Paid At',
            'With Vat?',
            'Attention To',
            'Additional Description',
            'Created By',
            'Created At',
            'Approved By',
            'Approved At'
        ];
        $filename   = 'Billing Invoices (Order Forms)';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function fundsHistory($filters = [])
    {
        $model      = new FundsHistoryModel();
        $compute    = "
            IF({$model->table}.transaction_type = 'outgoing', {$model->table}.current_funds - {$model->table}.transaction_amount, {$model->table}.current_funds + {$model->table}.transaction_amount)
        ";
        $columns    = "
            {$model->table}.id,
            UPPER({$model->table}.transaction_type) AS transaction_type,
            " . dt_sql_number_format("{$model->table}.transaction_amount") . " AS transaction_amount,
            " . dt_sql_number_format("{$model->table}.current_funds") . " AS previous_funds,
            " . dt_sql_number_format("{$compute}") . " AS current_funds,
            {$model->table}.coming_from,
            {$model->table}.expenses,
            {$model->table}.remarks,
            cb.employee_name AS created_by,
            " . dt_sql_datetime_format("{$model->table}.created_at") . " AS created_at
        ";
        $builder    = $model->select(new \CodeIgniter\Database\RawSql($columns));

        // Join with other tables
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');

        $builder->where("{$model->table}.deleted_at IS NULL");
        $builder->orderBy("{$model->table}.id", 'DESC');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'transaction_type');

        $builder->orderBy("{$model->table}.id", 'ASC');

        $data       = $builder->findAll();
        $header     = [
            'ID #',
            'Transaction Type',
            'Transaction Amount',
            'Previous Funds',
            'Running Funds',
            'Coming From',
            'Expenses',
            'Remarks',
            'Created By',
            'Created At'
        ];
        $filename   = 'Billing Invoices';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
}
