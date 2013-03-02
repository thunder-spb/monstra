<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Edit album', 'gallery'); ?></h2><br />

        <?php
        if (Notification::get('success'))
            Alert::success(Notification::get('success'));

        foreach($errors as $item)
        {
            Alert::error($item);
        }

        $sections = $post['sections'] == 0 ? false : true;

        echo Form::open(null, array('class' => 'form_validate'));
        echo Form::hidden('csrf', Security::token());
        echo Form::hidden('gallery_id', $gallery_id);
        echo (
            '<div class="row-fluid show-grid">'.
                '<div class="span6">'.
                Form::label('gallery_title', __('Title', 'gallery')).
                Form::input('gallery_title', $post['title'], array('class' => 'required span12')).

                Form::label('gallery_slug', __('Alias (slug)', 'gallery')).
                Form::input('gallery_slug', $post['slug'], array('class' => 'required span12')).

                Form::label('gallery_limit', __('Items per page (website)', 'gallery')).
                Form::input('gallery_limit', $post['limit'], array('class' => 'required span12')).

                Form::label('gallery_keywords', __('Keywords', 'gallery')).
                Form::input('gallery_keywords', $post['keywords'], array('class' => 'span12')).

                Form::label('gallery_description', __('Description', 'gallery')).
                Form::textarea('gallery_description', $post['description'], array('class' => 'span12')).
                '</div>'.
                '<div class="span6">'.
                Form::label('width_thumb', __('Width thumbnails (px)', 'gallery')).
                Form::input('width_thumb', $post['w'], array('class' => 'required span12')).

                Form::label('height_thumb', __('Height thumbnails (px)', 'gallery')).
                Form::input('height_thumb', $post['h'], array('class' => 'required span12')).

                Form::label('width_orig', __('Original width (px, max)', 'gallery')).
                Form::input('width_orig', $post['wmax'], array('class' => 'required span12')).

                Form::label('height_orig', __('Original height (px, max)', 'gallery')).
                Form::input('height_orig', $post['hmax'], array('class' => 'required span12')).

                Form::label('resize', __('Resize', 'gallery')).
                Form::select('resize', GalleryAdmin::$resize, $post['resize']).

                Form::label('gallery_sections', __('Sections', 'gallery')).
                Form::checkbox('gallery_sections', '0', $sections).

                '</div>'.
                '</div>'
        );
        ?>

        <div class="row-fluid">
            <div class="span6">
                <?php
                echo (
                    Form::submit('save_album_and_exit', __('Save and exit', 'gallery'), array('class' => 'btn')).Html::nbsp(2).
                    Form::submit('save_album', __('Save', 'gallery'), array('class' => 'btn')).Html::nbsp(2).
                    Form::submit('reload_album', __('Reload', 'gallery'), array('class' => 'btn',
                        'onclick' => "return confirm('".__("Resize images? Do not forget to save your album before upgrading.", 'gallery')."')"))
                );
                ?>
            </div>
        </div>
        <?php echo Form::close(); ?>
    </div>
</div>