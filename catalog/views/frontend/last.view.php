<?php
foreach ($items as $item) :
    $catalog = Catalog::$catalog->select('[id="'.$item['catalog'].'"]', null);
?>
<div class="media">
    <?php if (File::exists($opt["dir"].$item['id'].'.jpg')) { ?>
    <a class="pull-left" href="<?php echo $opt["url"].$item['id'].'.jpg' ?>"><img class="img-polaroid" alt="<?php echo $item['title'] ?>" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
    <?php } else{ ?>
    <a class="pull-left" href="<?php echo $opt["url"].'no_item.jpg';?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
    <?php }?>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $item['title'] ?></h4>
        <p><a href="<?php echo $opt["site_url"];?>catalog/<?php echo $catalog['slug'];?>"><?php echo $catalog['title'];?></a></p>
        <p><?php echo Text::toHtml(File::getContent(STORAGE . DS . 'catalog' . DS . 'item.' . $item['id'] . '.txt'))?></p>
        <span class="price label label-info"><?php echo $item['price']." ".$item['currency'] ?></span>
    </div>
</div>
<?php
endforeach
?>


