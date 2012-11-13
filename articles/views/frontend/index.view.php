<h1><?php echo __('Articles', 'articles');?></h1>

<div id="articles">

    <?php if(count($records)>0):?>
    
        <div id="articles-list">
        
            <?php foreach($records as $row):?>
                <?php $articles_url = $site_url.'articles/'.$row['id'].'/'.$row['slug'];?>
                <div class="articles-item">
                
                    <h2><a href="<?php echo $articles_url;?>"><?php echo $row['name'];?></a></h2>
                    <div class="articles-content"><?php echo Articles::getContentShort($row['id'], true, $articles_url); ?></div>
                    <div class="articles-status">
                        <div class="articles-fleft">
                            <?php echo Date::format($row['date'], 'd.m.Y');?> / 
                            <?php echo __('Hits count', 'articles');?>: <?php echo $row['hits'];?>
                        </div>
                        <div class="articles-fright">&nbsp;<?php Action::run('articles_item_status', array('id' => $row['id']));?></div>
                    </div>
                </div><!-- /articles-item-->
                
            <?php endforeach;?>
            
        </div><!-- /articles-list-->
    
    <?php endif;?>
    
    <div id="articles-paginator"><?php Articles::paginator($current_page, $pages_count, $site_url.'articles/page/');?></div>
</div><!-- /articles -->