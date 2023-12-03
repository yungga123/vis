<?php

namespace App\Services\Export;

use App\Models\CustomerModel;

class ClientExport extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $params     The passed params or request
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
        $this->processFilters($model->table, $builder, $filters, 'customer_type');

        $builder->where("{$model->table}.deleted_at IS NULL")->orderBy('id', 'DESC');

        $data       = $builder->findAll();
        $header     = [
            'Client ID',
            'New Client?',
            'Client Name',
            'Client Type',
            'Contact Person',
            'Contact Number',
            'Email Address',
            'Address',
            'Source',
            'Notes',
            'Referred By',
            'Created By',
            'Created At',
        ];
        $filename   = 'Clients Masterlist';

        $this->exportToCsv($data, $header, $filename);
    }
}