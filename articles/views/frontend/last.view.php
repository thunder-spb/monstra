<?php foreach($records as $row):?>
    <li><a href="<?php echo $site_url;?>articles/<?php echo $row['id'];?>/<?php echo $row['slug'];?>"><?php echo $row['name'];?></a></li>              
<?php endforeach;?>