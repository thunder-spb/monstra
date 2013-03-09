<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Edit catalog: :catalog', 'catalog', array(':catalog' => $post['title'])); ?></h2><br />

        <?php
        if (Notification::get('success'))
            Alert::success(Notification::get('success'));

        foreach($errors as $item)
        {
            Alert::error($item);
        }

        echo Form::open(null, array('class' => 'form_validate'));
        echo Form::hidden('csrf', Security::token());
        echo Form::hidden('catalog_id', $post['cid']);
        echo (
            Form::label('catalog_title', __('Title', 'catalog')).
                Form::input('catalog_title', $post['title'], array('class' => 'required span8')).

                Form::label('catalog_slug', __('Alias (slug)', 'catalog')).
                Form::input('catalog_slug', $post['slug'], array('class' => 'required span8')).

                Form::label('catalog_description', __('Description', 'catalog')).
                Form::input('catalog_description', $post['description'], array('class' => 'required span8')).

                Form::label('catalog_keywords', __('Keywords', 'catalog')).
                Form::input('catalog_keywords', $post['keywords'], array('class' => 'required span8'))
        );
        ?>
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