<?php

// Main engine defines
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', rtrim(dirname(__FILE__), '\\/'));
define('BACKEND', false);
define('MONSTRA_ACCESS', true);

// Load bootstrap file
require_once(ROOT . DS . 'engine' . DS . '_init.php');

$news = new Table('news');
$news->addField('tags');
$news->addField('template');
$items = $news->select(null, 'all');
foreach($items as $item)
{
    $data['tags'] = '';
    $data['template'] = 'index';
    $news->updateWhere('[id="'.$item['id'].'"]', $data);
}

echo 'Done!';