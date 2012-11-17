<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

	Option::delete('fr_template');
	Table::drop('slider_cat');
	Table::drop('slider_img');