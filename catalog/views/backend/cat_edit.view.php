<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Edit catalog: :catalog', 'catalog', array(':catalog' => $post['title'])); ?></h2><br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));

        foreach($errors as $item)
        {
            Alert::error($item);
        }

        echo Form::open(null, array('class' => 'form_validate','enctype' => 'multipart/form-data'));
        echo Form::hidden('csrf', Security::token());
        echo Form::hidden('catalog_id', $post['cid']);
        ?>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#catalog" data-toggle="tab"><?php echo __('Item', 'catalog'); ?></a></li>
            <li><a href="#seo" data-toggle="tab"><?php echo __('SEO', 'catalog'); ?></a></li>
            <li><a href="#img" data-toggle="tab"><?php echo __('Image', 'catalog'); ?></a></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog"><?php echo __('Return to Index', 'catalog'); ?></a></li>
        </ul>
        <div class="tab-content tab-page">
            <div class="tab-pane active" id="catalog">
                <?php
                echo (
                    Form::label('catalog_title', __('Title', 'catalog')).
                        Form::input('catalog_title', $post['title'], array('class' => 'required span12')).

                        Form::label('catalog_slug', __('Alias (slug)', 'catalog')).
                        Form::input('catalog_slug', $post['slug'], array('class' => 'required span12')).

                        Form::label('catalog_tag', __('Tag', 'catalog')).
                        Form::select('catalog_tag', $opt['tags'], $post['tags'])
                );
                ?>
            </div>
            <div class="tab-pane" id="seo">
                <?php
                echo (
                    Form::label('catalog_description', __('Description', 'catalog')).
                        Form::input('catalog_description', $post['description'], array('class' => 'span12')).

                        Form::label('catalog_keywords', __('Keywords', 'catalog')).
                        Form::input('catalog_keywords', $post['keywords'], array('class' => 'span12'))
                );
                ?>
            </div>
            <div class="tab-pane" id="img">
                <div class="row-fluid">
                    <div class="span4">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail image" style="width: 200px; height: 150px;">
                                <?php
                                if(file::exists($opt['dir'].'cat_'.$post['cid'].'.jpg')):
                                ?>
                                    <a href="#" rel="<?php echo $opt['url'].'cat_'.$post['cid'].'.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].'thumbnail/'.'cat_'.$post['cid'].'.jpg' ?>"></a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="btn btn-file">
                                    <span class="fileupload-new"><?php echo __('Select image', 'catalog'); ?></span>
                                    <span class="fileupload-exists"><?php echo __('Change', 'catalog'); ?></span>
                                    <?php echo Form::file('file')?></span>
                                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo __('Remove', 'catalog'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <?php Action::run('admin_editor', array(Html::toText($post['content']))); ?>
        <div class="row-fluid">
            <div class="span6">
                <?php
                echo (
                    Form::submit('edit_catalog_and_exit', __('Save and exit', 'catalog'), array('class' => 'btn')).Html::nbsp(2).
                        Form::submit('edit_catalog', __('Save', 'catalog'), array('class' => 'btn'))
                );
                ?>
            </div>
        </div>
        <?php echo Form::close(); ?>
    </div>
</div>
<div id="previewLightbox" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class='lightbox-header'>
        <button type="button" class="close" data-dismiss="lightbox" aria-hidden="true">&times;</button>
    </div>
    <div class='lightbox-content'>
        <img />
    </div>
</div>