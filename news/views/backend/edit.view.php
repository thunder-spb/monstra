<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Edit news', 'news'); ?></h2><br />

        <?php
            if (Notification::get('success')) Alert::success(Notification::get('success'));
            
            echo Form::open();
            echo Form::hidden('csrf', Security::token());
            echo Form::hidden('news_id', $news_id);
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('news')) { ?>class="active"<?php } ?>><a href="#news" data-toggle="tab"><?php echo __('Caption', 'news'); ?></a></li>
            <li <?php if (Notification::get('seo')) { ?>class="active"<?php } ?>><a href="#seo" data-toggle="tab"><?php echo __('SEO', 'news'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'news'); ?></a></li>
        </ul>
         
        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('news')) { ?>active<?php } ?>" id="news">
                <?php
                    echo Form::input('news_name', $post_name, array('class' => (isset($errors['news_empty_name'])) ? 'span8 error-field' : 'span8'));
                    if (isset($errors['news_empty_name'])) echo Html::nbsp(3).'<span style="color:red">'.$errors['news_empty_name'].'</span>';
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('seo')) { ?>active<?php } ?>" id="seo">
                <?php
                    echo (
                        Form::label('news_title', __('Title', 'news')).    
                        Form::input('news_title', $post_title, array('class' => 'span8')).

                        Form::label('news_h1', __('H1', 'news')).    
                        Form::input('news_h1', $post_h1, array('class' => 'span8')).
                        
                        Form::label('news_slug', __('Alias (slug)', 'news')).    
                        Form::input('news_slug', $post_slug, array('class' => 'span8')).

                        Form::label('news_keywords', __('Keywords', 'news')).
                        Form::input('news_keywords', $post_keywords, array('class' => 'span8')).

                        Form::label('news_description', __('Description', 'news')).
                        Form::textarea('news_description', $post_description, array('class' => 'span8'))
                    );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
                <div class="row-fluid">
                    <div class="span4">
                    <?php 
                        echo (
                            Form::label('status', __('Status', 'news')).
                            Form::select('status', $status_array, $status) 
                        );
                    ?>
                    </div>
                </div>
            </div>
        </div>
    
        <br /><br />
        <?php Action::run('admin_editor', array(Html::toText($post_content))); ?>
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
                <div class="pull-right"><?php echo __('Published', 'news'); ?>: <?php echo Form::input('news_date', $date, array('class' => 'input-large')); ?></div>
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>