<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

// Delete Options
Option::delete('catalog_template');
Option::delete('catalog_limit');
Option::delete('catalog_limit_admin');
Option::delete('catalog_currency');
Option::delete('catalog_w');
Option::delete('catalog_h');
Option::delete('catalog_wmax');
Option::delete('catalog_hmax');
Option::delete('catalog_resize');

Table::drop('cat_items');
Table::drop('cat_folder');
Table::drop('cat_tag');

function RemoveDir($dir) {
	if ($objs = glob($dir."/*")) {
		foreach($objs as $obj) {
			is_dir($obj) ? RemoveDir($obj) : unlink($obj);
		}
	}
	rmdir($dir);
}

RemoveDir(ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS);
RemoveDir(ROOT . DS . 'storage' . DS . 'catalog');