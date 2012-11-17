<h2><?php echo __('Pix gallery', 'slider');?></h2>
<?php
if (Notification::get('success'))
	Alert::success(Notification::get('success'));
if (Notification::get('error'))
	Alert::error(Notification::get('error'));

echo (
	Form::open(null, array('class' => 'form-horizontal')).
	Form::hidden('csrf', Security::token()).
	Form::hidden('id', $sImg['id']).
	Form::hidden('slider_edit_image', true).
	'<div class="control-group">'.
	Form::label('img', __('fImg', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::input('img', $sImg['img']).'</div>'.
	'</div>'.
	'<div class="control-group">'.
	Form::label('url', __('fUrl', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::input('url', $sImg['url']).'</div>'.
	'</div>'.
	'<div class="control-group">'.
	Form::label('title', __('fTitle', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::input('title', $sImg['title']).'</div>'.
	'</div>'.
	'<div class="control-group">'.
	Form::label('url', __('fCat', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::select('cat', $sCat, 'title').'</div>'.
	'</div>'.
	'<div class="control-group">'.
	Form::label('sort', __('fSort', 'slider'), array('class' => 'control-label')).
	'<div class="controls">'.Form::input('sort', $sImg['sort']).'</div>'.
	'</div>'.
	'<div class="control-group"><div class="controls">'.
	Form::button('submit_image_edit', __('Save', 'slider'), array('class' => 'btn', 'type' => 'submit')).
	'</div></div>'.
	Form::close()
);
?>