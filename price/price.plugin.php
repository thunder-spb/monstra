<?php

	/**
	 *  Price plugin
	 *
	 *  @package Monstra
	 *  @subpackage Plugins
	 *  @author KANekT
	 *  @copyright 2012 KANekT
	 *  @version 1.0.0
	 *
	 */

	// Register plugin
	Plugin::register( __FILE__,
					__('Price CSV', 'price'),
					__('Get Price from сsv file.', 'price'),
					'0.9.0',
					'KANekT',
					'http://kanekt.ru/');


	// Add new shortcode {price}
	// Example: {price file=price}
	Shortcode::add('price', 'Price::view');

	if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
		// Include Admin
		Plugin::admin('price');

	}
	/**
	 * Price Class
	 */
	class Price {
	
		/**
		 * View Price
		 */
		public static function view($price_file) {
			$return = "";
			if (isset($price_file['file']))
				$file = (string)$price_file['file'];
			else
				return $return;

			$price_file = ROOT . DS . 'public' . DS . 'prices' . DS . $file . '.csv';
			if (File::exists($price_file)) {
				$handle = fopen($price_file,"r");
				$return .= "<table>";
				while ($data = fgetcsv($handle, 1000, ";"))
				{
					$num = count($data);
					$return .= "<tr>";
					for ($c=0; $c < $num; $c++)
					{
						$return .= "<td>".$data[$c] . "</td>";
					}
					$return .= "</tr>";
				}
				$return .= "</table>";
				fclose ($handle);
			}
			return $return;
		}
	}
