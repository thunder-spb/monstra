<h2><?php echo __('Settings articles', 'articles');?></h2>

<?php      
echo (
    Form::open().
    
    Form::label('limit', __('Articles per page (website)', 'articles')).
    Form::input('limit', Option::get('articles_limit')).
    
    Form::label('limit_admin', __('Articles per page (admin)', 'articles')).
    Form::input('limit_admin', Option::get('articles_limit_admin')).
      
    Form::hidden('csrf', Security::token()).Html::Br().
    Form::submit('articles_submit_settings', __('Save', 'articles'), array('class' => 'btn')).Html::Nbsp(2).
    Form::submit('articles_submit_settings_cancel', __('Cancel', 'articles'), array('class' => 'btn')).
    Form::close()
);
?>