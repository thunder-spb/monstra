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
Table::create('gal_items', array('title', 'description', 'date', 'author', 'guid'));

Table::create('gal_folder', array('title', 'slug', 'parent', 'description', 'keywords', 'w', 'h', 'wmax', 'hmax', 'resize', 'limit', 'sections'));

// Add directory for content
$dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

$dir = $dir . 'thumbnail' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

$valid = (int)Option::get('dev_valid_backend');
$valid++;
Option::update('dev_valid_backend', $valid);

$upload = (int)Option::get('dev_file_upload');
$upload++;
Option::update('dev_file_upload', $upload);

$fancy = (int)Option::get('dev_fancy_frontend');
$fancy++;
Option::update('dev_fancy_frontend', $fancy);