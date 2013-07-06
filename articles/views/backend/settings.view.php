<h2><?php echo __('Settings articles', 'articles');?></h2>

<?php
$resize = array(
    'width'   => __('Respect to the width', 'articles'),
    'height'  => __('Respect to the height', 'articles'),
    'crop'    => __('Similarly, cutting unnecessary', 'articles'),
    'stretch' => __('Similarly with the expansion', 'articles'),
);
echo (
    Form::open().
        '<div class="row-fluid show-grid">'.
        '<div class="span3">'.
        Form::label('limit', __('Articles per page (website)', 'articles')).
        Form::input('limit', Option::get('article_limit')).
        Form::label('width_thumb', __('Width thumbnails (px)', 'articles')).
        Form::input('width_thumb', Option::get('article_w')).
        Form::label('height_thumb', __('Height thumbnails (px)', 'articles')).
        Form::input('height_thumb', Option::get('article_h')).
        Form::label('resize', __('Resize', 'articles')).
        Form::select('resize', $resize, Option::get('article_resize')).Html::Br().
        Form::submit('article_submit_settings', __('Save', 'articles'), array('class' => 'btn')).Html::Nbsp(2).
        Form::submit('article_submit_settings_cancel', __('Cancel', 'articles'), array('class' => 'btn')).
        '</div>'.
        '<div class="span3">'.
        Form::label('limit_admin', __('Articles per page (admin)', 'articles')).
        Form::input('limit_admin', Option::get('article_limit_admin')).
        Form::label('width_orig', __('Original width (px, max)', 'articles')).
        Form::input('width_orig', Option::get('article_wmax')).
        Form::label('height_orig', __('Original height (px, max)', 'articles')).
        Form::input('height_orig', Option::get('article_hmax')).
        Form::hidden('csrf', Security::token()).Html::Br(3).
        '</div>'.
        '</div>'.
        Form::close()
);
?>