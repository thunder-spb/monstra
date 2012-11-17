<h2><?php echo __('Pix gallery', 'slider');?></h2>
<ul class="nav nav-tabs">
	<li <?php if (Notification::get('upload')) { ?>class="active"<?php } ?>><a href="#upload" data-toggle="tab"><?php echo __('Upload photo', 'slider'); ?></a></li>
    <li <?php if (Notification::get('photo')) { ?>class="active"<?php } ?>><a href="#photo" data-toggle="tab"><?php echo __('Photo', 'slider'); ?></a></li>
	<li <?php if (Notification::get('catalog_add')) { ?>class="active"<?php } ?>><a href="#catalog_add" data-toggle="tab"><?php echo __('Catalog Add', 'slider'); ?></a></li>
	<li <?php if (Notification::get('catalog')) { ?>class="active"<?php } ?>><a href="#catalog" data-toggle="tab"><?php echo __('Catalog', 'slider'); ?></a></li>
</ul>

<div class="tab-content tab-page">
	<div class="tab-pane <?php if (Notification::get('catalog_add')) { ?>active<?php } ?>" id="catalog_add">
		<?php
		echo (
			Form::open(null, array('class' => 'form-horizontal')).
			Form::hidden('csrf', Security::token()).
			Form::hidden('slider_submit_catalog', true).
			'<div class="control-group">'.
				Form::label('title', __('fTitle', 'slider'), array('class' => 'control-label')).
				'<div class="controls">'.Form::input('title').'</div>'.
			'</div>'.
			'<div class="control-group">'.
				Form::label('url', __('fUrl', 'slider'), array('class' => 'control-label')).
				'<div class="controls">'.Form::input('url').'</div>'.
			'</div>'.
			'<div class="control-group">'.
				Form::label('sort', __('fSort', 'slider'), array('class' => 'control-label')).
				'<div class="controls">'.Form::input('sort', '0' , array('type' => 'number')).'</div>'.
			'</div>'.
			'<div class="control-group"><div class="controls">'.
			Form::button('submit_catalog_add', __('Add', 'slider'), array('class' => 'btn', 'type' => 'submit')).
			'</div></div>'.
			Form::close()
		);
		?>
	</div>
	<div class="tab-pane <?php if (Notification::get('catalog')) { ?>active<?php } ?>" id="catalog">
		<table class="table table-bordered">
			<thead>
			<tr>
				<td></td>
				<td><?php echo  __('fTitle', 'slider') ?></td>
				<td><?php echo  __('fUrl', 'slider') ?></td>
				<td><?php echo  __('fSort', 'slider') ?></td>
				<td width="40%">Действия</td>
			</tr>
			</thead>
			<tbody>
			<?php foreach($catalog as $item) { ?>
				<tr>
					<td><?php echo Html::toText($item['id']); ?></td>
					<td><?php echo Html::toText($item['title']); ?></td>
					<td><?php echo Html::toText($item['url']); ?></td>
					<td><?php echo Html::toText($item['sort']); ?></td>
					<td width="40%">
						<?php echo Html::anchor(__('Edit', 'slider'), 'index.php?id=slider&action=edit_cat&uid='.$item['id'], array('class' => 'btn btn-actions')); ?>
						<?php echo Html::anchor(__('Delete', 'slider'), 'index.php?id=slider&action=del_cat&uid='.$item['id'].'&url='.$item['url'].'&token='.Security::token(), array('class' => 'btn btn-actions')); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="tab-pane <?php if (Notification::get('upload')) { ?>active<?php } ?>" id="upload">
		<?php
		echo (
			Form::open(null, array('class' => 'form-horizontal')).
			Form::hidden('csrf', Security::token()).
			Form::hidden('slider_submit_image', true).
			'<div class="control-group">'.
			Form::label('img', __('fImg', 'slider'), array('class' => 'control-label')).
			'<div class="controls">'.Form::input('img').'</div>'.
			'</div>'.
			'<div class="control-group">'.
			Form::label('url', __('fUrl', 'slider'), array('class' => 'control-label')).
			'<div class="controls">'.Form::input('url').'</div>'.
			'</div>'.
			'<div class="control-group">'.
			Form::label('title', __('fTitle', 'slider'), array('class' => 'control-label')).
			'<div class="controls">'.Form::input('title').'</div>'.
			'</div>'.
			'<div class="control-group">'.
			Form::label('url', __('fCat', 'slider'), array('class' => 'control-label')).
			'<div class="controls">'.Form::select('cat', $sCat, 'title').'</div>'.
			'</div>'.
			'<div class="control-group">'.
			Form::label('sort', __('fSort', 'slider'), array('class' => 'control-label')).
			'<div class="controls">'.Form::input('sort', '0' , array('type' => 'number')).'</div>'.
			'</div>'.
			'<div class="control-group"><div class="controls">'.
			Form::button('submit_image_add', __('Add', 'slider'), array('class' => 'btn', 'type' => 'submit')).
			'</div></div>'.
			Form::close()
		);
		?>
	</div>
	<div class="tab-pane <?php if (Notification::get('photo')) { ?>active<?php } ?>" id="photo">
		<table class="table table-bordered">
			<thead>
			<tr>
				<td></td>
				<td><?php echo  __('fImg', 'slider') ?></td>
                <td><?php echo  __('fTitle', 'slider') ?></td>
				<td><?php echo  __('fCat', 'slider') ?></td>
				<td><?php echo  __('fSort', 'slider') ?></td>
				<td width="40%">Действия</td>
			</tr>
			</thead>
			<tbody>
			<?php foreach($sImg as $item) { ?>
			<tr>
				<td><?php echo Html::toText($item['id']); ?></td>
				<td><?php echo Html::toText($item['img']); ?></td>
				<td><?php echo Html::toText($item['title']); ?></td>
                <td><?php echo Html::toText($sCat[$item['cat']]); ?></td>
				<td><?php echo Html::toText($item['sort']); ?></td>
				<td width="40%">
					<?php echo Html::anchor(__('Edit', 'slider'), 'index.php?id=slider&action=edit_img&uid='.$item['id'], array('class' => 'btn btn-actions')); ?>
					<?php echo Html::anchor(__('Delete', 'slider'), 'index.php?id=slider&action=del_img&uid='.$item['id'].'&cat='.$item['cat'].'&token='.Security::token(), array('class' => 'btn btn-actions')); ?>
				</td>
			</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>