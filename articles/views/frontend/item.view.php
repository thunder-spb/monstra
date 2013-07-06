<?php if(!isset($opt['display'])) { ?>
    <ul class="breadcrumb">
        <li><a href="<?php echo $opt["site_url"];?>articles"><?php echo __('Articles', 'articles');?></a> <span class="divider">/</span></li>
        <li class="active"><?php echo $item['title'] ?></li>
    </ul>
<?php } ?>
<div id="articles">
    <div class="media">
        <?php
        if (File::exists($opt["dir"].$item['id'].'.jpg')) { ?>
            <a class="cImg pull-left" href="<?php echo $opt["url"].$item['id'].'.jpg' ?>"><img class="img-polaroid" alt="<?php echo $item['title'] ?>" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
        <?php } else{ ?>
            <a class="cImg pull-left" href="<?php echo $opt["url"].'no_item.jpg';?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
        <?php }?>
        <div class="media-body breadcrumb">
            <h4 class="media-heading"><?php echo $item['title'] ?></h4>
            <p><?php echo Articles::getArticlesContent($item['id']); ?></p>
        </div>
    </div>
</div>
<?php if(!isset($opt['display'])) { ?>
    <p><br /><? echo Articles::getTags($item['slug']);?>
    <? echo Articles::getRelatedPosts(); ?><br />
    <ul class="breadcrumb">
        <li><?php echo Date::format($item['date'], 'd.m.Y'); ?> <span class="divider">/</span></li>
        <li class="active"><?php echo __('Hits count','articles').$item['hits'] ?></li>
    </ul>
<?php }
?>