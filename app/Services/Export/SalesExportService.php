<?php

namespace App\Services\Export;

use App\Models\TaskLeadModel;

class SalesExportService extends ExportService
{
    /**
     * Exporting data to csv
     *
     * @param array $filters     The passed params or request
     * @return void
     */
    public function taskleads($filters = [], $booked = false)
    {
        $model      = new TaskLeadModel();
        $builder    = $model->dtGetTaskLeads($booked);
        $optionFN   = $booked ? 'quarter' : 'status';
        $dateFN     = $booked ? 'updated_at' : 'created_at';
        
        if (! $booked) $builder->where('status !=', $model->booked);

        // Process and add filters
        $this->processFilters($model->view, $builder, $filters, $optionFN, $dateFN);

        $query      = $builder->orderBy('id', 'ASC')->get();
        $data       = $query->getResultArray();
        $header     = [
            'Tasklead ID',
            'Employee Name',
            'Quarter',
            'Percent',
            'Status',
            'Client Name',
            'Client Type',
            'Branch Name',
            'Contact Number',
            'Project',
            'Amount',
            'Quotation Number',
            'Quotation Type',
            'Forecast Close Date',
            'Min. Forecast',
            'Max Forecast',
            'Hit?',
            'Remark Next Step',
            'Close Deal Date',
            'Start Date',
            'End Date',
            'Duration',
            'Created By',
            'Created At'
        ];
        $header     = $booked ? array_merge($header, ['Booked At (the date filter was based on this)']) : $header;
        $filename   = $booked ? 'Booked Task Leads' : 'Task Leads';

        $this->logSelectQuery($builder, __METHOD__);

        $this->exportToCsv($data, $header, $filename);
    }
}