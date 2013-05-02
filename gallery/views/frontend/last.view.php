<ul class="thumbnails">
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
</div></li>
<?php
endforeach;
?>
</ul>