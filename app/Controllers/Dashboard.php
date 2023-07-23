<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    /**
     * Display the index view
     *
     * @return view
     */
    public function index()
    {
        $data['title']          = 'Dashboard';
        $data['page_title']     = 'Dashboard';
        $data['exclude_toastr'] = true;
        $data['module_cards']   = $this->moduleCardMenu();

        return view('dashboard/dashboard', $data);
    }

    /**
     * Get the module card menu
     *
     * @return string (html)
     */
    public function moduleCardMenu()
    {
        $html       = '';
        $modules    = $this->modules;
        $bgColor    = [
            'bg-info', 
            'bg-primary', 
            'bg-warning', 
            'bg-danger', 
            'bg-secondary', 
        ];

        if (! empty($modules) && is_array($modules)) {
            // Sort modules ascending
            sort($modules);
            
            $count = 0;
            foreach ($modules as $val) {
                // Not include DASHBOARD module
                if ($val !== 'DASHBOARD') {
                    $module = setup_modules($val);

                    if ($count === 0 || $count === 4) {
                        $html .= $count === 4 ? '</div><div class="row">' : '<div class="row">';
                        $count = 0;
                    }

                    // Add module card menu
                    $html .= <<<EOF
                        <div class="col-lg-3 col-6" id="{$val}">
                            <div class="small-box bg-success">
                                <div class="inner"><h4>{$module['name']}</h4></div>
                                <div class="icon"><i class="{$module['icon']}"></i></div>
                                <a href="{$module['url']}" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    EOF;
                    $count += 1;
                    shuffle($bgColor);
                }
            }
        } else {
            $html = '<h3>No module card to be displayed!</h3>';
        }

        return $html;
    }
}
