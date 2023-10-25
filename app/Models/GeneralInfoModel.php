<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneralInfoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'general_info';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['key', 'value'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function singleSave($data, $additionalUpdateFields = [])
    {
        $builder = $this->db->table($this->table);
        $builder->setData($data);

        if (! empty($additionalUpdateFields)) {
            $builder->updateFields($additionalUpdateFields, true);
        }

        $builder->upsert();
    }

    public function multipleSave($data)
    {
        $builder = $this->db->table($this->table);
        $builder->setData($data);

        if (! empty($additionalUpdateFields)) {
            $builder->updateFields($additionalUpdateFields, true);
        }

        $builder->upsertBatch();
    }

    /**
     * For fetching single data
     *
     * @param string $key    the key/param to search
     * @return array
     */
    public function fetch(string $key)
    {
        $builder = $this->select($this->allowedFields);
        $builder->where('deleted_at IS NULL');
        $builder->where('key', $key);

        return $builder->find();
    }

    /**
     * For fetching multiple data
     *
     * @param array $key   the key/param to search
     * @return array
     */
    public function fetchAll(array $key = [])
    {
        $builder = $this->select($this->allowedFields);
        $builder->where('deleted_at IS NULL');

        if (! empty($key)) $builder->whereIn('key', $key);

        return $builder->findAll();
    }
}
