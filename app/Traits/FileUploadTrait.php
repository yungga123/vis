<?php

namespace App\Traits;

trait FileUploadTrait
{
    public $imgExtensions = ['jpeg', 'jpg', 'png', 'gif'];

    /**
     * Get/fetch the uploadeded files in the specified path/source
     * 
     * @param string|integer $id    The unique or primary key or something else
     * @param array $filenames      The array names of the files
     * @param string $filepath      The path to store the file
     * @param string $downloadUrl   The download route of the file
     *
     * @return array
     */
    public function getFiles($id, $filenames, $filepath, $downloadUrl)
    {
        $files = [];

        if (! empty($filenames)) {
            // Get all files in the specified folder
            foreach (new \DirectoryIterator($filepath) as $file) {
                if ($file->isFile() && in_array($file->getFilename(), $filenames)) {
                    $files[] = $this->setFileData($id, $file, '', $downloadUrl, true);
                }
            }
        }

        return $files;
    }

    /**
     * For uploading file
     *
     * @param string|integer $id    The unique or primary key or something else
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
     * @param string|integer $id    The unique or primary key or something else
     * @param object $file          The file object from request
     * @param string $filename      The name of the file
     * @param string $downloadUrl   The download route of the file
     * @param bool $isDirIterator   Whether from \DirectoryIterator or not
     * 
     * @return array                The files       
     */
    public function setFileData($id, $file, $filename, $downloadUrl, $isDirIterator = false)
    {
        $filename   = empty($filename) 
            ? ($isDirIterator ? $file->getFilename() : $file->getClientName()) 
            : $filename;

        $ext        = $isDirIterator ? $file->getExtension() : $file->guessExtension();
        $arr        = [
            '_id'       => $id,
            'name'      => $filename,
            'size'      => $file->getSize(),
            'url'       => $downloadUrl . $id .'/'. $filename,
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