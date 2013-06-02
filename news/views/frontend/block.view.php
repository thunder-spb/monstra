<?php foreach($records as $item): ?>
    <li><b><?php echo Date::format($item['date'], 'd.m.Y');?></b>
		<p><?php echo News::getNewsContent($item['id'], false); ?>
			<a href="<?php echo $opt['site_url'];?>news/view/<?php echo $item['slug'];?>"><?php echo __('Read more', 'news') ?></a>
		</p>
	</li>
<?php endforeach;?>