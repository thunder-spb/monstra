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
                    __('Dev', 'dev'),
                    __('Developer JS  plugin for Monstra', 'dev'),  
                    '1.0.0',
                    'KANekT',                 
                    'http://kanekt.ru/',
                    'dev');

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