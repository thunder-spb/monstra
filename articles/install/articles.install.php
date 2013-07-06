<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Add New Options
    Option::add('article_template', 'index');
    Option::add('article_limit', 7);
    Option::add('article_limit_admin', 10);
    Option::add('article_w', 165);
    Option::add('article_h', 100);
    Option::add('article_wmax', 900);
    Option::add('article_hmax', 800);
    Option::add('article_resize', 'crop');
    
    // Add table
    $fields = array('slug','robots_index', 'robots_follow', 'title', 'parent', 'status', 'template', 'access', 'expand', 'description', 'keywords', 'author', 'date', 'hits', 'tags');
    Table::create('articles', $fields);

    // Add directory for content
    $dir = ROOT . DS . 'storage' . DS . 'articles' . DS;
    if(!is_dir($dir)) mkdir($dir, 0755);

// Add directory for content
$dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'articles' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

File::copy(ROOT . DS . 'plugins' . DS . 'articles'. DS . 'img' . DS .'noimage.jpg' , $dir.'no_item.jpg');

$dir = $dir . 'thumbnail' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);
