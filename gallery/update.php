<?php

// Main engine defines
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', rtrim(dirname(__FILE__), '\\/'));
define('BACKEND', false);
define('MONSTRA_ACCESS', true);

// Load bootstrap file
require_once(ROOT . DS . 'engine' . DS . '_init.php');

$gallery = new Table('gal_items');
$gallery->addField('hits');
$gallery->addField('media');

File::copy(ROOT . DS . 'plugins' . DS . 'gallery'. DS . 'img' . DS .'noimage.jpg' , ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS . 'no_item.jpg');

    echo 'Done!';