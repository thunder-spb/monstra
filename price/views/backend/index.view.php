<h2><?php echo __('Price', 'price'); ?></h2>
<br />

<!-- Price_upload_files -->
<?php
if (Notification::get('error'))
	Alert::error(Notification::get('error'));

echo (
	Form::open(null, array('enctype' => 'multipart/form-data')).
		Form::hidden('csrf', Security::token()).
		Form::input('file', null, array('type' => 'file', 'size' => '25')).Html::br().
		Form::submit('upload_file', __('Upload', 'price'), array('class' => 'btn default btn-small')).
		Form::close()
)
?>
<!-- /Price_upload_files -->

<!-- Price_path -->
<ul class="breadcrumb">
	<?php
	$path_parts = explode ('/',$path);
	$s = '';
	foreach ($path_parts as $p) {
		$s .= $p.'/';
		if($p == $current[count($current)-2]) $active = ' class="active"'; else $active = '';
		echo '<span class="divider">/<span> <li'.$active.'><a href="index.php?id=price&path='.$s.'">'.$p.'</a></li>';
	}
	?>
</ul>
<!-- /Price_path -->

<table class="table table-bordered">
	<thead>
	<tr>
		<td><?php echo __('Name', 'price'); ?></td>
		<td><?php echo __('ShortCode', 'price'); ?></td>
		<td><?php echo __('Size', 'price'); ?></td>
		<td width="30%"><?php echo __('Actions', 'price'); ?></td>
	</tr>
	</thead>
	<tbody>
	<?php if (isset($dir_list)) foreach ($dir_list as $dir) { ?>
	<tr>
		<td>
			<b><?php echo Html::anchor($dir, 'index.php?id=price&path='.$path.$dir.'/'); ?></b>
		</td>
		<td>

		</td>
		<td>
			<?php echo Number::byteFormat(Dir::size(UPLOADS . DS . $dir)); ?>
		</td>
		<td>
			<?php echo Html::anchor(__('Delete', 'price'),
			'index.php?id=price&delete_dir='.$dir.'&path='.$path.'&token='.Security::token(),
			array('class' => 'btn', 'onclick' => "return confirmDelete('".__('Delete directory: :dir', 'price', array(':dir' => $dir))."')"));
			?>
		</td>
	</tr>
	<?php } ?>
	<?php if (isset($files_list)) foreach ($files_list as $file) { $ext = File::ext($file); ?>
		<?php if (in_array($ext, $allowed_types)) { ?>
		<tr>
			<td>
				<?php echo Html::anchor(File::name($file), $site_url.'public' . DS .$path.$file, array('target'=>'_blank'));?>
			</td>
			<td>
				{price file=<?php echo $file; ?>}
			</td>
			<td>
				<?php echo Number::byteFormat(filesize($files_path. DS .$file)); ?>
			</td>
			<td>
				<?php echo Html::anchor(__('Delete', 'price'),
				'index.php?id=price&delete_file='.$file.'&path='.$path.'&token='.Security::token(),
				array('class' => 'btn btn-actions', 'onclick' => "return confirmDelete('".__('Delete file: :file', 'price', array(':file' => $file))."')"));
				?>
			</td>
		</tr>
	<?php }
	} ?>
	</tbody>
</table>