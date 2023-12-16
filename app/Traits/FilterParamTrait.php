<?php

namespace App\Traits;

trait FilterParamTrait
{
    /**
     * Filter params based on request and provided model/table
     *
     * @param array $request        The form request
     * @param mixed $builder        The database builder or model
     * @param string $columnName    The column name - 
     * if join with other table include the table name as prefix (eg. table.column)
     * @param string $fieldName     The form field name of param like status
     * 
     * @return object|\CodeIgniter\Model::class $builder
     */
    public function filterParam($request, $builder, $columnName = 'status', $fieldName = 'status')
    {
        if (isset($request['params']) && !empty($request['params'])) {
            $params = $request['params'];
            $table  = $builder->getTable();
            $column = strpos($columnName, '.') === false 
                ? $table .'.'. $columnName : $columnName; 
            
            if (isset($params[$fieldName]) && !empty($params[$fieldName])) {
                $field  = "UPPER(TRIM({$column}))";
                $search = $this->_convertValue(clean_param($params[$fieldName], 'strtoupper'));

                if (is_array($search))
                    $builder->whereIn($field, $search);
                else 
                    $builder->where($field, strtoupper($search));
            }
        }
        return $builder;
    }

    /**
     * Convert param value bas
     *
     * @param array|string $param The param to be converted
     * 
     * @return array|string
     */
    private function _convertValue($param)
    {
        $convert = [
            'zero' => 0,
            'null' => null,
        ];

        if (is_array($param)) {
            $arr = [];
            foreach ($param as $key => $val) {
                if (isset($convert[strtolower($val)])) 
                    $arr[$key] = $convert[strtolower($val)];
                else $arr[$key] = $val;
            }

            return $arr;
        }

        return isset($convert[strtolower($param)]) 
            ? $convert[strtolower($param)] : $param;
    }
}