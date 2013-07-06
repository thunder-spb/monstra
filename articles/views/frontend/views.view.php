<?php foreach($records as $item):?>
    <li><a href="<?php echo $opt['site_url'];?>articles/view/<?php echo $item['slug'];?>"><?php echo $item['title'];?></a></li>
<?php endforeach;?>