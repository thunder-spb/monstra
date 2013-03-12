<h2><?php echo __('Settings gallery', 'gallery');?></h2>

<?php
echo (
    Form::open().
        Form::hidden('csrf', Security::token()).
        Form::hidden('limit_admin', Option::get('gallery_limit_admin')).
        '<div class="row-fluid show-grid">'.
        '<div class="span4">'.
        Form::label('width_thumb', __('Width thumbnails (px)', 'gallery')).
        Form::input('width_thumb', Option::get('gallery_w')).
        Form::label('height_thumb', __('Height thumbnails (px)', 'gallery')).
        Form::input('height_thumb', Option::get('gallery_h')).
        '</div>'.
        '<div class="span4">'.
        Form::label('width_orig', __('Original width (px, max)', 'gallery')).
        Form::input('width_orig', Option::get('gallery_wmax')).
        Form::label('height_orig', __('Original height (px, max)', 'gallery')).
        Form::input('height_orig', Option::get('gallery_hmax')).
        '</div>'.
        '<div class="span4">'.
        Form::label('limit', __('Items per page (website)', 'gallery')).
        Form::input('limit', Option::get('gallery_limit')).
        Form::label('resize', __('Resize', 'gallery')).
        Form::select('resize', GalleryAdmin::$resize, Option::get('gallery_resize')).
        '</div>'.
        '</div>'.
        Form::label('editor', __('Description', 'gallery'))
    );
        Action::run('admin_editor', array(Text::toHtml(File::getContent(STORAGE . DS . 'gallery' . DS. 'album.0.txt'))));
echo (
        Form::submit('gallery_submit_settings', __('Save', 'gallery'), array('class' => 'btn')).Html::Nbsp(2).
        Form::submit('gallery_submit_settings_cancel', __('Cancel', 'gallery'), array('class' => 'btn')).

        Form::close()
);

?>