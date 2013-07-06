<?php foreach($records as $item): ?>
    <li><b><?php echo Date::format($item['date'], 'd.m.Y');?></b>
		<p><?php echo Articles::getArticlesContent($item['id'], false); ?>
			<a href="<?php echo $opt['site_url'];?>articles/view/<?php echo $item['slug'];?>"><?php echo __('Read more', 'articles') ?></a>
		</p>
	</li>
<?php endforeach;?>