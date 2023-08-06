<?php

namespace App\Controllers\Inventory;

use App\Controllers\BaseController;
use App\Traits\InventoryTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use InventoryTrait;

    /* Search task lead booked by Masterlist */
    public function searchMasterlist()
    {
        try {
            $options = $this->request->getVar('options') ?? [];
            $results = $this->fetchMatestlist(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($results);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
        }
    }
}
