<?php

namespace App\Traits;

trait ExportTrait
{
    /**
     * Exporting data to csv
     *
     * @param array $data       The query results to export
     * @param array $header     The title header for the csv
     * @param array $filename   The file name of the csv
     * 
     * @return \Exception|void
     */
    public function exportToCsv($data, $header, $filename)
    {
        // Start the output buffer.
        ob_start();

        // Create a file pointer with PHP
        $output = fopen('php://output', 'w');

        // Add CSV header
        fputcsv($output, $header);

        // Loop through the prepared data to output it to CSV file
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
    }
}