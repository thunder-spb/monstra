<?php

/**
 *  Dev Js plugin
 *
 *  @package Monstra
 *  @subpackage Plugins
 *  @copyright Copyright (C) KANekT @ http://kanekt.ru
 *  @license http://creativecommons.org/licenses/by-nc/3.0/
 *  Creative Commons Attribution-NonCommercial 3.0
 *  Donate Web Money Z104136428007 R346491122688
 *  Yandex Money 410011782214621
 *
 */


// Register plugin
Plugin::register( __FILE__,
    __('Dev', 'dev'),
    __('Developer Helper plugin for Monstra', 'dev'),
    '1.4.1',
    'KANekT',
    'http://kanekt.ru/'
);

    // Load Sandbox Admin for Editor and Admin

    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {

        Plugin::admin('dev');

    }
	
    if (Registry::exists('dev_valid_frontend'))
    {
        Javascript::add('plugins/dev/js/validate.js', 'frontend', 11);
    }
	
    if (Registry::exists('dev_valid_backend'))
    {
        Javascript::add('plugins/dev/js/validate.js', 'backend', 11);
    }

    if (Registry::exists('dev_date_frontend'))
    {
        Javascript::add('plugins/dev/js/datepicker.js', 'frontend', 11);
        Stylesheet::add('plugins/dev/css/datepicker.css', 'frontend',11);
    }
	
    if (Registry::exists('dev_date_backend'))
    {
        Javascript::add('plugins/dev/js/datepicker.js', 'backend', 11);
        Stylesheet::add('plugins/dev/css/datepicker.css', 'backend',11);
    }

    if (Registry::exists('dev_file_upload'))
    {
        Stylesheet::add('plugins/dev/css/jquery.fileupload-ui.css', 'backend',15);
        Javascript::add('plugins/dev/js/vendor/jquery.ui.widget.js', 'backend', 15);
        Javascript::add('plugins/dev/js/jquery.iframe-transport.js', 'backend', 16);
        Javascript::add('plugins/dev/js/jquery.fileupload.js', 'backend', 17);
        Javascript::add('plugins/dev/js/jquery.fileupload-ui.js', 'backend', 18);
    }

    if (Registry::exists('dev_bootstrap_file_upload'))
    {
        Stylesheet::add('plugins/dev/css/bootstrap-fileupload.min.css', 'backend',18);
        Javascript::add('plugins/dev/js/bootstrap-fileupload.min.js', 'backend', 18);
        Javascript::add('plugins/dev/js/bootstrap-fileupload-setting.js', 'backend', 19);
    }

    if (Registry::exists('dev_fancy_frontend'))
    {
        Javascript::add('plugins/dev/js/jquery.fancybox.pack.js', 'frontend', 15);
        Javascript::add('plugins/dev/js/jquery.fancybox-media.js', 'frontend', 16);
        Stylesheet::add('plugins/dev/css/jquery.fancybox.css', 'frontend',15);

        Javascript::add('plugins/dev/js/script.js', 'frontend', 17);
    }
	
    if (Registry::exists('dev_fancy_backend'))
    {
        Javascript::add('plugins/dev/js/jquery.fancybox.pack.js', 'backend', 15);
        Stylesheet::add('plugins/dev/css/jquery.fancybox.css', 'backend',15);

        Javascript::add('plugins/dev/js/script.js', 'backend', 17);
    }

    if (Registry::exists('dev_migrate_frontend'))
    {
        Javascript::add('plugins/dev/js/jquery-migrate.min.js', 'frontend', 5);
    }
	
    if (Registry::exists('dev_migrate_backend'))
    {
        Javascript::add('plugins/dev/js/jquery-migrate.min.js', 'backend', 5);
    }

    if (Registry::exists('dev_responsiveslides'))
    {
        Javascript::add('plugins/dev/js/responsiveslides.min.js', 'frontend', 25);
        Stylesheet::add('plugins/dev/css/responsiveslides.css', 'frontend', 25);
    }

class Dev extends Frontend {

    /**
     * current page
     * pages all
     * site_url
     * limit pages
     */
    public static function paginator($current, $pages, $urls, $sections = 1, $limit_pages=10) {

        $content = '';
        if (is_array($urls))
        {
            $url = $urls[0];
            $req = $urls[1];
        }
        else{
            $url = $urls;
            $req = '';
        }
        if ($pages > 1) {

            // pages count > limit pages
            if ($pages > $limit_pages) {
                $start = ($current <= 6) ? 1 : $current-3;
                $finish = (($pages-$limit_pages) > $current) ? ($start + $limit_pages - 1) : $pages;
            } else {
                $start = 1;
                $finish = $pages;
            }

            // pages list
            $content .= '<div class="pagination"><ul>';

            // next
            if($current!=$pages && $sections > 0)
            {
                $content .= '<li><a href="'.$url.($current+1).$req.'">'.__('Next', 'dev').'</a></li>';
            }

            if (($pages > $limit_pages) and ($current > 6)) {
                $content .= '<li><a href="'.$url.'1'.$req.'">1</a></li>';
            }

            for ($i = $start; $i <= $finish; $i++) {
                $class = ($i == $current) ? ' class="active"' : '';
                $content .= '<li '.$class.'><a href="'.$url.$i.$req.'">'.$i.'</a></li>';
            }

            if (($pages > $limit_pages) && ($current < ($pages - $limit_pages))) {
                $content .= '<li><a href="'.$url.$pages.$req.'">'.$pages.'</a></li>';
            }

            // prev
            if($current!=1 && $sections > 0)
            {
                $content .= '<li><a href="'.$url.($current-1).$req.'">'.__('Prev', 'dev').'</a></li>';
            }
            $content .= '</ul></div>';
        }
        return $content;
    }
}