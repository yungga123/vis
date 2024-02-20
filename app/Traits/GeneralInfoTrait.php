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
     * @param array $info
     * @param bool $keys  Whether to return the keys only
     * 
     * @return array
     */
    public function getCompanyInfo($info = [], $keys = false)
    {
        $_keys = [
			'company_logo',
			'company_name',
			'company_address',
			'company_contact_number',
			'company_email_address',
			'company_bank_name',
			'company_bank_account_name',
			'company_bank_account_number',
			'company_bank_branch',
        ];

        if ($keys) return $_keys;

        $info = empty($info) ? $this->getGeneralInfo($_keys, true) : $info;

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
     * Get company funds
     *
     * @return float    The current funds amount
     */
    public function getCompanyFunds() 
    {
        return floatval($this->getGeneralInfo('company_funds', true) ?? 0);
    }

    /**
     * Save (insert/update) company funds
     * 
     * @param int|float $funds 
     * @param bool $plus        Plus or minus funds
     *
     * @return array|bool|null
     */
    public function saveCompanyFunds($funds, $plus = true) 
    {
        $model  = new GeneralInfoModel();
        $curr   = $this->getCompanyFunds();
        $funds  = floatval($funds);
        $data   = [
            'key'           => 'company_funds',
            'value'         => $plus ? $funds + $curr : $curr - $funds,
            'updated_by'    => session('username'),
        ];

        if (! $plus && $curr < $funds) return false;

        return $model->singleSave($data);
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

    /**
     * Get vat percent
     *
     * @return float    The current vat percent
     */
    public function getVatPercent(bool $decimal = true)
    {
        $percent = floatval($this->getGeneralInfo('vat_percent', true) ?? 12);
        $percent = empty($percent) ? 12 : $percent; // Default value is 12% VAT.

        if (! $decimal) return $percent;

        return $percent / 100;
    }
}