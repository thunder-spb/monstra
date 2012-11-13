<h2><?php echo __('Settings news', 'news');?></h2>

<?php      
echo (
    Form::open().
    
    Form::label('limit', __('News per page (website)', 'news')).
    Form::input('limit', Option::get('news_limit')).
    
    Form::label('limit_admin', __('News per page (admin)', 'news')).
    Form::input('limit_admin', Option::get('news_limit_admin')).
      
    Form::hidden('csrf', Security::token()).Html::Br().
    Form::submit('news_submit_settings', __('Save', 'news'), array('class' => 'btn')).Html::Nbsp(2).
    Form::submit('news_submit_settings_cancel', __('Cancel', 'news'), array('class' => 'btn')).
    Form::close()
);
?>