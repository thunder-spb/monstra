<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

// Delete Options
Option::delete('article_template');
Option::delete('article_limit');
Option::delete('article_limit_admin');
Option::delete('article_w');
Option::delete('article_h');
Option::delete('article_wmax');
Option::delete('article_hmax');
Option::delete('article_resize');

Table::drop('articles');

function RemoveDir($dir) {
    if ($objs = glob($dir."/*")) {
        foreach($objs as $obj) {
            is_dir($obj) ? RemoveDir($obj) : unlink($obj);
        }
    }
    rmdir($dir);
}

RemoveDir(ROOT . DS . 'storage' . DS . 'articles');
RemoveDir(ROOT . DS . 'public' . DS . 'uploads' . DS . 'articles' . DS);