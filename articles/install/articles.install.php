<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Add New Options
    Option::add('articles_template', 'index');
    Option::add('articles_limit', 7);
    Option::add('articles_limit_admin', 10);
    
    // Add table
    $fields = array('name', 'title', 'h1', 'description', 'keywords', 'slug', 'date', 'author', 'status', 'hits');
    Table::create('articles', $fields);
    
    // Add directory for content
    $dir = ROOT . DS . 'storage' . DS . 'articles' . DS;
    if(!is_dir($dir)) mkdir($dir, 0755);