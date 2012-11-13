<div id="news">
    <div class="news-item">
        <h1><a href="<?php echo $site_url;?>news"><?php echo __('News', 'news');?></a> / <?php echo $row['h1'];?></h1>

        <div class="news-content"><?php echo News::getContentShort($row['id'], false);?></div>
        
        <div class="news-status">
            <div class="news-fleft">
                <?php echo Date::format($row['date'], 'd.m.Y');?> / 
                <?php echo __('Hits count', 'news');?>: <?php echo $row['hits'];?>
            </div>
            <div class="news-fright">&nbsp;<?php Action::run('news_item_status', array('id' => $row['id']));?></div>
        </div>

    </div><!-- /news-item-->
</div>
<?php Action::run('news_current_footer', array('id' => $row['id']));?>