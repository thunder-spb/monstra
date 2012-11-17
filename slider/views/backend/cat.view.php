<h2><?php echo __('Pix gallery', 'slider');?></h2>
<?php
if (Notification::get('success'))
	Alert::success(Notification::get('success'));
if (Notification::get('error'))
	Alert::error(Notification::get('error'));

echo (
	Form::open(null, array('class' => 'form-horizontal')).
	Form::hidden('csrf', Security::token()).
	Form::hidden('id', $catalog['id']).
	Form::hidden('slider_edit_catalog', true).
	'<div class="control-group">'.
	Form::label('title', __('fTitle', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::input('title', $catalog['title']).'</div>'.
	'</div>'.
	'<div class="control-group">'.
	Form::label('url', __('fUrl', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::input('url', $catalog['url']).'</div>'.
	'</div>'.
	'<div class="control-group">'.
	Form::label('sort', __('fSort', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::input('sort', $catalog['sort']).'</div>'.
	'</div>'.
	'<div class="control-group"><div class="controls">'.
	Form::button('submit_catalog_edit', __('Save', 'slider'), array('class' => 'btn', 'type' => 'submit')).
	'</div></div>'.
	Form::close()
);
?>