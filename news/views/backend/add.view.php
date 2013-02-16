<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('New news', 'news'); ?></h2>
        <br />

        <?php if (Notification::get('success')) Alert::success(Notification::get('success')); ?>

        <?php    
            echo (
                Form::open(null, array('class' => 'form_validate')).
                Form::hidden('csrf', Security::token())
            );
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('news')) { ?>class="active"<?php } ?>><a href="#news" data-toggle="tab"><?php echo __('Page', 'news'); ?></a></li>
            <li <?php if (Notification::get('metadata')) { ?>class="active"<?php } ?>><a href="#metadata" data-toggle="tab"><?php echo __('Metadata', 'news'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'news'); ?></a></li>
        </ul>
         
        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('news')) { ?>active<?php } ?>" id="news">
                <?php
                    echo (
                        Form::label('title', __('Title', 'news')).
                        Form::input('title', $news['title'], array('class' => 'required span6')).

                        Form::label('name', __('Name (slug)', 'news')).
                        Form::input('name', $news['name'], array('class' => 'required span6'))
                    );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('metadata')) { ?>active<?php } ?>" id="metadata">
                <?php
                    echo (
                        Form::label('keywords', __('Keywords', 'news')).
                        Form::input('keywords', $news['keywords'], array('class' => 'span8')).
                        Html::br(2).
                        Form::label('description', __('Description', 'news')).
                        Form::textarea('description', $news['description'], array('class' => 'span8'))
                    );
                    echo (   
                        Html::br(2).  
                        Form::label('robots', __('Search Engines Robots', 'news')).   
                        'no Index'.Html::nbsp().Form::checkbox('robots_index', 'index', $news['robots_index']).Html::nbsp(2).
                        'no Follow'.Html::nbsp().Form::checkbox('robots_follow', 'follow', $news['robots_follow'])
                    );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
                <div class="row-fluid">
                    <div class="span3">
                    <?php
                        echo (
                            Form::label('parent', __('Parent', 'news')).
                            Form::select('parent', $news_array, $news['parent'])
                        );
                    ?>
                    </div>
                    <div class="span3">
                    <?php 
                        echo (
                            Form::label('status', __('Status', 'news')).
                            Form::select('status', $status_array, $news['status'])
                        );
                    ?>
                    </div>
                    <div class="span3">
                    <?php 
                        echo (
                            Form::label('access', __('Access', 'news')).
                            Form::select('access', $access_array, $news['access'])
                        );
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <?php echo Form::label('short', __('News Short', 'news')).Form::textarea('short', Html::toText($news['short']), array('class' => 'required', 'style' => 'width: 100%; height: 100px;')); ?>
        <?php Action::run('admin_editor', array(Html::toText($news['content']))); ?>

        <br />

        <div class="row-fluid">
            <div class="span6">
                <?php
                    echo (
                        Form::submit('add_news_and_exit', __('Save and exit', 'news'), array('class' => 'btn')).Html::nbsp(2).
                        Form::submit('add_news', __('Save', 'news'), array('class' => 'btn'))
                    );
                ?>
            </div>
            <div class="span6">
                <div class="pull-right"><?php echo __('Published on', 'news'); ?>: <?php echo Form::input('date', $news['date'], array('class' => 'input-large')); ?></div>
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>