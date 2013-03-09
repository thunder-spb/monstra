<h2><?php echo __('Settings catalog', 'catalog');?></h2>

<?php
$resize = array(
	'width'   => __('Respect to the width', 'catalog'),
	'height'  => __('Respect to the height', 'catalog'),
	'crop'    => __('Similarly, cutting unnecessary', 'catalog'),
	'stretch' => __('Similarly with the expansion', 'catalog'),
);
echo (
    Form::open().
'<div class="row-fluid show-grid">'.
'<div class="span3">'.
		Form::label('limit', __('Items per page (website)', 'catalog')).
		Form::input('limit', Option::get('catalog_limit')).
		Form::label('width_thumb', __('Width thumbnails (px)', 'catalog')).
		Form::input('width_thumb', Option::get('catalog_w')).
		Form::label('height_thumb', __('Height thumbnails (px)', 'catalog')).
		Form::input('height_thumb', Option::get('catalog_h')).
		Form::label('currency', __('Currency', 'catalog')).
		Form::input('currency', Option::get('catalog_currency')).
		Form::hidden('csrf', Security::token()).Html::Br(3).
		Form::submit('catalog_submit_settings', __('Save', 'catalog'), array('class' => 'btn')).Html::Nbsp(2).
		Form::submit('catalog_submit_settings_cancel', __('Cancel', 'catalog'), array('class' => 'btn')).
'</div>'.
'<div class="span3">'.
		Form::label('limit_admin', __('Items per page (admin)', 'catalog')).
		Form::input('limit_admin', Option::get('catalog_limit_admin')).
		Form::label('width_orig', __('Original width (px, max)', 'catalog')).
		Form::input('width_orig', Option::get('catalog_wmax')).
		Form::label('height_orig', __('Original height (px, max)', 'catalog')).
		Form::input('height_orig', Option::get('catalog_hmax')).
		Form::label('resize', __('Resize', 'catalog')).
		Form::select('resize', $resize, Option::get('catalog_resize')).Html::Br().
'</div>'.
	'</div>'.
    Form::close()
);
?>