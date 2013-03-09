<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

// Add New Options
Option::add(
    array(
        'dev_valid_frontend'        => 0,
        'dev_valid_backend'         => 0,
        'dev_fancy_frontend'        => 0,
        'dev_fancy_backend'         => 0,
        'dev_migrate_frontend'      => 0,
        'dev_migrate_backend'       => 0,
        'dev_date_frontend'         => 0,
        'dev_date_backend'          => 0,
        'dev_file_upload'           => 0,
        'dev_bootstrap_file_upload' => 0,
    )
);