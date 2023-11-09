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
}