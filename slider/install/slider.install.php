<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

	Table::create('slider_cat', array('title', 'url', 'sort'));
	Table::create('slider_img', array('img', 'small', 'title', 'cat', 'sort', 'url'));

	// Add directory for content
	$dir = ROOT . DS . 'storage' . DS . 'foto' . DS;
	if(!is_dir($dir)) mkdir($dir, 0755);