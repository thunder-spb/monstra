<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Add New Options
    Option::add('news_template', 'index');
    Option::add('news_limit', 7);
    Option::add('news_limit_admin', 10);
    Option::add('news_w', 165);
    Option::add('news_h', 100);
    Option::add('news_wmax', 900);
    Option::add('news_hmax', 800);
    Option::add('news_resize', 'crop');
    
    // Add table
    $fields = array('slug','robots_index', 'robots_follow', 'title', 'parent', 'status', 'access', 'expand', 'description', 'keywords', 'author', 'date', 'hits');
    Table::create('news', $fields);

    // Add directory for content
    $dir = ROOT . DS . 'storage' . DS . 'news' . DS;
    if(!is_dir($dir)) mkdir($dir, 0755);

    // Add directory for image
    $dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'news' . DS;
    if(!is_dir($dir)) mkdir($dir, 0755);

$valid = (int)Option::get('dev_valid_backend');
$valid++;
Option::update('dev_valid_backend', $valid);