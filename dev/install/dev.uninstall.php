<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Delete Options
Option::delete('dev_valid_frontend');
Option::delete('dev_valid_backend');
Option::delete('dev_fancy_frontend');
Option::delete('dev_fancy_backend');
Option::delete('dev_date_frontend');
Option::delete('dev_date_backend');
Option::delete('dev_file_upload');
Option::delete('dev_bootstrap_file_upload');