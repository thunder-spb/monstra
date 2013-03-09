<h2><?php echo __('Settings dev', 'dev');?></h2>

<?php

echo (
    Form::open().
        Form::hidden('csrf', Security::token()).
        '<div class="row-fluid show-grid">'.
        '<div class="span3">'.
        Form::label('valid_frontend', __('Valid frontend', 'dev')).
        Form::input('valid_frontend', Option::get('dev_valid_frontend'), array('type' => 'number', 'min' => '0')).
        Form::label('valid_backend', __('Valid backend', 'dev')).
        Form::input('valid_backend', Option::get('dev_valid_backend'), array('type' => 'number', 'min' => '0')).
        Form::label('jquery_migrate_frontend', __('jQuery Migrate frontend', 'dev')).
        Form::input('jquery_migrate_frontend', Option::get('dev_migrate_frontend'), array('type' => 'number', 'min' => '0')).
        Form::label('jquery_migrate_backend', __('jQuery Migrate backend', 'dev')).
        Form::input('jquery_migrate_backend', Option::get('dev_migrate_backend'), array('type' => 'number', 'min' => '0')).
        Form::label('file_upload', __('File upload', 'dev')).
        Form::input('file_upload', Option::get('dev_file_upload'), array('type' => 'number', 'min' => '0')).
        Form::label('submit_settings', __('', 'dev')).
        Form::submit('dev_submit_settings', __('Save', 'dev'), array('class' => 'btn')).
        '</div>'.
        '<div class="span3">'.
        Form::label('date_frontend', __('Date frontend', 'dev')).
        Form::input('date_frontend', Option::get('dev_date_frontend'), array('type' => 'number', 'min' => '0')).
        Form::label('date_backend', __('Date backend', 'dev')).
        Form::input('date_backend', Option::get('dev_date_backend'), array('type' => 'number', 'min' => '0')).
        Form::label('fancy_frontend', __('Fancy frontend', 'dev')).
        Form::input('fancy_frontend', Option::get('dev_fancy_frontend'), array('type' => 'number', 'min' => '0')).
        Form::label('fancy_backend', __('Fancy backend', 'dev')).
        Form::input('fancy_backend', Option::get('dev_fancy_backend'), array('type' => 'number', 'min' => '0')).
        Form::label('bootstrap_file_upload', __('Bootstrap File upload', 'dev')).
        Form::input('bootstrap_file_upload', Option::get('dev_bootstrap_file_upload'), array('type' => 'number', 'min' => '0')).
        '</div>'.
        '</div>'.
        Form::close()
);
?>