<h2><?php echo __('Settings dev', 'dev');?></h2>

<?php

echo (
    Form::open().
        Form::hidden('csrf', Security::token()).
        '<div class="row-fluid show-grid">'.
        '<div class="span3">'.
        Form::label('valid_frontend', __('Valid frontend', 'dev')).
        Form::input('valid_frontend', Option::get('dev_valid_frontend')).
        Form::label('valid_backend', __('Valid backend', 'dev')).
        Form::input('valid_backend', Option::get('dev_valid_backend')).
        Form::label('fancy_frontend', __('Fancy frontend', 'dev')).
        Form::input('fancy_frontend', Option::get('dev_fancy_frontend')).
        Form::label('fancy_backend', __('Fancy backend', 'dev')).
        Form::input('fancy_backend', Option::get('dev_fancy_backend')).
        Form::label('submit_settings', __('', 'dev')).
        Form::submit('dev_submit_settings', __('Save', 'dev'), array('class' => 'btn')).
        '</div>'.
        '<div class="span3">'.
        Form::label('date_frontend', __('Date frontend', 'dev')).
        Form::input('date_frontend', Option::get('dev_date_frontend')).
        Form::label('date_backend', __('Date backend', 'dev')).
        Form::input('date_backend', Option::get('dev_date_backend')).
        Form::label('file_upload', __('File upload', 'dev')).
        Form::input('file_upload', Option::get('dev_file_upload')).
        '</div>'.
        '</div>'.
        Form::close()
);
?>