<div id="articles">
    <div class="articles-item">
        <h1><a href="<?php echo $site_url;?>articles"><?php echo __('Articles', 'articles');?></a> / <?php echo $row['h1'];?></h1>

        <div class="articles-content"><?php echo Articles::getContentShort($row['id'], false);?></div>
        
        <div class="articles-status">
            <div class="articles-fleft">
                <?php echo Date::format($row['date'], 'd.m.Y');?> / 
                <?php echo __('Hits count', 'articles');?>: <?php echo $row['hits'];?>
            </div>
            <div class="articles-fright">&nbsp;<?php Action::run('articles_item_status', array('id' => $row['id']));?></div>
        </div>

    </div><!-- /articles-item-->
</div>
<?php Action::run('articles_current_footer', array('id' => $row['id']));?>