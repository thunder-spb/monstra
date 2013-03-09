<ul class="thumbnails">
    <?php foreach($records as $row):
    $url_item = Option::get('siteurl').'catalog/'.$row["slug"];
    ?>
    <li class="span4">
        <div class="thumbnail media">
            <?php if (File::exists($opt["dir"].$row['id'].'.jpg')) { ?>
            <a class="pull-left" href="<?php echo $url_item; ?>"><img class="img-polaroid" alt="<?php echo $row['title'] ?>" src="<?php echo $opt["url"].'thumbnail/'.$row['id'].'.jpg' ?>"></a>
            <?php }
        else{ ?>
            <a class="pull-left" href="<?php echo $url_item; ?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
            <?php }?>
            <div class="media-body breadcrumb">
                <h4 class="media-heading"><?php echo $row['title'] ?></h4>
                <p><?php echo Text::toHtml(File::getContent(STORAGE . DS . 'catalog' . DS . 'catalog.' . $row['id'] . '.txt'))?></p>
                <p><a class="btn btn-primary" href="<?php echo $url_item; ?>"><?php echo __('View','catalog')?></a></p>
                </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
