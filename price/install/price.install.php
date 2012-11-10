<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

	$mdir = ROOT . DS . 'public' . DS . 'prices' . DS;

	if(!is_dir($mdir))
		mkdir($mdir, 0755);
