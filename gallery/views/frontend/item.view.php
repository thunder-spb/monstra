<?php if(!isset($opt['display'])) { ?>
<ul class="breadcrumb">
    <li><a href="<?php echo $opt["site_url"];?>gallery"><?php echo __('Gallery', 'gallery');?></a> <span class="divider">/</span></li>
    <li><a href="<?php echo $opt["site_url"];?>gallery/<?php echo $opt['gallery']['slug'];?>"><?php echo $opt['gallery']['title'];?></a> <span class="divider">/</span></li>
    <li class="active"><?php echo $item['title'] ?></li>
</ul>
<?php } ?>

<div class="media">
    <?php if (File::exists($opt["dir"].$item['id'].'.jpg') && $item['media'] != '') { ?>
    <a class="cImg pull-left" href="<?php echo $item['media']; ?>"><img class="img-polaroid" alt="<?php echo $item['title'] ?>" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
    <?php } elseif (File::exists($opt["dir"].$item['id'].'.jpg')) { ?>
    <a class="cImg pull-left" href="<?php echo $opt["url"].$item['id'].'.jpg' ?>"><img class="img-polaroid" alt="<?php echo $item['title'] ?>" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
    <?php } else{ ?>
    <a class="cImg pull-left" href="<?php echo $item['media']; ?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
    <?php }?>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $item['title'] ?></h4>
        <p><?php echo $item['description']?></p>
    </div>
</div>
<?php if(!isset($opt['display'])) { ?>
<p>
<ul class="breadcrumb">
    <li><?php echo Date::format($item['date'], 'd.m.Y'); ?> <span class="divider">/</span></li>
    <li class="active"><?php echo __('Hits count','gallery').$item['hits'] ?></li>
</ul>
<?php } ?>


