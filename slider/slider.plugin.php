<?php

    /**
     *  Slider plugin
     *
     *  @package Monstra
     *  @subpackage Plugins
	 *  @copyright Copyright (C) KANekT @ http://kanekt.ru
	 *  @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
	 *  Donate Web Money Z104136428007 R346491122688
     *
     */


    // Register plugin
    Plugin::register( __FILE__,                    
                    __('Slider', 'slider'),
                    __('Slider plugin for Monstra', 'slider'),  
                    '0.1.0',
                    'KANekT',
                    'http://kanekt.ru/');

    // Load Slider Admin for Editor and Admin
    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {        
        Plugin::admin('slider');
    }
    
    if (!BACKEND){
		Stylesheet::add('plugins/slider/content/flexslider.css', 'frontend', 11);
		Javascript::add('plugins/slider/content/jquery.flexslider-min.js', 'frontend', 11);
		$files = File::scan(STORAGE . DS . 'slider', 'js');
		foreach ($files as $name) {
			Javascript::add('storage' . DS . 'slider' . DS . $name, 'frontend', 12);
		}
	}

	// Add new shortcode {slider}
	// Example: {slider cat=test}
	Shortcode::add('slider', 'Slider::show');

    class Slider extends Frontend {
		public static function show($attributes) {
			$return = "";
			if (isset($attributes['cat']))
			{
				$cat = $attributes['cat'];
				if(File::exists(STORAGE . DS . 'slider' . DS . $cat . '.txt'))
				{
					$return = Text::toHtml(File::getContent(STORAGE . DS . 'slider' . DS . $cat . '.txt'));
				}
			}
			Javascript::add(STORAGE . DS . 'slider' . DS . $cat . '.js', 'frontend', 11);
			return $return;
		}
    }
