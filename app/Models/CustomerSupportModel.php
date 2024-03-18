<?php

namespace App\Models;

use App\Traits\ClientTrait;
use App\Traits\FilterParamTrait;
use CodeIgniter\Model;

class CustomerSupportModel extends Model
{
    /* Declare trait here to use */
    use ClientTrait, FilterParamTrait;

    protected $DBGroup          = 'default';
    protected $table            = 'customer_supports';
    protected $view             = 'customer_supports_view';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_id',
        'customer_branch_id',
        'ticket_number',
        'issue',
        'findings',
        'action',
        'troubleshooting',
        'security_ict_system',
        'security_ict_system_other',
        'priority',
        'due_date',
        'follow_up_date',
        'remarks',
        'status',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'customer_id'     => [
            'rules' => 'if_exist|required',
            'label' => 'task lead',
        ],
        'customer_branch_id'   => [
            'rules' => 'permit_empty',
            'label' => 'client branch',
        ],
        'ticket_number'   => [
            'rules' => 'permit_empty|max_length[100]',
            'label' => 'ticket number',
        ],
        'issue'   => [
            'rules' => 'if_exist|required',
            'label' => 'problem/issue encountered',
        ],
        'findings'   => [
            'rules' => 'if_exist|required',
            'label' => 'findings',
        ],
        'action'   => [
            'rules' => 'permit_empty',
            'label' => 'initial action taken by the customer',
        ],
        'troubleshooting'   => [
            'rules' => 'permit_empty',
            'label' => 'initial troubleshooting done?',
        ],
        'security_ict_system'   => [
            'rules' => 'if_exist|required|max_length[100]',
            'label' => 'type of security and ict system',
        ],
        'security_ict_system_other'   => [
            'rules' => 'if_exist|permit_empty|max_length[100]',
        ],
        'priority'   => [
            'rules' => 'if_exist|required',
            'label' => 'priority',
        ],
        'due_date'     => [
            'rules' => 'if_exist|required',
            'label' => 'due date',
        ],
        'follow_up_date'   => [
            'rules' => 'if_exist|required',
            'label' => 'date need to follow-up?',
        ],
        'remarks'   => [
            'rules' => 'if_exist|required',
            'label' => 'remarks',
        ],
        'status'   => [
            'rules' => 'if_exist|required|max_length[50]',
            'label' => 'status',
        ],
        'specialists'   => [
            'rules' => 'if_exist|required',
            'label' => 'support specialist/s',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedByValue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setStatusByAndAt'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Custom variables
    // Restrict edit/delete action for this statuses
    protected $restrictedStatuses = ['done', 'turn_over'];

    /**
     * Set the value for created_by before inserting
     */
    protected function setCreatedByValue(array $data)
    {
        $data['data']['created_by'] = session('username');

        return $data;
    }

    /**
     * Set the value for 'status_' by and at before updating status
     */
    protected function setStatusByAndAt(array $data)
    {
        $not_in = ['pending', 'create-jo'];
        $status = $data['data']['status'] ?? '';

        if (! empty($status) && ! in_array($status, $not_in)) {
            $data['data'][$status .'_by'] = session('username');
            $data['data'][$status .'_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    /**
     * For counting records
     */
    public function countRecords($param = null)
    {
        $builder = $this->where('deleted_at IS NULL');

        if (! $param) return $builder->countAllResults();

        return $builder->where('LOWER(status)', strtolower($param))->countAllResults();        
    }

    /**
     * Make amount_paid required
     */
    public function makeSecICTSystemRequired()
    {
        $this->validationRules['amount_paid']['rules'] = 'if_exist|required|max_length[100]';
    }

    /**
     * Join with customer_supports_view
     */
    public function joinView($builder = null) 
    {
        $builder = $builder ?? $this;
        
        $builder->join($this->view, "{$this->view}.customer_support_id = {$this->table}.id", 'left');

        return $builder;
    }

    /**
     * For dataTables
     */
    public function noticeTable($request) 
    {
        $builder        = $this->db->table($this->table);
        $columns        = "
            {$this->table}.id,
            {$this->table}.status,
            {$this->view}.client_name,
            {$this->view}.client_branch_name,
            {$this->table}.ticket_number,
            {$this->table}.issue,
            {$this->table}.findings,
            {$this->table}.action,
            {$this->table}.troubleshooting,
            IF({$this->table}.security_ict_system = 'OTHER', {$this->table}.security_ict_system_other, {$this->table}.security_ict_system) AS security_ict_system,
            {$this->table}.priority,
            ".dt_sql_date_format("{$this->table}.due_date")." AS due_date,
            ".dt_sql_date_format("{$this->table}.follow_up_date")." AS follow_up_date,
            {$this->table}.remarks,
            {$this->view}.specialists_formatted,
            {$this->view}.created_by,
            ".dt_sql_datetime_format("{$this->table}.created_at")." AS created_at,
            {$this->view}.done_by,
            ".dt_sql_datetime_format("{$this->table}.done_at")." AS done_at,
            {$this->view}.turn_over_by,
            ".dt_sql_datetime_format("{$this->table}.turn_over_at")." AS turn_over_at
        ";

        $builder->select($columns);

        // Join with views
        $this->joinView($builder);

        // Filters
        $this->filterParam($request, $builder);
        $this->filterParam($request, $builder, 'security_ict_system', 'security_ict_system');
        $this->filterParam($request, $builder, 'priority', 'priority');

        $builder->where("{$this->table}.deleted_at IS NULL");
        $builder->orderBy("{$this->table}.id", 'DESC');

        return $builder;
    }

    /**
     * DataTable action buttons
     */
    public function buttons($permissions)
    {
        $id         = $this->primaryKey;
        $dropdown   = false;
        $closureFun = function($row) use($id, $permissions, $dropdown) {
            $buttons = dt_button_actions($row, $id, $permissions, $dropdown);

            if (check_permissions($permissions, 'CHANGE')) {
                $onclick = <<<EOF
                    onclick="change({$row[$id]}, '{$row['status']}')" title="Change Status"
                EOF;
                $buttons .= dt_button_html([
                    'text'      => $dropdown ? 'Change' : '',
                    'button'    => 'btn-primary',
                    'icon'      => 'fas fa-exchange-alt',
                    'condition' => $onclick,
                ], $dropdown);
            }

            return dt_buttons_dropdown($buttons);
        };
        
        return $closureFun;
    }

    /**
     * DataTable status formatter
     */
    public function dtStatusFormat()
    {
        $closureFun = function($row) {
            $text    = get_customer_support_status($row['status']);
            $color   = dt_status_color($row['status']);

            return text_badge($color, $text);
        };
        
        return $closureFun;
    }
}