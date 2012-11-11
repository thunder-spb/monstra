<h2><?php echo __('Price', 'price').': '.$name ?></h2>
<br />

<!-- Price_upload_files -->
<?php
echo (
	Form::open(null, array('enctype' => 'multipart/form-data')).
		Form::hidden('csrf', Security::token()).
		Form::hidden('name', $name).
		Form::input('file', null, array('type' => 'file', 'size' => '25')).Html::br().
		Form::submit('upload_file', __('Edit', 'price'), array('class' => 'btn default')).
		Html::anchor(__('Back', 'price'), 'index.php?id=price', array('class' => 'btn')).
		Form::close()
)
?>
<!-- /Price_upload_files -->