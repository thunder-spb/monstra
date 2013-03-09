<?php if(!isset($opt['display'])) { ?>
<ul class="breadcrumb">
    <li><a href="<?php echo $opt["site_url"];?>catalog"><?php echo __('Catalog', 'catalog');?></a> <span class="divider">/</span></li>
    <li class="active"><?php echo $opt['title'];?></li>
</ul>
<?php } ?>
<div class="caption">
    <h1><?php echo $opt['title']; ?></h1>
    <p><?php echo Text::toHtml(File::getContent(STORAGE . DS . 'catalog' . DS. 'catalog.'. $opt['id'] .'.txt')); ?></p>
</div>

<ul class="thumbnails">
<?php
foreach($records as $row):
?>
    <li class="span4">
        <div class="thumbnail media">
            <?php if (File::exists($opt["url"].$row['id'].'.jpg')) { ?>
            <a class="pull-left" href="<?php echo $opt["url"].$row['id'].'.jpg' ?>"><img class="img-polaroid" alt="<?php echo $row['title'] ?>" src="<?php echo $opt["url"].'thumbnail'.DS.$row['id'].'.jpg' ?>"></a>
            <?php }
        else{ ?>
            <a class="pull-left" href="<?php echo $opt["url"].'no_item.jpg';?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
            <?php }?>
            <div class="media-body breadcrumb">
                <h4 class="media-heading"><?php echo $row['title'] ?></h4>
                <?php echo $row['short']?>
                <?php if ($opt["price"] == 1) { ?>
                    <br><span class="price label label-info"><?php echo $row['price']." ".$row['currency'] ?></span>
                <? } ?>
                <br><br><a href="<?php echo $opt["site_url"];?>catalog/<?php echo $opt["slug"];?>/item/<?php echo $row['id'] ?>"><?php echo __('More', 'catalog'); ?></a>
            </div>
        </div>
    </li>
<?php
endforeach; ?>
</ul>
<?php if(!isset($opt['display'])) {
    Catalog::paginator($opt['page'], $opt['pages'], $opt["site_url"].'catalog/'.$opt["slug"].'?page=');
}?>
