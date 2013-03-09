<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

// Add New Options
Option::add('catalog_template', 'index');
Option::add('catalog_limit', 7);
Option::add('catalog_limit_admin', 10);
Option::add('catalog_currency', '$');
Option::add('catalog_w', 165);
Option::add('catalog_h', 100);
Option::add('catalog_wmax', 900);
Option::add('catalog_hmax', 800);
Option::add('catalog_resize', 'crop');

// Add tables
Table::create('cat_items', array('title', 'short', 'price', 'currency', 'h1', 'description', 'keywords', 'date', 'author', 'status', 'catalog', 'hits'));

Table::create('cat_folder', array('title', 'description', 'keywords', 'slug', 'parent'));

// Add directory for content
$dir = ROOT . DS . 'storage' . DS . 'catalog' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

// Add directory for content
$dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

File::copy(ROOT . DS . 'plugins' . DS . 'catalog'. DS . 'img' . DS .'noimage.jpg' , $dir.'no_item.jpg');

$dir = $dir . 'thumbnail' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

$valid = (int)Option::get('dev_valid_backend');
$valid++;
Option::update('dev_valid_backend', $valid);