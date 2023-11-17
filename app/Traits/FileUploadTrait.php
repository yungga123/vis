<?php

namespace App\Traits;

trait FileUploadTrait
{
    public $imgExtensions = ['jpeg', 'jpg', 'png', 'gif'];

    /**
     * Set root directory path for file uploads
     *
     * @return string
     */
    public function setRootFileUploadsDirPath()
    {
        // ROOT_FILE_UPLOAD_DIR was from Constants.php file
        // This is the very root path for storing files
        // Set the very root directory path in Constants.php file
        // If empty, default directory path set to writable folder
        return empty(ROOT_FILE_UPLOAD_DIR) 
            ? WRITEPATH : ROOT_FILE_UPLOAD_DIR;
    }

    /**
     * Get/fetch the uploadeded files in the specified path/source
     * 
     * @param string|int|null $id   The unique or primary key or something else
     * @param array $filenames      The array names of the files
     * @param string $directory     The path/directory to store/get the file
     * @param string $downloadUrl   The download route of the file
     *
     * @return array
     */
    public function getFiles($id, $filenames, $directory, $downloadUrl)
    {
        $files = [];

        try {
            if (! empty($filenames)) {
                // Get all files in the specified folder
                $iterator = new \DirectoryIterator ($directory);

                foreach ($iterator as $file) {
                    if ($file->isFile() && in_array($file->getFilename(), $filenames)) {
                        $files[] = $this->setFileData($id, $file, '', $downloadUrl, true);
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', '[DirectoryIterator] {exception}', ['exception' => $e]);
        }

        return $files;
    }

    /**
     * For uploading file and returen the formatted file data
     *
     * @param string|int|null $id   The unique or primary key or something else
     * @param object $file          The file object from request
     * @param string $filename      The name of the file
     * @param string $filepath      The path to store the file
     * @param string $downloadUrl   The download route of the file
     * 
     * @return array                The files
     */
    public function uploadFile($id, $file, $filename, $filepath, $downloadUrl)
    {
        $files = [];

        if (! $file->isValid()) {
            throw new \RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
        }

        if (! $file->hasMoved()) {
            // Get the formatted file data
            $files = $this->setFileData($id, $file, $filename, $downloadUrl);

            // Move files to specified file path
            $file->move($filepath, $filename, true);
        }

        return $files;
    }

    /**
     * For removing file
     *
     * @param string $file  The file full path/source
     * 
     * @return void               
     */
    public function removeFile($file)
    {
        if (file_exists($file)) unlink($file);
    }

    /**
     * For setting file data
     *
     * @param string|int|null $id   The unique or primary key or something else
     * @param object $file          The file object from request
     * @param string $filename      The name of the file
     * @param string $downloadUrl   The download route of the file
     * @param bool $isDirIterator   Whether from \DirectoryIterator or not
     * 
     * @return array                The formatted file data      
     */
    public function setFileData($id, $file, $filename, $downloadUrl, $isDirIterator = false)
    {
        $filename       = empty($filename) 
            ? ($isDirIterator ? $file->getFilename() : $file->getClientName()) 
            : $filename;
        $nfilename      = explode('/', $downloadUrl);
        $nfilename      = array_pop($nfilename);
        $downloadUrl    = $nfilename === $filename ? $downloadUrl : $downloadUrl . '/'. $filename;
        $ext            = $isDirIterator ? $file->getExtension() : $file->guessExtension();
        $arr            = [
            '_id'       => $id,
            'name'      => $filename,
            'size'      => $file->getSize(),
            'url'       => $downloadUrl,
            'ext'       => $ext,
            'accepted'  => true,        
            'status'    => 'uploaded',
            'is_img'    => in_array($ext, $this->imgExtensions),
        ];

        return $arr;
    }

    /**
     * File validation rules
     *
     * @param string $filename      The name of the file
     * @param string $allowed       Allowed file extensions
     * @param int $maxSize          Max size file allowed
     * 
     * @return array                The rules       
     */
    public function validationFileRules($filename, $allowed, $maxSize = 5)
    {
        $default    = 'jpg,jpeg,png,webp,pdf,doc,docx,xlx,xlsx,csv';
        $allowed    = empty($allowed) ? $default : $allowed;
        $sizeToKb   = mb_to_kb((int) $maxSize);
        $rules      = [
            "{$filename}" => [
                'label' => ucwords($filename),
                'rules' => [
                    "uploaded[{$filename}]",
                    "ext_in[{$filename},{$allowed}]",
                    "max_size[{$filename},{$sizeToKb}]",
                ],
                'errors' => [
                    'ext_in' => "File must be {$allowed} only.",
                    'max_size' => "File size must be {$maxSize}mb only.",
                ]
            ]
        ];

        return $rules;
    }
}