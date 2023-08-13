<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestPurchaseFormModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'request_purchase_forms';
    protected $view             = 'rpf_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'remarks',
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
        'inventory_id' => [
            'rules' => 'required|if_exist',
            'label' => 'item'
        ],
        'quantity_in' => [
            'rules' => 'required|if_exist',
            'label' => 'quantity in'
        ],
        'received_date' => [
            'rules' => 'permit_empty|if_exist',
            'label' => 'received date'
        ],
        'delivery_date' => [
            'rules' => 'required|if_exist',
            'label' => 'delivery date'
        ],
        'remarks' => [
            'rules' => 'permit_empty|if_exist'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedBy'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = ['updateInventoryStock'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Set the value for created_by before inserting
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Set the value for 'status_' by and at before updating status
    protected function setStatusByAndAt(array $data)
    {
        if (isset($data['data']['status'])) {
            $status = $data['data']['status'];
            $data['data'][$status .'_by'] = session('username');
            $data['data'][$status .'_at'] = current_datetime();
        }
        
        return $data;
    }

    // Update inventory stock after item out
    public function updateInventoryStock(array $data)
    {
        if ($data['result'] && isset($data['data']['status'])) {
            if ($data['data']['status'] === 'receive') {
                $rpfItemModel   = new RPFItemModel();
                $columns        = "
                    {$rpfItemModel->table}.inventory_id, 
                    {$rpfItemModel->table}.quantity_in,
                    inventory.stocks
                ";
                $record         = $rpfItemModel->getRpfItemsByPrfId($data['id'], $columns, false, true);
                $action         = 'ITEM_OUT';

                if (! empty($record)) {
                    $logs_data = [];
                    foreach ($record as $val) {
                        $this->traitUpdateInventoryStock($val['inventory_id'], $val['quantity_in'], $action);
                        $logs_data[] = [
                            'inventory_id'  => $val['inventory_id'],
                            'stocks'        => $val['quantity_in'],
                            'parent_stocks' => $val['stocks'],
                            'action'        => $action,
                            'created_by'    => session('username'),
                        ];
                    }

                    // Add inventory logs
                    $this->saveInventoryLogs($logs_data);
                }
            }
        }

        return $data;
    }

    // Join with prf_view
    public function joinView($builder)
    {
        $builder->join($this->view, "{$this->table}.id = {$this->view}.rpf_id");
    }

    // Get project request forms
    public function getRequestPurchaseForms($id = null, $joinView = false, $columns = '')
    {
        $columns = $columns ? $columns : $this->columns(true, $joinView);
        $builder = $this->select($columns);

        if ($joinView) $this->joinView($builder);
        $builder->where("{$this->table}.deleted_at", null);

        return $id ? $builder->find($id) : $builder->findAll();
    }
}
