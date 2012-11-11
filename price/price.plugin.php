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
					'1.1.0',
					'KANekT',
					'http://kanekt.ru/');


	// Add new shortcode {price}
	// Example: {price file=price.csv}
	Shortcode::add('price', 'Price::view');

	if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
		// Include Admin
		Plugin::admin('price');
	}
	if (!BACKEND){
		Javascript::add('plugins/price/content/jquery.columnfilters.js', 'frontend', 11);
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

			$price_file = ROOT . DS . 'public' . DS . 'prices' . DS . $file;
			if (File::exists($price_file)) {
				$handle = fopen($price_file,"r");
				$first_str = true;
				$return .= "<table class=\"table\" id=\"price\"><thead>";
				while ($data = fgetcsv($handle, 1000, ";"))
				{
					$num = count($data);
					if ($first_str) {
						$return .= "<tr>";
						for ($c=0; $c < $num; $c++)
						{
							$return .= "<th>".$data[$c] . "</th>";
						}
						$return .= "</tr></thead><tboby>";
						$first_str = false;
					}
					else {
						$return .= "<tr>";
						for ($c=0; $c < $num; $c++)
						{
							$return .= "<td>".$data[$c] . "</td>";
						}
						$return .= "</tr>";
					}
				}
				$return .= "</tbody></table>";
				fclose ($handle);
			}
			else{
				echo 'sdf';
			}
			return $return;
		}
	}
