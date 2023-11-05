<?php

namespace App\Traits;

use App\Models\GeneralInfoModel;

trait GeneralInfoTrait
{
    /**
     * Fetching/searching job order by quotation number
     *
     * @param string|array|null $param   The param to search
     * @return array|null                The results of the search
     */
    public function getGeneralInfo($param = [])
    {
        $model = new GeneralInfoModel();

        if(is_array($param)) return $model->fetchAll($param);
        
        $result = $model->fetch($param);
        return $result ? $result[0]['value'] : null;
    }
}