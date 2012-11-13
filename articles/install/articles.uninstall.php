<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Delete Options
    Option::delete('articles_template');
    Option::delete('articles_limit');
    Option::delete('articles_limit_admin');
    
    Table::drop('articles');
    
    function articlesRemoveDir($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? articlesRemoveDir($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }
    
    articlesRemoveDir(ROOT . DS . 'storage' . DS . 'articles');
    