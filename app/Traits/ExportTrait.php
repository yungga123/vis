<?php

namespace App\Traits;

use CodeIgniter\Database\RawSql;

trait ExportTrait
{
    /**
     * Max months allowed in filter.
     * To avoid eating up server resources
     * and avoid request time out
     * 
     * @var int
     */
    protected $maxMonthsToExport = 6;

    /**
     * Exporting data to csv
     *
     * @param array $data       The query results to export
     * @param array $header     The title header for the csv
     * @param array $filename   The file name of the csv
     * @param callable|null $callback   An optional loop callback function to use 
     * when you have to change/process data before putting to csv output
     * 
     * @return \Exception|void
     */
    public function exportToCsv($data, $header, $filename, $callback = null)
    {
        try {
            // Start the output buffer.
            ob_start();

            // Create a file pointer with PHP
            $output = fopen('php://output', 'w');

            // Add CSV header
            fputcsv($output, $header);

            // Loop through the prepared data to output it to CSV file
            if ($callback) {
                // A callback function to use when you have to change/process
                // data before putting to csv output
                $callback($data, $output);
            } else {
                // Using foreach for readability
                // foreach($data as $row){
                //     fputcsv($output, $row);
                // }

                // Using while for memory efficient - good for processing large data set
                $i = 0;
                while (isset($data[$i])) {
                    $row = $data[$i];
                    fputcsv($output, $row);
                    
                    $i++;
                }
            }

            // Close the file pointer with PHP with the updated output
            fclose($output);

            // Capture the output and clear the buffer
            $csv        = ob_get_clean();
            $filename   = $filename .' - '. date('Y-m-d h-i-s A');

            // Send the CSV as a download
            header("Content-Disposition: attachment; filename={$filename}.csv");
            header("Content-Type: text/csv");

            echo $csv;
            exit;
        } catch (\Exception $e) {
            log_message('error', '[EXPORT ERROR] {exception}', ['exception' => $e]);
            return redirect()->back();
        }
    }

    /**
     * Process and add filter/where clause
     *
     * @param string $table     The table name
     * @param object $builder   The db object
     * @param array $filters    The request filters
     * @param string $optionFN  The option field name (eg. status, customer_type or etc..) 
     * 
     * @return void
     */
    public function processFilters($table, $builder, $filters, $optionFN = 'status')
    {
        if (! empty($filters)) {
            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $start_date = $filters['start_date'];
                $end_date   = $filters['end_date'];
                
                // Check date
                $this->checkDateFilter($start_date, $end_date);

                // Additional filter/where clause on between
                $between = "(DATE({$table}.created_at) BETWEEN '{$start_date}' AND '{$end_date}')";
                $builder->where(new RawSql($between));
            }

            if (isset($filters['status'])) {
                $status     = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
                
                // Additional filter/where clause on status
                $builder->where("{$table}.{$optionFN}", $status);
            }
        }
    }

    /**
     * Check date filter to see 
     * if exceeded in allowed months
     *
     * @param string $startDate
     * @param string $endDate
     * 
     * @return void|\Exception
     */
    public function checkDateFilter($startDate, $endDate)
    {
        // Get the months
        $months = $this->calculateDateFilter($startDate, $endDate);

        if ($months > $this->maxMonthsToExport) {
            throw new \Exception(
                "Date filter is greated than the allowed months which is {$this->maxMonthsToExport} months only!",
                1
            );
        }
    }

    /**
     * Calculate date filter to see 
     * if exceeded in allowed months
     *
     * @param string $startDate
     * @param string $endDate
     * 
     * @return void
     */
    public function calculateDateFilter($startDate, $endDate)
    {
        // Creates DateTime objects
        $startDate  = new \DateTime($startDate);
        $endDate    = new \DateTime($endDate);

        // Calculates the difference between DateTime objects
        $interval = $startDate->diff($endDate);
        
        // Get the months
        $months = ($interval->y * 12) + $interval->m;

        return $months;
    }
}