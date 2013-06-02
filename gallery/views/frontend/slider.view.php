<ul class="rslides">
    <?php
    foreach($items as $item):
        ?>
        <li>
            <?php
            if (File::exists($opt["dir"].$item['id'].'.jpg')) {
                ?>
                    <img alt="<?php echo Text::toHtml( $item['description']);?>" title="<?php echo Text::toHtml( $item['description']);?>" src="<?php echo $opt["url"].$item['id'].'.jpg' ?>">
            <?php
            }
            ?>
        </li>
    <?php
    endforeach;
    ?>
</ul>