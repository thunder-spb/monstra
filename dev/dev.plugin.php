<?php

/**
 *  Dev Js plugin
 *
 *  @package Monstra
 *  @subpackage Plugins
 *  @copyright Copyright (C) KANekT @ http://kanekt.ru
 *  @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 *  Donate Web Money Z104136428007 R346491122688
 *  @version 1.0.0
 *
 */


// Register plugin
Plugin::register( __FILE__,
    __('DevJS', 'dev'),
    __('Developer JS  plugin for Monstra', 'dev'),
    '1.2.0',
    'KANekT',
    'http://kanekt.ru/',
    'dev');

    // Load Sandbox Admin for Editor and Admin
    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {

        Plugin::admin('dev');

    }
    if ((int)Option::get('dev_valid_frontend') > 0)
    {
        Javascript::add('plugins/dev/js/validate.js', 'frontend', 11);
    }
    if ((int)Option::get('dev_valid_backend') > 0)
    {
        Javascript::add('plugins/dev/js/validate.js', 'backend', 11);
    }

    if ((int)Option::get('dev_date_frontend') > 0)
    {
        Javascript::add('plugins/dev/js/datepicker.js', 'frontend', 11);
        Stylesheet::add('plugins/dev/css/datepicker.css', 'frontend',11);
    }
    if ((int)Option::get('dev_date_backend') > 0)
    {
        Javascript::add('plugins/dev/js/datepicker.js', 'backend', 11);
        Stylesheet::add('plugins/dev/css/datepicker.css', 'backend',11);
    }

    if ((int)Option::get('dev_file_upload') > 0)
    {
        Stylesheet::add('plugins/dev/css/jquery.fileupload-ui.css', 'backend',15);
        Javascript::add('plugins/dev/js/vendor/jquery.ui.widget.js', 'backend', 15);
        Javascript::add('plugins/dev/js/jquery.iframe-transport.js', 'backend', 16);
        Javascript::add('plugins/dev/js/jquery.fileupload.js', 'backend', 17);
    }

    if ((int)Option::get('dev_fancy_frontend') > 0)
    {
        Javascript::add('plugins/dev/js/jquery.fancybox.pack.js', 'frontend', 15);
        Stylesheet::add('plugins/dev/css/jquery.fancybox.css', 'frontend',15);

        Javascript::add('plugins/dev/js/script.js', 'frontend', 17);
    }
    if ((int)Option::get('dev_fancy_backend') > 0)
    {
        Javascript::add('plugins/dev/js/jquery.fancybox.pack.js', 'backend', 15);
        Stylesheet::add('plugins/dev/css/jquery.fancybox.css', 'backend',15);

        Javascript::add('plugins/dev/js/script.js', 'backend', 17);
    }