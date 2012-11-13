<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Edit articles', 'articles'); ?></h2><br />

        <?php
            if (Notification::get('success')) Alert::success(Notification::get('success'));
            
            echo Form::open();
            echo Form::hidden('csrf', Security::token());
            echo Form::hidden('articles_id', $articles_id);
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('articles')) { ?>class="active"<?php } ?>><a href="#articles" data-toggle="tab"><?php echo __('Caption', 'articles'); ?></a></li>
            <li <?php if (Notification::get('seo')) { ?>class="active"<?php } ?>><a href="#seo" data-toggle="tab"><?php echo __('SEO', 'articles'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'articles'); ?></a></li>
        </ul>
         
        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('articles')) { ?>active<?php } ?>" id="articles">
                <?php
                    echo Form::input('articles_name', $post_name, array('class' => (isset($errors['articles_empty_name'])) ? 'span8 error-field' : 'span8'));
                    if (isset($errors['articles_empty_name'])) echo Html::nbsp(3).'<span style="color:red">'.$errors['articles_empty_name'].'</span>';
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('seo')) { ?>active<?php } ?>" id="seo">
                <?php
                    echo (
                        Form::label('articles_title', __('Title', 'articles')).    
                        Form::input('articles_title', $post_title, array('class' => 'span8')).

                        Form::label('articles_h1', __('H1', 'articles')).    
                        Form::input('articles_h1', $post_h1, array('class' => 'span8')).
                        
                        Form::label('articles_slug', __('Alias (slug)', 'articles')).    
                        Form::input('articles_slug', $post_slug, array('class' => 'span8')).

                        Form::label('articles_keywords', __('Keywords', 'articles')).
                        Form::input('articles_keywords', $post_keywords, array('class' => 'span8')).

                        Form::label('articles_description', __('Description', 'articles')).
                        Form::textarea('articles_description', $post_description, array('class' => 'span8'))
                    );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
                <div class="row-fluid">
                    <div class="span4">
                    <?php 
                        echo (
                            Form::label('status', __('Status', 'articles')).
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
                        Form::submit('edit_articles_and_exit', __('Save and exit', 'articles'), array('class' => 'btn')).Html::nbsp(2).
                        Form::submit('edit_articles', __('Save', 'articles'), array('class' => 'btn'))
                    );
                ?>
            </div>
            <div class="span6">
                <div class="pull-right"><?php echo __('Published', 'articles'); ?>: <?php echo Form::input('articles_date', $date, array('class' => 'input-large')); ?></div>
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>