<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryDropdownModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inventory_dropdowns';
    protected $primaryKey       = 'dropdown_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'dropdown',
        'dropdown_type',
        'other_category_type',
        'parent_id',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'dropdown' => [
            'rules' => 'required',
            'label' => 'description'
        ],
        'dropdown_type' => [
            'rules' => 'required',
            'label' => 'category'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['beforeInsert'];
    protected $beforeInsertBatch   = ['beforeInsertBatch'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    protected $otherCategoryTypes = ['BRAND', 'SIZE', 'UNIT'];

    // Set the value for created_by before inserting
    protected function beforeInsert(array $data)
    {
        $data['data']['created_by'] = session('employee_id');

        return $data;
    }

    // Set the value for created_by before inserting
    protected function beforeInsertBatch(array $data)
    {
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['created_by'] = session('employee_id');
        }

        return $data;
    }
    
    // Saving dropdowns
    public function saveDropdowns($inputs) 
    {
        $dropdowns = explode(',', $inputs['dropdown']);
        if (! empty($inputs['is_category']) || count($dropdowns) <= 1) {
            return $this->save($inputs);
        } else {
            if (count($dropdowns) > 1) {
                $data = [];
                foreach ($dropdowns as $key => $val) {
                    $data[$key] = [
                        'dropdown'      => $val,
                        'dropdown_type' => $inputs['dropdown_type'],
                        'parent_id'     => $inputs['parent_id'],
                    ];
                }

                return $this->insertBatch($data);
            }
        }        
   }
    
   // Save  other category types
   public function saveOtherCategoryTypes($inputs) 
   {
        $id         = 0;
        $other_type = strtoupper($inputs['other_category_type']);
        $record     = $this->select('dropdown_id, dropdown')
                        ->where('other_category_type', $other_type)
                        ->where('parent_id', 0)->first();

        if (empty($record)) {
            $this->save([
                'dropdown'              => $other_type,
                'dropdown_type'         => 'CATEGORY',
                'other_category_type'   => $other_type,
                'parent_id'             => 0,
            ]);

            $id = $this->insertID;
        } else $id = $record['dropdown_id'];

        $dropdowns  = explode(',', $inputs['dropdown']);
        if (count($dropdowns) <= 1) {
            $data = [
                'dropdown'              => $inputs['dropdown'],
                'dropdown_type'         => $other_type,
                'other_category_type'   => $other_type,
                'parent_id'             => $id,
            ];
            return $this->save($data);
        } else {
            $data = [];
            foreach ($dropdowns as $key => $val) {
                $data[$key] = [
                    'dropdown'              => $val,
                    'dropdown_type'         => $other_type,
                    'other_category_type'   => $other_type,
                    'parent_id'             => $id,
                ];
            }
            return $this->insertBatch($data);
        }
    }
    
    // Get specific dropdown base on type
    public function getDropdowns($param, $columns = null, $all_categories = false) 
    {
        $param          = remove_string($param, 'other__');
        $is_category    = ($param === 'CATEGORY' || $param == 0);
        $columns        = $columns ?? 'dropdown_id, dropdown';
        $field          = is_numeric($param) ? 'parent_id' : 'dropdown_type';
        $builder        = $this->select($columns);
        
        if(is_array($param)) {
            $field = is_numeric($param[0]) ? 'parent_id' : $field;
            $builder->whereIn($field, $param);
        }
        else $builder->where($field, $param);
        
        if ($is_category && !$all_categories) $builder->where('other_category_type', '');            
        return $builder->findAll();
   }
    
   // Get unique dropdown types
    public function getDropdownTypes($param = null) 
    {
        $columns = 'dropdown_type, parent_id';
        if ($param) {
            $field = is_numeric($param) ? 'parent_id' : 'dropdown_type';
            $builder = $this->select($columns)->where($field, $param)
                        ->orderBy('parent_id')->distinct()->findAll();
        } else {
            $builder = $this->select($columns)->orderBy('parent_id')
                        ->distinct()->findAll();
        }
            
        return $builder;
    }
    
    // Get the other category type dropdown base on type or all
    public function getOtherCategoryTypes($param = null, $columns = null) 
    {
        $columns = $columns ?? 'dropdown_id, dropdown, dropdown_type, parent_id';
        $builder = $this->select($columns);
        
        if($param) {
            $builder->where("other_category_type", $param);
            $builder->where("parent_id != 0");
        } else
            $builder->where("other_category_type IS NOT NULL OR other_category_type != ''");

        return $builder->findAll();
   }
    
    // Get specific dropdown base on type
    public function categoryHasDropdowns($param, $columns = null) 
    {
        $columns = $columns ?? ['dropdown_id'] + $this->allowedFields;
        $field = is_numeric($param) ? 'parent_id' : 'dropdown_type';
        $builder = $this->select($columns)->where($field, $param)->findAll();
            
        return $builder;
   }
    
    // For DataTables
    public function noticeTable($filter) 
    {
        $builder = $this->db->table($this->table);
        $builder->select('dropdown_id, dropdown, dropdown_type');
            
        if ($filter) {
            $builder->whereIn('parent_id', $filter);
        }

        $builder->where('deleted_at', null);
            
        return $builder;
   }

   public function buttons($permissions)
   {
        $id = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions);
            return $buttons;
        };
        
        return $closureFun;
   }
}
