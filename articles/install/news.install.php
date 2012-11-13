<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Add New Options
    Option::add('news_template', 'index');
    Option::add('news_limit', 7);
    Option::add('news_limit_admin', 10);
    
    // Add table
    $fields = array('name', 'title', 'h1', 'description', 'keywords', 'slug', 'date', 'author', 'status', 'hits');
    Table::create('news', $fields);
    
    // Add directory for content
    $dir = ROOT . DS . 'storage' . DS . 'news' . DS;
    if(!is_dir($dir)) mkdir($dir, 0755);