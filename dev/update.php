<?php

    // Main engine defines    
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', rtrim(dirname(__FILE__), '\\/'));
    define('BACKEND', false);    
    define('MONSTRA_ACCESS', true);

    // Load bootstrap file
    require_once(ROOT . DS . 'monstra' . DS . 'bootstrap.php');

// Add New Options
Option::add(
    array(
        'dev_migrate_frontend'      => 0,
        'dev_migrate_backend'       => 0,
        'dev_bootstrap_file_upload' => 0
    )
);

    echo 'Done!';