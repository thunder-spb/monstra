<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('Edit news', 'news'); ?></h2>
        <br />

        <?php if (Notification::get('success')) Alert::success(Notification::get('success')); ?>

        <?php    
            echo (
                Form::open(null, array('class' => 'form_validate')).
                Form::hidden('csrf', Security::token()).
                Form::hidden('old_name', Request::get('name')).
                Form::hidden('old_parent', $news['parent']).
                Form::hidden('news_id', $news['id'])
            );
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('news')) { ?>class="active"<?php } ?>><a href="#news" data-toggle="tab"><?php echo __('Page', 'news'); ?></a></li>
            <li <?php if (Notification::get('metadata')) { ?>class="active"<?php } ?>><a href="#metadata" data-toggle="tab"><?php echo __('Metadata', 'news'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'news'); ?></a></li>
            <li <?php if (Notification::get('upload')) { ?>class="active"<?php } ?>><a href="#upload" data-toggle="tab"><?php echo __('Upload photo', 'news'); ?></a></li>
        </ul>
         
        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('news')) { ?>active<?php } ?>" id="news">
				<?php
				echo (
					Form::label('title', __('Title', 'news')).
						Form::input('title', $news['title'], array('class' => 'required span6')).

						Form::label('name', __('Name (slug)', 'news')).
						Form::input('name', $news['slug'], array('class' => 'required span6'))
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
            <div class="tab-pane <?php if (Notification::get('upload')) { ?>active<?php } ?>" id="upload">
                <?php
                echo (
                Form::input('catalog_upload', __('Upload', 'catalog'), array('class' => 'btn', 'data-toggle' => 'modal', 'onclick' => '$("#upPhoto").modal("show").width(270);'))
                );
                if (file_exists($dir.$news['id'].'_t.jpg'))
                {
                    ?>
                    <a href="<?php echo $url.$news['id'].'_o.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $url.$news['id'].'_t.jpg' ?>"></a>
                    <?php
                }
                ?>
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
					Form::submit('edit_news_and_exit', __('Save and exit', 'news'), array('class' => 'btn')).Html::nbsp(2).
						Form::submit('edit_news', __('Save', 'news'), array('class' => 'btn'))
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

<div id="upPhoto" class="modal hide">
    <div class="modal-header">
        <a data-dismiss="modal" class="close">×</a>
        <h3><?php echo __('Upload photo', 'catalog') ?></h3>
    </div>
    <div class="modal-body">
        <?php
        echo (
            Form::open(null, array('enctype' => 'multipart/form-data')).
                Form::hidden('csrf', Security::token()).
                Form::hidden('name', $news['slug']).
                Form::hidden('id', $news['id']).
                Form::input('file', null, array('type' => 'file', 'size' => '25')).
                Form::submit('upload_file', __('Upload', 'stock'), array('class' => 'btn default')).
                Form::close()
        );
        ?>
    </div>
</div>