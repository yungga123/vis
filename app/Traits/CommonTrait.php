<?php

namespace App\Traits;

trait CommonTrait
{
    /**
     * Restricting the edit/delete of the record when under specific status(es)
     *
     * @param int|null $id      The id to search
     * @param object $model     The model object to check from
     * 
     * @return \Exception|void
     */
    public function checkRecordRestrictionViaStatus($id, $model)
    {
        if (empty($id)) return;
        
        $builder = $model->select('status');
        $builder->where('deleted_at IS NULL');
        $builder->whereIn('status', $model->restrictedStatuses);

        $result = $builder->find($id);
        if ($result) {
            $status = strtoupper(str_replace('_', ' ', $result['status']));
            throw new \Exception("
                <strong>EDIT</strong> or <strong>DELETE</strong> action for this record are not allowed! It's already in <strong>{$status}</strong> status.
            ", 2);
        }
    }
}