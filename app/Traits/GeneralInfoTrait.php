<?php

namespace App\Traits;

use App\Models\GeneralInfoModel;

trait GeneralInfoTrait
{
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
}