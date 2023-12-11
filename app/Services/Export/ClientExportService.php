<?php

namespace App\Services\Export;

use App\Models\CustomerModel;
use App\Models\CustomerBranchModel;

class ClientExportService extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function clients($filters = [])
    {
        $model      = new CustomerModel();
        $address    = dt_sql_concat_client_address($model->table);
        $columns    = "
            {$model->table}.id,
            IF({$model->table}.forecast = 0, 'NO', 'YES') AS new_client,
            {$model->table}.name,
            {$model->table}.type,
            {$model->table}.contact_person,
            {$model->table}.contact_number,
            {$model->table}.telephone,
            {$model->table}.email_address,
            {$address},
            {$model->table}.source, 
            {$model->table}.notes,
            {$model->table}.referred_by,
            cb.employee_name AS created_by,
            ".dt_sql_datetime_format("{$model->table}.created_at")." AS created_at
        ";
        $builder    = $model->select($columns);

        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');

        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'type');

        $builder->where("{$model->table}.deleted_at IS NULL")->orderBy('id', 'DESC');

        $data       = $builder->findAll();
        $header     = [
            'Client ID',
            'New Client?',
            'Client Name',
            'Client Type',
            'Contact Person',
            'Contact Number',
            'Telephone Number',
            'Email Address',
            'Address',
            'Source',
            'Notes',
            'Referred By',
            'Created By',
            'Created At',
        ];
        $filename   = 'Clients Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }

    /**
     * Exporting data to csv - client's branches
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function branches($filters = [])
    {
        $model      = new CustomerBranchModel();
        $datetimeFormat = dt_sql_datetime_format();
        $address    = dt_sql_concat_client_address();
        $columns    = "
            {$model->table}.id,
            {$model->table}.customer_id,
            {$model->table}.branch_name,
            {$model->table}.contact_person,
            {$model->table}.contact_number,
            {$model->table}.email_address,
            {$address},
            {$model->table}.notes,
            cb.employee_name AS created_by,
            DATE_FORMAT({$model->table}.created_at, '{$datetimeFormat}') AS created_at
        ";
        $builder    = $model->select($columns);
        $this->joinAccountView($builder, "{$model->table}.created_by", 'cb');
        $builder->where("deleted_at IS NULL")->orderBy('id', 'DESC');
        
        // Process and add filters
        $this->processFilters($model->table, $builder, $filters, 'type');

        $data       = $builder->findAll();
        $header     = [
            'Branch ID',
            'Client ID',
            'Client Branch Name',
            'Contact Person',
            'Contact Number',
            'Email Address',
            'Address',
            'Notes',
            'Created By',
            'Created At',
        ];
        $filename   = 'Client Branches Masterlist';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
}