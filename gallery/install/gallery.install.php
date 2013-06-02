<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

// Add New Options
Option::add('gallery_template', 'index');
Option::add('gallery_limit', 8);
Option::add('gallery_limit_admin', 10);
Option::add('gallery_w', 165);
Option::add('gallery_h', 100);
Option::add('gallery_wmax', 900);
Option::add('gallery_hmax', 800);
Option::add('gallery_resize', 'crop');

// Add tables
Table::create('gal_items', array('title', 'description', 'date', 'hits', 'media', 'author', 'guid'));

Table::create('gal_folder', array('title', 'description', 'slug', 'parent', 'keywords', 'w', 'h', 'wmax', 'hmax', 'resize', 'limit', 'sections'));

$dir = STORAGE . DS . 'gallery' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

// Add directory for content
$dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

File::copy(ROOT . DS . 'plugins' . DS . 'gallery'. DS . 'img' . DS .'noimage.jpg' , $dir.'no_item.jpg');

$dir = $dir . 'thumbnail' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);