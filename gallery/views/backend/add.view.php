<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('New album', 'gallery'); ?></h2><br />

        <?php
        if (Notification::get('success'))
            Alert::success(Notification::get('success'));

        foreach($errors as $item)
        {
            Alert::error($item);
        }

        echo Form::open(null, array('class' => 'form_validate','enctype' => 'multipart/form-data'));
        echo Form::hidden('csrf', Security::token());
        echo (
        '<div class="row-fluid show-grid">'.
            '<div class="span4">'.
            Form::label('gallery_title', __('Title', 'gallery')).
            Form::input('gallery_title', $post['title'], array('class' => 'required span12')).

            Form::label('gallery_slug', __('Alias (slug)', 'gallery')).
            Form::input('gallery_slug', $post['slug'], array('class' => 'required span12')).

            Form::label('gallery_limit', __('Items per page (website)', 'gallery')).
            Form::input('gallery_limit', $post['limit'], array('class' => 'required span12')).

            Form::label('gallery_keywords', __('Keywords', 'gallery')).
            Form::input('gallery_keywords', $post['keywords'], array('class' => 'span12')).

            '</div>'.
            '<div class="span4">'.
            Form::label('width_orig', __('Original width (px, max)', 'gallery')).
            Form::input('width_orig', $post['wmax'], array('class' => 'required span12')).

            Form::label('height_orig', __('Original height (px, max)', 'gallery')).
            Form::input('height_orig', $post['hmax'], array('class' => 'required span12')).

            Form::label('gallery_description', __('Description', 'gallery')).
            Form::textarea('gallery_description', $post['description'], array('class' => 'span12')).

            Form::label('resize', __('Resize', 'gallery')).
            Form::select('resize', GalleryAdmin::$resize, $post['resize']).

            '</div>'.
            '<div class="span4">'.
            Form::label('width_thumb', __('Width thumbnails (px)', 'gallery')).
            Form::input('width_thumb', $post['w'], array('class' => 'required span12')).

            Form::label('height_thumb', __('Height thumbnails (px)', 'gallery')).
            Form::input('height_thumb', $post['h'], array('class' => 'required span12')).

            '
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="fileupload-preview thumbnail" style="width: 200px; height: 100px;">
                    </div>
                    <div>
                        <span class="btn btn-file">
                            <span class="fileupload-new">'.__('Select image', 'news').'</span>
                                    <span class="fileupload-exists">'.__('Change', 'news').'</span>
                                    '.Form::file('gal_file').'</span>
                                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">'.__('Remove', 'news').'</a>
                            </div>
                        </div>
                '.
            '</div>'.
        '</div>'
        );
        Action::run('admin_editor');
        ?>

        <div class="row-fluid">
            <div class="span6">
                <?php
                echo (
                    Form::submit('add_album_and_exit', __('Save and exit', 'gallery'), array('class' => 'btn')).Html::nbsp(2).
                        Form::submit('add_album', __('Save', 'gallery'), array('class' => 'btn'))
                );
                ?>
            </div>
        </div>
        <?php echo Form::close(); ?>
    </div>
</div>