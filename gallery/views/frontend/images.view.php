<?php
foreach($records as $row):
?>
<li><div class="thumbnail">
    <?php
    if (file_exists($opt["dir"].$row['id'].'.jpg'))
    {
        ?>
        <a class="cImg" rel="group" href="<?php echo $opt["img"].$row['id'].'.jpg' ?>" title="<?php echo Text::toHtml( $row['description']);?>">
            <img alt="" style="max-width:200px; max-height:100px;" src="<?php echo $opt["img"].'thumbnail/'.$row['id'].'.jpg' ?>">
        </a>
        <?php
    }
    ?>
    <div class="caption">
        <h3><?php echo $row['title'];?></h3>
        <p><?php echo Text::toHtml( $row['description']);?></p>
    </div>
</div></li>
<?php
endforeach;
?>
