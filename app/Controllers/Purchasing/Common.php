<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Traits\PurchasingTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use PurchasingTrait;

    /* Search Job Order by quotation number */
    public function searchSuppliers()
    {
        // d($this->fetchSuppliers(
        //     '',
        //     [
        //         'page' => 1,
        //         'perPage' => 10
        //     ]
        // ));
        try {
            $options = $this->request->getVar('options') ?? [];
            $results = $this->fetchSuppliers(
                $this->request->getVar('q'),
                $options
            );

            return $this->response->setJSON($results);
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
        }
    }
}
