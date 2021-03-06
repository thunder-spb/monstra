<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

// Delete Options
Option::delete('gallery_template');
Option::delete('gallery_limit');
Option::delete('gallery_limit_admin');
Option::delete('gallery_w');
Option::delete('gallery_h');
Option::delete('gallery_wmax');
Option::delete('gallery_hmax');
Option::delete('gallery_resize');

Table::drop('gal_items');
Table::drop('gal_folder');

function RemoveDir($dir) {
    if ($objs = glob($dir."/*")) {
        foreach($objs as $obj) {
            is_dir($obj) ? RemoveDir($obj) : unlink($obj);
        }
    }
    rmdir($dir);
}

RemoveDir(ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS);
RemoveDir(STORAGE . DS . 'gallery' . DS);