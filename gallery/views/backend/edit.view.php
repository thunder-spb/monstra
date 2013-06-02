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

        echo Form::open(null, array('class' => 'form_validate','enctype' => 'multipart/form-data'));
        echo Form::hidden('csrf', Security::token());
        echo Form::hidden('gallery_id', $post['id']);
        echo (
            '<div class="row-fluid show-grid">'.
                '<div class="span4">'.
                Form::label('gallery_title', __('Title', 'gallery')).
                Form::input('gallery_title', $post['title'], array('class' => 'required span12')).

                Form::label('gallery_slug', __('Alias (slug)', 'gallery')).
                Form::input('gallery_slug', $item['slug'], array('class' => 'required span6', 'readonly' => 'readonly')).

                Form::label('gallery_keywords', __('Keywords', 'gallery')).
                Form::input('gallery_keywords', $post['keywords'], array('class' => 'span12')).

                Form::label('gallery_description', __('Description', 'gallery')).
                Form::textarea('gallery_description', $post['description'], array('class' => 'span12')).

                '</div>'.
                '<div class="span4">'.
                Form::label('width_orig', __('Original width (px, max)', 'gallery')).
                Form::input('width_orig', $post['wmax'], array('class' => 'required span12')).

                Form::label('height_orig', __('Original height (px, max)', 'gallery')).
                Form::input('height_orig', $post['hmax'], array('class' => 'required span12')).

                Form::label('gallery_limit', __('Items per page (website)', 'gallery')).
                Form::input('gallery_limit', $post['limit'], array('class' => 'required span12')).
                '
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail" style="width: 200px; height: 50px;">');
                if(file::exists($opt['dir'].'album_'.$post['id'].'.jpg')):
                    ?>
                    <a href="#" rel="<?php echo $opt['url'].'album_'.$post['id'].'.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].'thumbnail/album_'.$post['id'].'.jpg' ?>"></a>
                <?php endif;
                echo ('
                                </div>
                            <div>
                                <span class="btn btn-file">
                                    <span class="fileupload-new">'.__('Select image', 'news').'</span>
                                    <span class="fileupload-exists">'.__('Change', 'news').'</span>

                </span>
                                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">'.__('Remove', 'news').'</a>
                            </div>
                        </div>
                '.
                '</div>'.
                '<div class="span4">'.
                Form::label('width_thumb', __('Width thumbnails (px)', 'gallery')).
                Form::input('width_thumb', $post['w'], array('class' => 'required span12')).

                Form::label('height_thumb', __('Height thumbnails (px)', 'gallery')).
                Form::input('height_thumb', $post['h'], array('class' => 'required span12')).

                Form::label('resize', __('Resize', 'gallery')).
                Form::select('resize', GalleryAdmin::$resize, $post['resize']).

                Form::label('gallery_sections', __('Sections', 'gallery')).
                Form::checkbox('gallery_sections', '0', true).
                '</div>'.
                '</div>'
        );
        Action::run('admin_editor', array(Text::toHtml(File::getContent(STORAGE . DS . 'gallery' . DS. 'album.'.$post['id'].'.txt'))));
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

<div id="upPhoto" class="modal hide">
    <div class="modal-header">
        <a data-dismiss="modal" class="close">×</a>
        <h3><?php echo __('Upload photo', 'gallery') ?></h3>
    </div>
    <div class="modal-body">
        <?php
        echo (
            Form::open(null, array('enctype' => 'multipart/form-data')).
                Form::hidden('csrf', Security::token()).
                Form::hidden('id', $post['id'])
        );
        ?>
        <div class="fileupload fileupload-new" data-provides="fileupload">
            <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>
            <div>
                <span class="btn btn-file">
                    <span class="fileupload-new"><?php echo __('Select image', 'gallery'); ?></span>
                    <span class="fileupload-exists"><?php echo __('Change', 'gallery'); ?></span>
                    <?php echo Form::input('file', null, array('type' => 'file', 'size' => '25'))?></span>
                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo __('Remove', 'gallery'); ?></a>
            </div>
        </div>
        <?php
        echo (
            Form::submit('upload_file', __('Upload', 'gallery'), array('class' => 'btn default')).
                Form::close()
        );
        ?>
    </div>
</div>