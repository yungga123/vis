<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\TaskLeadFileModel;
use App\Traits\FileUploadTrait;

class TaskLeadBookedFile extends BaseController
{
    /* Declare trait here to use */
    use FileUploadTrait;

    /**
     * Use to initialize PermissionModel class
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
        $this->_model           = new TaskLeadFileModel(); // Current model
        // File directory
        $this->_rootDirPath     = $this->setRootFileUploadsDirPath();
        // Please don't change this
        $this->_initialFilePath = 'uploads/project-booked/';
        $this->_fullFilePath    = $this->_rootDirPath . $this->_initialFilePath;
    }

    /**
     * Get the uploaded files
     * 
     * @param int $id   The tasklead_id
     *
     * @return object|array
     */
    public function fetchFiles($id)
    {
        $record         = $this->_model->getTaskleadFiles($id);
        $filenames      = empty($record) ? [] : json_decode($record['filenames']);
        $directory       = $this->_fullFilePath . $id;
        $downloadUrl    = site_url('tasklead/booked/files/download/') . $id;
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
        $data           = [
            'status'    => STATUS_SUCCESS,
            'message'   => 'File(s) have successfully uploaded!'
        ];
        $response       = $this->customTryCatch(
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
                $filepath       = $this->_fullFilePath . $id;
                $filenames      = [];
                $newFiles       = [];

                foreach ($files['file'] as $file) {
                    $filename       = time() .'-'. $file->getClientName();
                    $downloadUrl    = site_url('tasklead/booked/files/download/') . $id .'/'. $filename;
                    $newFiles[]     = $this->uploadFile($id, $file, $filename, $filepath, $downloadUrl);
                    $filenames[]    = $filename;
                }

                // Add the uploaded new files in the response
                $data['files']      = $newFiles;
                $data['id']         = $id;

                if (! empty($filenames)) {
                    $record     = $this->_model->getTaskleadFiles($id);
                    $fileNames  = ! empty($record) ? json_decode($record['filenames']) : [];
                    $inputs     = [
                        'tasklead_id'   => $id,
                        'filenames'    => json_encode(array_merge($fileNames, $filenames)),
                        'created_by'    => session('username')
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
            'status'    => STATUS_SUCCESS,
            'message'   => 'File has been removed!'
        ];
        $response   = $this->customTryCatch(
            $data,
            function($data) {
                $id             = $this->request->getVar('id');
                $filename       = $this->request->getVar('filename');
        
                $record         = $this->_model->getTaskleadFiles($id);
                $filenames      = empty($record) ? [] : json_decode($record['filenames']);
                $newFilenames   = [];
                
                if (count($filenames) > 1) {                    
                    foreach ($filenames as $_filename) {
                        if ($_filename != $filename) 
                            $newFilenames[] = $_filename;
                    }
                }

                if (! empty($newFilenames) || count($filenames) === 1) {
                    $inputs = [
                        'tasklead_id'   => $id,
                        'filenames'    => json_encode($newFilenames),
                        'created_by'    => session('username')
                    ];
        
                    // Insert or update if $id exists
                    if (! $this->_model->upsert($inputs)) {
                        $data['errors']     = $this->_model->errors();
                        $data['status']     = STATUS_ERROR;
                        $data['message']    = 'Validation error!';
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
