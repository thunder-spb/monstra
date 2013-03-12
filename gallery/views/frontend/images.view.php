<?php if(!isset($opt['display'])) { ?>
<ul class="breadcrumb">
    <li><a href="<?php echo $opt["site_url"];?>gallery"><?php echo __('Gallery', 'gallery');?></a> <span class="divider">/</span></li>
    <li class="active"><?php echo $opt['title'];?></li>
</ul>
<?php } ?>
<div class="caption">
    <p><?php echo Text::toHtml(File::getContent(STORAGE . DS . 'gallery' . DS. 'album.'. $opt['id'] .'.txt')); ?></p>
</div>
<?php
foreach($records as $row):
?>
<li><div class="thumbnail">
    <?php
    if (File::exists($opt["dir"].$row['id'].'.jpg') && $row['media'] != '') {
        ?>
        <a class="cImg" rel="group" href="<?php echo $row['media']; ?>" title="<?php echo Text::toHtml( $row['description']);?>">
            <img alt="" style="max-width:200px; max-height:100px;" src="<?php echo $opt["url"].'thumbnail/'.$row['id'].'.jpg' ?>">
        </a>
        <?php
    }
    elseif (File::exists($opt["dir"].$row['id'].'.jpg')) {
        ?>
        <a class="cImg" rel="group" href="<?php echo $opt["url"].$row['id'].'.jpg' ?>" title="<?php echo Text::toHtml( $row['description']);?>">
            <img alt="" style="max-width:200px; max-height:100px;" src="<?php echo $opt["url"].'thumbnail/'.$row['id'].'.jpg' ?>">
        </a>
        <?php
    }
    else {
        ?>
        <a class="cImg" rel="group" href="<?php echo $row['media']; ?>" title="<?php echo Text::toHtml( $row['description']);?>">
            <img style="max-width:200px; max-height:100px;"alt="" src="<?php echo $opt["url"].'no_item.jpg' ?>">
        </a>
        <?php
    }
    ?>
    <div class="caption">
        <h3><?php echo $row['title'];?></h3>
        <p><a class="btn btn-primary" href="<?php echo $opt["site_url"].'gallery/'.$opt["slug"].'/'.$row["id"];?>"><?php echo __('View','gallery')?></a></p>
    </div>
</div></li>
<?php
endforeach;
?>
