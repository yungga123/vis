<?php

namespace App\Traits;

use App\Models\GeneralInfoModel;

trait GeneralInfoTrait
{
    /**
     * The root directory to save the uploaded files
     * Change it desired directory path
     * Example F:\files
     * @var string
     */
    protected $rootDirPath = FCPATH;

    /**
     * The initial file path after the root path
     * @var string
     */
    protected $initialFilePathLogo = 'uploads/logo/';

    /**
     * Fetching the general info
     *
     * @param string|array|null $param  The param to search
     * @param bool $format              Whether to format the result
     * 
     * @return array|string|null        The results of the search
     */
    public function getGeneralInfo($param = [], $format = false)
    {
        $model = new GeneralInfoModel();

        if(is_array($param)) {            
            return $format ? 
                format_results($model->fetchAll($param)) 
                : $model->fetchAll($param);
        }
        
        $result = $model->fetch($param);
        return $result ? $result['value'] : null;
    }

    /**
     * Get company info
     *
     * @return array
     */
    public function getCompanyInfo()
    {
        $info = $this->getGeneralInfo(['company_logo', 'company_name', 'company_address'], true);

        $info['company_logo']       = ($info['company_logo'] ?? null)
            ? base_url($this->initialFilePathLogo . $info['company_logo']) : '';
        $info['company_name']       = ($info['company_name'] ?? null) 
            ? $info['company_name'] : COMPANY_NAME;
        $info['company_address']    = ($info['company_address'] ?? null) 
            ? $info['company_address'] : COMPANY_ADDRESS;

        return $info;
    }

    /**
     * Get company logo
     *
     * @param string $filename  Optional filename
     * 
     * @return string           The full path of the logo
     */
    public function getCompanyLogo($filename = '') 
    {
        if (empty($filename)) {
            $filename = $this->getGeneralInfo('company_logo');
        }

        return empty($filename) 
            ? '' : base_url($this->initialFilePathLogo . $filename);
    }

    /**
     * The full file path
     *
     * @return string
     */
    public function fullFilePathLogo() 
    {
        return $this->rootDirPath . $this->initialFilePathLogo;
    }
}