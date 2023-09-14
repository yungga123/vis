<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\AdminTrait;
class Dashboard extends BaseController
{
    /* Declare trait here to use */
    use AdminTrait;

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
        $data['modules']        = $this->_moduleBoxMenu();
        $data['type_legend']    = $this->scheduleTypeLegend();
        $data['schedules']      = $this->getSchedulesForToday(true);

        return view('dashboard/dashboard', $data);
    }

    /**
     * Get the module card menu
     *
     * @return string (html)
     */
    public function _moduleBoxMenu()
    {
        $modules    = $this->modules;
        $arr        = [];

        if (! empty($modules) && is_array($modules)) {
            // Sort modules ascending
            sort($modules);

            $setup_modules = array_keys(setup_modules());

            foreach ($modules as $val) {
                // Not include DASHBOARD module
                
                if ($val !== 'DASHBOARD' && in_array($val, $setup_modules)) {
                    $module = setup_modules($val);
                    $menu   = empty($module['menu']) ? $val : $module['menu'];

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

                    // Store in array based on menu
                    $arr[$menu][] = $card;
                }
            }
        }

        return $this->_cardHtml($arr);
    }

    /**
     * Get the whole card box html
     *
     * @return string (html)
     */
    private function _cardHtml($arr)
    {
        $html = '';    

        if (!empty($arr)) {
            $modules = get_modules();

            foreach ($arr as $key => $val) {
                $box    = implode('', $val);
                $title  = isset($modules[$key]) ? get_modules($key) : get_nav_menus($key)['name'];
                $html   .= <<<EOF
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{$title}</h5>
                            </div>
                            <div class="card-body">
                                {$box}
                            </div>
                        </div>
                    </div>	
                EOF;
           }
        } else $html = '<h2>No module card to be displayed!</h2>';

        return $html;
    }
}