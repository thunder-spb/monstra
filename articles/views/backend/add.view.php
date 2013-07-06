<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('New articles', 'articles'); ?></h2>
        <br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));

        echo (
                Form::open(null, array('class' => 'form_validate','enctype' => 'multipart/form-data')).
                Form::hidden('csrf', Security::token())
            );
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('articles')) { ?>class="active"<?php } ?>><a href="#articles" data-toggle="tab"><?php echo __('Page', 'articles'); ?></a></li>
            <li <?php if (Notification::get('metadata')) { ?>class="active"<?php } ?>><a href="#metadata" data-toggle="tab"><?php echo __('Metadata', 'articles'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'articles'); ?></a></li>
            <li <?php if (Notification::get('img')) { ?>class="active"<?php } ?>><a href="#img" data-toggle="tab"><?php echo __('Image', 'articles'); ?></a></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=articles"><?php echo __('Return to Index', 'articles'); ?></a></li>
        </ul>
         
        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('articles')) { ?>active<?php } ?>" id="articles">
                <?php
                    echo (
                        Form::label('article_title', __('Title', 'articles')).
                        Form::input('article_title', $item['title'], array('class' => 'required span6')).

                        Form::label('article_slug', __('Name (slug)', 'articles')).
                        Form::input('article_slug', $item['slug'], array('class' => 'required span6'))
                    );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('metadata')) { ?>active<?php } ?>" id="metadata">
                <?php
                    echo (
                        Form::label('article_keywords', __('Keywords', 'articles')).
                        Form::input('article_keywords', $item['keywords'], array('class' => 'span8')).
                        Form::label('article_tags', __('Tags', 'articles')).
                        Form::input('article_tags', $item['tags'], array('class' => 'span8')).
                        Form::label('article_description', __('Description', 'articles')).
                        Form::textarea('article_description', $item['description'], array('class' => 'span8'))
                    );
                    echo (   
                        Html::br(2).  
                        Form::label('article_robots', __('Search Engines Robots', 'articles')).
                        'no Index'.Html::nbsp().Form::checkbox('article_robots_index', 'index', $item['robots_index']).Html::nbsp(2).
                        'no Follow'.Html::nbsp().Form::checkbox('article_robots_follow', 'follow', $item['robots_follow'])
                    );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
                <div class="row-fluid">
                    <div class="span3">
                    <?php
                        echo (
                            Form::label('article_parent', __('Parent', 'articles')).
                            Form::select('article_parent', $opt['list'], $item['parent'])
                        );
                    ?>
                    </div>
                    <div class="span3">
                        <?php
                        echo (
                            Form::label('article_template', __('Template', 'articles')).
                            Form::select('article_template', $opt['templates'], $item['template'])
                        );
                        ?>
                    </div>
                    <div class="span3">
                    <?php 
                        echo (
                            Form::label('article_status', __('Status', 'articles')).
                            Form::select('article_status', $opt['status'], $item['status'])
                        );
                    ?>
                    </div>
                    <div class="span3">
                    <?php 
                        echo (
                            Form::label('article_access', __('Access', 'articles')).
                            Form::select('article_access', $opt['access'], $item['access'])
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
                                    <span class="fileupload-new"><?php echo __('Select image', 'articles'); ?></span>
                                    <span class="fileupload-exists"><?php echo __('Change', 'articles'); ?></span>
                                    <?php echo Form::file('article_file')?></span>
                                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo __('Remove', 'articles'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <?php echo Form::label('article_short', __('Articles Short', 'articles')).Form::textarea('article_short', Html::toText($item['short']), array('class' => 'required', 'style' => 'width: 100%; height: 100px;')); ?>
        <?php Action::run('admin_editor', array(Html::toText($item['content']))); ?>

        <br />

        <div class="row-fluid">
            <div class="span6">
                <?php
                    echo (
                        Form::submit('add_articles_and_exit', __('Save and exit', 'articles'), array('class' => 'btn')).Html::nbsp(2).
                        Form::submit('add_articles', __('Save', 'articles'), array('class' => 'btn'))
                    );
                ?>
            </div>
            <div class="span6">
                <div class="pull-right"><?php echo __('Published on', 'articles'); ?>: <?php echo Form::input('article_date', $item['date'], array('class' => 'input-large')); ?></div>
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>