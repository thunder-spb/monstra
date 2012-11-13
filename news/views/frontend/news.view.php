<?php foreach($records as $row):
		$text = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $row['id'] . '.news.txt'));
		$content_array = explode("{cut}", $text);
?>
    <li><b><?php echo Date::format($row['date'], 'd.m.Y');?></b>
		<p><?php echo $content_array[0];?>
			<a href="<?php echo $site_url;?>news/<?php echo $row['id'];?>/<?php echo $row['slug'];?>"><?php echo __('Read more', 'news') ?></a>
		</p>
	</li>
<?php endforeach;?>