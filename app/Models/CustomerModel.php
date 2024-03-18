<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\FilterParamTrait;
use App\Traits\HRTrait;

class CustomerModel extends Model
{
    /* Declare trait here to use */
    use FilterParamTrait, HRTrait;

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
        'is_cn_formatted',
        'telephone',
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
            'rules' => 'permit_empty|max_length[50]',
            'label' => 'contact number'
        ],
        'mobile_number' => [
            'rules' => 'required|max_length[50]',
            'label' => 'mobile number'
        ],
        'telephone' => [
            'rules' => 'permit_empty|max_length[50]',
            'label' => 'telephone'
        ],
        'email_address' => [
            'rules' => 'permit_empty|valid_email',
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
    public function columns($dtTable = false)
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
            {$this->table}.telephone,
            {$this->table}.email_address,
            {$this->table}.type,
            {$this->table}.forecast,
            IF({$this->table}.forecast = 0, 'NO', 'YES') AS new_client,
            {$this->table}.source, 
            {$this->table}.notes,
            {$this->table}.referred_by
        ";

        if ($dtTable) {
            $datetimeFormat = dt_sql_datetime_format();
            $addressConcat  = dt_sql_concat_client_address();
            $columns        .= ",
                {$addressConcat},
                DATE_FORMAT({$this->table}.created_at, '{$datetimeFormat}') AS created_at,
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

        $this->joinAccountView($builder, "{$this->table}.created_by");

        $builder->where("{$this->table}.deleted_at IS NULL");

        $this->filterParam($request, $builder, "{$this->table}.forecast", 'new_client');
        $this->filterParam($request, $builder, "{$this->table}.type", 'type');
        $this->filterParam($request, $builder, "{$this->table}.source", 'source');

        $builder->orderBy("{$this->table}.id", 'DESC');
            
        return $builder;
    }

    // DataTable action buttons
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $closureFun = function($row) use($id, $permissions) {
            $buttons = dt_button_actions($row, $id, $permissions);

            if (strtoupper($row['type']) === 'COMMERCIAL') {
                if (check_permissions($permissions, ACTION_ADD)) {
                    $buttons .= <<<EOF
                        <button class="btn btn-sm btn-success" onclick="addBranch({$row["$id"]}, '{$row["name"]}')" title="Add Branch"><i class="fas fa-plus-square"></i> </button> 
                    EOF;
                }
            }

            if (check_permissions($permissions, ACTION_UPLOAD)) {
                $buttons .= <<<EOF
                    <button class="btn btn-sm btn-primary" onclick="upload({$row["$id"]}, '{$row["name"]}')" title="View or Attach Files"><i class="fas fa-paperclip"></i> </button> 
                EOF;
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
    }

    // DataTable view customer's branches
    public function dtViewClientBranches()
    {
         $id         = $this->primaryKey;
         $closureFun = function($row) use($id) {
            $button = '~~N/A~~';

            if (strtoupper($row['type']) === 'COMMERCIAL') {
                $button = <<<EOF
                    <button class="btn btn-sm btn-primary" onclick="branchList({$row["$id"]}, '{$row["name"]}')" title="View Branches"><i class="fas fa-eye"></i> View</button>
                EOF;
            }

            return $button;
        };
        
        return $closureFun;
    }
}
