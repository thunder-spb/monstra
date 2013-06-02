<ul class="thumbnails">
<?php
foreach($records as $item):
?>
<li><div class="thumbnail">
    <?php
    if (File::exists($opt["dir"].$item['id'].'.jpg') && $item['media'] != '') {
        ?>
        <a class="cImg" rel="group" href="<?php echo $item['media']; ?>" title="<?php echo Text::toHtml( $item['description']);?>">
            <img alt="" style="max-width:200px; max-height:100px;" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>">
        </a>
        <?php
    }
    elseif (File::exists($opt["dir"].$item['id'].'.jpg')) {
        ?>
        <a class="cImg" rel="group" href="<?php echo $opt["url"].$item['id'].'.jpg' ?>" title="<?php echo Text::toHtml( $item['description']);?>">
            <img alt="" style="max-width:200px; max-height:100px;" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>">
        </a>
        <?php
    }
    else {
        ?>
        <a class="cImg" rel="group" href="<?php echo $item['media']; ?>" title="<?php echo Text::toHtml( $item['description']);?>">
            <img style="max-width:200px; max-height:100px;"alt="" src="<?php echo $opt["url"].'no_item.jpg' ?>">
        </a>
        <?php
    }
    ?>
</div></li>
<?php
endforeach;
?>
</ul>