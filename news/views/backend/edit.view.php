<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('Edit news', 'news'); ?></h2>
        <br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));

        echo (
            Form::open(null, array('class' => 'form_validate','enctype' => 'multipart/form-data')).
            Form::hidden('csrf', Security::token()).
            Form::hidden('old_parent', $item['parent']).
            Form::hidden('news_id', $item['id'])
        );
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('news')) { ?>class="active"<?php } ?>><a href="#news" data-toggle="tab"><?php echo __('Page', 'news'); ?></a></li>
            <li <?php if (Notification::get('metadata')) { ?>class="active"<?php } ?>><a href="#metadata" data-toggle="tab"><?php echo __('Metadata', 'news'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'news'); ?></a></li>
            <li <?php if (Notification::get('img')) { ?>class="active"<?php } ?>><a href="#img" data-toggle="tab"><?php echo __('Image', 'news'); ?></a></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=news"><?php echo __('Return to Index', 'news'); ?></a></li>
        </ul>

        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('news')) { ?>active<?php } ?>" id="news">
                <?php
                echo (
                    Form::label('news_title', __('Title', 'news')).
                    Form::input('news_title', $item['title'], array('class' => 'required span6')).

                    Form::label('news_slug', __('Name (slug)', 'news'))
                );
                if ($item['slug'] != "")
                {
                    echo Form::input('news_slug', $item['slug'], array('class' => 'required span6', 'readonly' => 'readonly'));
                }
                else
                {
                    echo Form::input('news_slug', $item['slug'], array('class' => 'required span6'));
                }
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('metadata')) { ?>active<?php } ?>" id="metadata">
                <?php
                echo (
                    Form::label('news_keywords', __('Keywords', 'news')).
                    Form::input('news_keywords', $item['keywords'], array('class' => 'span8')).
                    Form::label('news_tags', __('Tags', 'news')).
                    Form::input('news_tags', $item['tags'], array('class' => 'span8')).
                    Form::label('news_description', __('Description', 'news')).
                    Form::textarea('news_description', $item['description'], array('class' => 'span8'))
                );
                echo (
                    Html::br(2).
                        Form::label('news_robots', __('Search Engines Robots', 'news')).
                        'no Index'.Html::nbsp().Form::checkbox('news_robots_index', 'index', $item['robots_index']).Html::nbsp(2).
                        'no Follow'.Html::nbsp().Form::checkbox('news_robots_follow', 'follow', $item['robots_follow'])
                );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
                <div class="row-fluid">
                    <div class="span3">
                        <?php
                        echo (
                            Form::label('news_parent', __('Parent', 'news')).
                            Form::select('news_parent', $opt['list'], $item['parent'])
                        );
                        ?>
                    </div>
                    <div class="span3">
                        <?php
                        echo (
                            Form::label('news_template', __('Template', 'news')).
                            Form::select('news_template', $opt['templates'], $item['template'])
                        );
                        ?>
                    </div>
                    <div class="span3">
                        <?php
                        echo (
                            Form::label('news_status', __('Status', 'news')).
                            Form::select('news_status', $opt['status'], $item['status'])
                        );
                        ?>
                    </div>
                    <div class="span3">
                        <?php
                        echo (
                            Form::label('news_access', __('Access', 'news')).
                            Form::select('news_access', $opt['access'], $item['access'])
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="img">
                <div class="row-fluid">
                    <div class="span4">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail image" style="width: 200px; height: 150px;">
                                <?php
                                if(file::exists($opt['dir'].$item['id'].'.jpg')):
                                    ?>
                                    <a href="#" rel="<?php echo $opt['url'].$item['id'].'.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="btn btn-file">
                                    <span class="fileupload-new"><?php echo __('Select image', 'news'); ?></span>
                                    <span class="fileupload-exists"><?php echo __('Change', 'news'); ?></span>
                                    <?php echo Form::file('news_file')?></span>
                                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo __('Remove', 'news'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <?php echo Form::label('news_short', __('News Short', 'news')).Form::textarea('news_short', Html::toText($item['short']), array('class' => 'required', 'style' => 'width: 100%; height: 100px;')); ?>
        <?php Action::run('admin_editor', array(Html::toText($item['content']))); ?>

        <br />

        <div class="row-fluid">
            <div class="span6">
                <?php
                echo (
                    Form::submit('edit_news_and_exit', __('Save and exit', 'news'), array('class' => 'btn')).Html::nbsp(2).
                        Form::submit('edit_news', __('Save', 'news'), array('class' => 'btn'))
                );
                ?>
            </div>
            <div class="span6">
                <div class="pull-right"><?php echo __('Published on', 'news'); ?>: <?php echo Form::input('news_date', $item['date'], array('class' => 'input-large')); ?></div>
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>