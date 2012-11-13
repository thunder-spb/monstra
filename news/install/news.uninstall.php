<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Delete Options
    Option::delete('news_template');
    Option::delete('news_limit');
    Option::delete('news_limit_admin');
    
    Table::drop('news');
    
    function newsRemoveDir($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? newsRemoveDir($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }
    
    newsRemoveDir(ROOT . DS . 'storage' . DS . 'news');
    