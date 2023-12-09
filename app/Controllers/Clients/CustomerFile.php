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
     * @var string
     */
    private $_rootDirPath;

    /**
     * The initial file path after the root path
     * @var string
     */
    private $_initialFilePath;
 
    /**
     * The full file path
     * @var string
     */
    private $_fullFilePath;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_model           = new CustomerFileModel(); // Current model
        $this->_rootDirPath     = $this->setRootFileUploadsDirPath();
        // Please don't change this
        $this->_initialFilePath = 'uploads/customers/';
        $this->_fullFilePath    = $this->_rootDirPath . $this->_initialFilePath;
    }

    /**
     * Get the uploaded files
     * 
     * @param int $id   The customer_id
     *
     * @return string|array
     */
    public function fetchFiles($id)
    {
        $record         = $this->_model->getCustomerFiles($id);
        $filenames      = empty($record) ? [] : json_decode($record['file_names']);
        $directory       = $this->_fullFilePath . $id;
        $downloadUrl    = site_url('clients/files/download/') . $id;
        $files          = $this->getFiles($id, $filenames, $directory, $downloadUrl);

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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.uploaded', 'File(s)')
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $rules = $this->validationFileRules('file', null, 15);
            
                if (! $this->validate($rules)) {
                    $data['errors']     = $this->validator->getErrors();
                    $data['status']     = res_lang('status.error');
                    $data['message']    = res_lang('error.validation');

                    return $data;
                } 

                $id             = $this->request->getVar('id');
                $files          = $this->request->getFiles();
                $filepath       = $this->_fullFilePath . $id;
                $filenames      = [];
                $newFiles       = [];

                foreach ($files['file'] as $file) {
                    $filename       = time() .'-'. $file->getClientName();
                    $downloadUrl    = site_url('clients/files/download/') . $id .'/'. $filename;
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
                        'created_by'    => session('username')
                    ];

                    // Insert or update if $id exists
                    if (! $this->_model->upsert($inputs)) {
                        $data['errors']     = $this->_model->errors();
                        $data['status']     = res_lang('status.error');
                        $data['message']    = res_lang('error.validation');

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
        $filepath   = $this->_fullFilePath . $id .'/'. $filename;
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
            'status'    => res_lang('status.success'),
            'message'   => res_lang('success.removed', 'File')
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
                        'created_by'    => session('username')
                    ];
        
                    // Insert or update if $id exists
                    if (! $this->_model->upsert($inputs)) {
                        $data['errors']     = $this->_model->errors();
                        $data['status']     = res_lang('status.error');
                        $data['message']    = res_lang('error.validation');
                        return $data;
                    }
        
                    // Remove previous uploaded file
                    $file = $this->_fullFilePath . $id .'/'. $filename;
                    $this->removeFile($file);
                }
                return $data;
            },
            true
        );

        return $response;
    }
}
