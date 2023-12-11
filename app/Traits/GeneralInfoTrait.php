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
     * @return array|string|null        The results of the search
     */
    public function getGeneralInfo($param = [], $format = false)
    {
        $model = new GeneralInfoModel();

        if(is_array($param)) {            
            return $format ? 
                $this->formatResult($model->fetchAll($param)) 
                : $model->fetchAll($param);
        }
        
        $result = $model->fetch($param);
        return $result ? $result['value'] : null;
    }

    /**
     * Get company logo
     *
     * @param string $filename  Optional filename
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
     * Formatting the result into one assoc array
     *
     * @param array $result     The array/result to format
     * @return array            The formatted array
     */
    public function formatResult($result) 
    {
        $arr = [];

        if (! empty($result)) {
            foreach ($result as $key => $value) {
                $arr[$value['key']] = $value['value'];
            }
        }

        return $arr;
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