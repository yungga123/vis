<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customers';
    protected $accountsView     = 'accounts_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'province',
        'city',
        'barangay',
        'subdivision',
        'contact_person',
        'contact_number',
        'email_address',
        'type',
        'forecast',
        'source', 
        'notes',
        'referred_by',
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
        'name' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'client name'
        ],
        'province' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'province'
        ],
        'city' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'city'
        ],
        'barangay' => [
            'rules' => 'permit_empty|string|min_length[2]|max_length[255]',
            'label' => 'barangay'
        ],
        'subdivision' => [
            'rules' => 'permit_empty|string|min_length[2]|max_length[255]',
            'label' => 'subdivision'
        ],
        'contact_person' => [
            'rules' => 'required|string|min_length[2]|max_length[255]',
            'label' => 'contact person'
        ],
        'contact_number' => [
            'rules' => 'required|string|min_length[6]|max_length[255]',
            'label' => 'contact number'
        ],
        'email_address' => [
            'rules' => 'permit_empty|string|min_length[2]|max_length[255]',
            'label' => 'email address'
        ],
        'type' => [
            'rules' => 'required',
            'label' => 'type'
        ],
        'forecast' => [
            'rules' => 'required',
            'label' => 'new client'
        ],
        'notes' => [
            'rules' => 'permit_empty|string|min_length[5]|max_length[255]',
            'label' => 'notes'
        ],
        'source' => [
            'rules' => 'required',
            'label' => 'source of contact'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Set the value for created_by before inserting
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');
        return $data;
    }

    // Set the columns
    protected function columns($dtTable = false)
    {
        $columns = "
            {$this->table}.id,
            {$this->table}.name,
            {$this->table}.province,
            {$this->table}.city,
            {$this->table}.barangay,
            {$this->table}.subdivision,
            {$this->table}.contact_person,
            {$this->table}.contact_number,
            {$this->table}.email_address,
            {$this->table}.type,
            {$this->table}.forecast,
            IF({$this->table}.forecast = 0, 'NO', 'YES') AS new_client,
            {$this->table}.source, 
            {$this->table}.notes,
            {$this->table}.referred_by
        ";

        if ($dtTable) {
            $addressConcat = $this->customerAddressQueryConcat();
            $columns .= ",
                {$addressConcat},
                DATE_FORMAT({$this->table}.created_at, '%b %e, %Y') AS created_at,
                {$this->accountsView}.employee_name AS created_by
            ";
        }

        return $columns;
    }

    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();
        return $builder->where('type', strtoupper($param))->countAllResults();
        
    }

    // For dataTables
    public function noticeTable($request) 
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->columns(true));
        $builder->join($this->accountsView, "{$this->table}.created_by = {$this->accountsView}.username", 'left');
        $builder->where("deleted_at IS NULL");

        if (isset($request['params'])) {
            $params = $request['params'];

            if (isset($params['new_client']) && $params['new_client'] != '') {
                $builder->where('forecast', $params['new_client']);
            }

            if (isset($params['type']) && !empty($params['type'])) {
                $builder->where('type', $params['type']);
            }

            if (isset($params['source']) && !empty($params['source'])) {
                $builder->whereIn('source', $params['source']);
            }
        }

        $builder->orderBy('id', 'DESC');        
        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions);

            if (strtoupper($row['type']) === 'COMMERCIAL') {
                if (check_permissions($permissions, 'ADD')) {
                    $buttons .= <<<EOF
                        <button class="btn btn-sm btn-success" onclick="addBranch({$row["$id"]}, '{$row["name"]}')" title="Add Branch"><i class="fas fa-plus-square"></i> </button> 
                    EOF;
                }
    
                $buttons .= <<<EOF
                    <button class="btn btn-sm btn-info" onclick="branchList({$row["$id"]}, '{$row["name"]}')" title="View Branch"><i class="fas fa-eye"></i> </button>
                EOF;
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
    }

    // Query concat of customer address
    public function customerAddressQueryConcat()
    {
        return "
            CONCAT(
                IF(province = '' || province IS NULL, '', CONCAT(province, ', ')),
                IF(city = '' || city IS NULL, '', CONCAT(city, ', ')),
                IF(barangay = '' || barangay IS NULL, '', CONCAT(barangay, ', ')),
                IF(subdivision = '', '', subdivision)
            ) AS address
        ";
    }
}
