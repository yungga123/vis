<?php

namespace App\Controllers\Clients;

use App\Models\CustomerFileModel;
use App\Traits\FileUploadTrait;

class CustomerFile extends Customer
{
    /* Declare trait here to use */
    use FileUploadTrait;

    /**
     * Use to initialize model class
     * @var object
     */
    private $_model;

    /**
     * The root directory to save the uploaded files
     * @var array
     */
    private $_rootDirPath;

    /**
     * The initial file path after the root path
     * @var array
     */
    private $_initialFilePath;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_model           = new CustomerFileModel(); // Current model
        // Change it desired directory path
        // Example F:\files
        $this->_rootDirPath     = WRITEPATH;
        // Please don't change this
        $this->_initialFilePath = 'uploads/customers/';
    }

    /**
     * Get the uploaded files
     * 
     * @param int $id       The customer_id
     *
     * @return string|array
     */
    public function fetchFiles($id)
    {
        $record         = $this->_model->getCustomerFiles($id);
        $filenames      = empty($record) ? [] : json_decode($record['file_names']);
        $filepath       = $this->_getFullFilePath() .'/'. $id;
        $downloadUrl    = site_url('clients/files/download/');
        $files          = $this->getFiles($id, $filenames, $filepath, $downloadUrl);

        return $this->response->setJSON(['files' => $files]);
    }

    /**
     * For uploading files
     *
     * @return json
     */
    public function upload()
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'File(s) have successfully uploaded!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $rules = $this->validationFileRules('file', null, 15);
            
                if (! $this->validate($rules)) {
                    $data['status']     = STATUS_ERROR;
                    $data['errors']     = $this->validator->getErrors();
                    $data['message']    = 'Validation error!';

                    return $data;
                } 

                $id             = $this->request->getVar('id');
                $files          = $this->request->getFiles();
                $filepath       = $this->_getFullFilePath() . $id;
                $filenames      = [];
                $newFiles       = [];

                foreach ($files['file'] as $file) {
                    $filename       = time() .'-'. $file->getClientName();
                    $downloadUrl    = site_url('clients/files/download/');
                    $newFiles[]     = $this->uploadFile($id, $file, $filename, $filepath, $downloadUrl);
                    $filenames[]    = $filename;
                }

                // Add the uploaded new files in the response
                $data['files'] = $newFiles;

                if (! empty($filenames)) {
                    $record     = $this->_model->getCustomerFiles($id);
                    $fileNames  = ! empty($record) ? json_decode($record['file_names']) : [];
                    $inputs     = [
                        'customer_id'   => $id,
                        'file_names'    => json_encode(array_merge($fileNames, $filenames)),
                    ];

                    // Insert or update if $id exists
                    if (! $this->_model->upsert($inputs)) {
                        $data['errors']     = $this->_model->errors();
                        $data['status']     = STATUS_ERROR;
                        $data['message']    = 'Validation error!';

                        return $data;
                    }
                }

                return $data;
            }
        );

        return $response;
    }

    /**
     * For downloading/displaying files
     *
     * @return mixed
     */
    public function download($id, $filename)
    {
        $filepath   = $this->_getFullFilePath() . $id .'/'. $filename;
        return $this->response->download($filepath, null);
    }

    /**
     * For removing files
     *
     * @return json
     */
    public function remove()
    {
        $data = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'File has been removed!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $filename       = $this->request->getVar('filename');
        
                $record         = $this->_model->getCustomerFiles($id);
                $filenames      = empty($record) ? [] : json_decode($record['file_names']);
                $newFilenames   = [];
                
                if (count($filenames) > 1) {                    
                    foreach ($filenames as $_filename) {
                        if ($_filename != $filename) 
                            $newFilenames[] = $_filename;
                    }
                }

                if (! empty($newFilenames) || count($filenames) === 1) {
                    $inputs = [
                        'customer_id'   => $id,
                        'file_names'    => json_encode($newFilenames),
                    ];
        
                    // Insert or update if $id exists
                    if (! $this->_model->upsert($inputs)) {
                        $data['errors']     = $this->_model->errors();
                        $data['status']     = STATUS_ERROR;
                        $data['message']    = 'Validation error!';
                        return $data;
                    }
        
                    // Remove previous uploaded file
                    $file = $this->_getFullFilePath() . $id .'/'. $filename;
                    $this->removeFile($file);
                }
                return $data;
            },
            true
        );

        return $response;
    }

    /**
     * Get full file path
     *
     * @return string
     */
    private function _getFullFilePath()
    {
        return $this->_rootDirPath . $this->_initialFilePath;
    }
}
