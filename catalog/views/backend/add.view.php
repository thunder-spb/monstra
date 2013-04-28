<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('New item: :catalog', 'catalog', array(':catalog' => $opt['title'])); ?></h2><br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));

        echo Form::open(null, array('class' => 'form_validate','enctype' => 'multipart/form-data', 'id' => 'cat_valid'));
        echo Form::hidden('csrf', Security::token());
        echo Form::hidden('cat_id', $opt['cid']);
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('catalog')) { ?>class="active"<?php } ?>><a href="#catalog" data-toggle="tab"><?php echo __('Item', 'catalog'); ?></a></li>
            <li <?php if (Notification::get('seo')) { ?>class="active"<?php } ?>><a href="#seo" data-toggle="tab"><?php echo __('SEO', 'catalog'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'catalog'); ?></a></li>
            <li <?php if (Notification::get('img')) { ?>class="active"<?php } ?>><a href="#img" data-toggle="tab"><?php echo __('Image', 'catalog'); ?></a></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog&action=items&catalog_id=<?php echo $opt['cid']; ?>"><?php echo __('Return to Cat', 'catalog'); ?></a></li>
        </ul>

        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('catalog')) { ?>active<?php } ?>" id="catalog">
                <?php
                echo (
                    Form::label('catalog_title', __('Title', 'catalog')).
                        Form::input('catalog_title', $post['title'], array('class' => 'required span8')).

                        Form::label('catalog_price', __('Price', 'catalog')).
                        Form::input('catalog_price', $post['price'], array('class' => 'required digits span4')).
                        Form::input('catalog_currency', $post['currency'], array('class' => 'required span2'))
                );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('seo')) { ?>active<?php } ?>" id="seo">
                <?php
                echo (
                        Form::label('catalog_h1', __('H1', 'catalog')).
                        Form::input('catalog_h1', $post['h1'], array('class' => 'span8')).

                        Form::label('catalog_keywords', __('Keywords', 'catalog')).
                        Form::input('catalog_keywords', $post['keywords'], array('class' => 'span8')).

                        Form::label('catalog_description', __('Description', 'catalog')).
                        Form::textarea('catalog_description', $post['description'], array('class' => 'span8'))
                );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
                <div class="row-fluid">
                    <div class="span4">
                        <?php
                        echo (
                            Form::label('status', __('Status', 'catalog')).
                                Form::select('catalog_status', $opt['status'], 'published')
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?php if (Notification::get('img')) { ?>active<?php } ?>" id="img">
                <div class="row-fluid">
                    <div class="span4">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
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
        <?php echo Form::label('catalog_short', __('Short', 'catalog')).Form::textarea('catalog_short', Html::toText($post['short']), array('class' => 'required', 'style' => 'width: 100%; height: 100px;')); ?>
        <?php Action::run('admin_editor', array(Html::toText($post['content']))); ?>
        <br />

        <div class="row-fluid">
            <div class="span6">
                <?php
                echo (
                    Form::submit('add_item_and_exit', __('Save and exit', 'catalog'), array('class' => 'btn')).Html::nbsp(2).
                        Form::submit('add_item', __('Save', 'catalog'), array('class' => 'btn'))
                );
                ?>
            </div>
            <div class="span6">
                <div class="pull-right"><?php echo __('Published', 'catalog'); ?>: <?php echo Form::input('catalog_date', $opt['date'], array('class' => 'input-large')); ?></div>
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>