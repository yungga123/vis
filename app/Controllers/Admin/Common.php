<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TaskLeadView;
use App\Traits\AdminTrait;

class Common extends BaseController
{
    /* Declare trait here to use */
    use AdminTrait;

    /* Search task lead booked by quotation */
    public function searchQuotation()
    {
        $options = $this->request->getVar('options') ?? [];

        $result = $this->findBookedTaskLeadsByQuotation(
            (new TaskLeadView),
            $this->request->getVar('q'),
            $options
        );

        return $this->response->setJSON($result);
    }
}
