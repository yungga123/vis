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
        $data['modules']        = $this->moduleBoxMenu();

        return view('dashboard/dashboard', $data);
    }

    /**
     * Get the module card menu
     *
     * @return string (html)
     */
    public function moduleBoxMenu()
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

        $hr_html        = '';
        $sales_html     = '';
        $settings_html  = '';

        if (! empty($modules) && is_array($modules)) {
            // Sort modules ascending
            sort($modules);

            foreach ($modules as $val) {
                // Not include DASHBOARD module
                if ($val !== 'DASHBOARD') {
                    $module = setup_modules($val);

                    // Add module card menu
                    $card = <<<EOF
                        <div class="small-box bg-success">
                            <div class="inner"><h4>{$module['name']}</h4></div>
                            <div class="icon"><i class="{$module['icon']}"></i></div>
                            <a href="{$module['url']}" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    EOF;

                    switch ($module['menu']) {
                        case 'HUMAN_RESOURCE':
                            $hr_html .= $card;
                            break;
                        case 'SALES':
                            $sales_html .= $card;
                            break;
                        case 'SETTINGS':
                            $settings_html .= $card;
                            break;
                    }
                }
            }
        } else {
            $html = '<h2>No module card to be displayed!</h2>';
        }

        return (! empty($html)) ? $html : [
            'hr_modules'        => $hr_html,
            'sales_modules'     => $sales_html,
            'settings_modules'  => $settings_html,
        ];
    }
}
