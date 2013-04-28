<?
if (count($tags) > 0):
foreach($tags as $row):
    $records = Catalog::$catalog->select('[tags='.$row['id'].']');
    if(count($records) > 0):
?>
    <h5><?php echo $row['title'] ?></h5>
    <ul class="thumbnails">
        <?php foreach($records as $item):
        $url_item = Option::get('siteurl').'catalog/'.$item["slug"];
        ?>
        <li class="span3">
            <div class="thumbnail">
                <center><h4><a href="<?php echo $url_item; ?>"><?php echo $item['title'] ?></a></h4>
                <?php if (File::exists($opt["dir"].'cat_'.$item['id'].'.jpg')) { ?>
                <a href="<?php echo $url_item; ?>"><img class="img-polaroid"  alt="<?php echo $item['title'] ?>" src="<?php echo $opt["url"].'thumbnail/cat_'.$item['id'].'.jpg' ?>"></a>
                <?php }
            else{ ?>
                <a href="<?php echo $url_item; ?>"><img class="img-polaroid"  src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
                <?php }?>
                <div class="caption">
                    <p><?php echo Text::toHtml(File::getContent(STORAGE . DS . 'catalog' . DS . 'catalog.' . $item['id'] . '.txt'))?></p>
                </div></center>
            </div>
        </li>
        <?php
        endforeach;
        ?>
    </ul>
    <?php
    endif;
endforeach;
endif;
?>
