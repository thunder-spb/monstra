<div class="caption">
    <h1><?php echo __('Gallery', 'gallery'); ?></h1>
    <p><?php
        if (File::exists(STORAGE . DS . 'gallery' . DS. 'album.0.txt')){
            echo Text::toHtml(File::getContent(STORAGE . DS . 'gallery' . DS. 'album.0.txt'));
        }
        ?>
    </p>
</div>
<ul class="thumbnails">
    <?php foreach($records as $item):
    $url_item = Option::get('siteurl').'gallery/'.$item["slug"];
    ?>
    <li class="span3">
        <div class="thumbnail">
            <?php if (File::exists($opt["dir"].'album_'.$item['id'].'.jpg')) { ?>
            <a href="<?php echo $url_item; ?>"><img class="img-polaroid" alt="<?php echo $item['title'] ?>" src="<?php echo $opt["url"].'thumbnail/album_'.$item['id'].'.jpg' ?>"></a>
            <?php }
        else{ ?>
            <a href="<?php echo $url_item; ?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
            <?php }?>
            <div class="caption">
                <h4><?php echo $item['title'] ?></h4>
                <p><a class="btn btn-primary" href="<?php echo $url_item; ?>"><?php echo __('View','gallery')?></a></p>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
