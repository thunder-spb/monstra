<h1><?php echo __('News', 'news');?></h1>

<div id="news">

    <?php if(count($records)>0):?>
    
        <div id="news-list">
        
            <?php foreach($records as $row):?>
                <?php $news_url = $site_url.'news/'.$row['id'].'/'.$row['slug'];?>
                <div class="news-item">
                
                    <h2><a href="<?php echo $news_url;?>"><?php echo $row['name'];?></a></h2>
                    <div class="news-content"><?php echo News::getContentShort($row['id'], true, $news_url); ?></div>
                    <div class="news-status">
                        <div class="news-fleft">
                            <?php echo Date::format($row['date'], 'd.m.Y');?> / 
                            <?php echo __('Hits count', 'news');?>: <?php echo $row['hits'];?>
                        </div>
                        <div class="news-fright">&nbsp;<?php Action::run('news_item_status', array('id' => $row['id']));?></div>
                    </div>
                </div><!-- /news-item-->
                
            <?php endforeach;?>
            
        </div><!-- /news-list-->
    
    <?php endif;?>
    
    <div id="news-paginator"><?php News::paginator($current_page, $pages_count, $site_url.'news/page/');?></div>
</div><!-- /news -->