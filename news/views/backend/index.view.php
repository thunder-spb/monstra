<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('News', 'news'); ?></h2><br />

        <?php 
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        echo (Html::anchor(__('Add news', 'news'), 'index.php?id=news&action=add', array('class' => 'btn default btn-small'))).Html::Nbsp(2);
        echo (Html::anchor(__('Settings', 'news'), 'index.php?id=news&action=settings', array('class' => 'btn default btn-small')));
        ?>
        <br /><br />
        
        
        <ul class="news-sort">
            <li><b><?php echo __('Sort by:', 'news');?></b> &nbsp;</li>
            
            <li><a href="index.php?id=news&page=<?php echo $current_page;?>&sort=date&order=<?php echo $order;?>"<?php if($sort=='date') echo ' class="current"';?>><?php echo __('by date', 'news');?></a> <div class="news-line">/</div></li>
            <li><a href="index.php?id=news&page=<?php echo $current_page;?>&sort=id&order=<?php echo $order;?>"<?php if($sort=='id') echo ' class="current"';?>><?php echo __('by number', 'news');?></a> <div class="news-line">/</div></li>
            <li><a href="index.php?id=news&page=<?php echo $current_page;?>&sort=hits&order=<?php echo $order;?>"<?php if($sort=='hits') echo ' class="current"';?>><?php echo __('by views', 'news');?></a> <div class="news-line">/</div></li>
            <li><a href="index.php?id=news&page=<?php echo $current_page;?>&sort=status&order=<?php echo $order;?>"<?php if($sort=='status') echo ' class="current"';?>><?php echo __('by status', 'news');?></a></li>
            
            <li class="news-right">
                <a href="index.php?id=news&page=<?php echo $current_page;?>&sort=<?php echo $sort;?>&order=ASC"<?php if($order=='ASC') echo ' class="current"';?>><?php echo __('ASC', 'news');?></a>
            </li>
            <li class="news-right">
                <a href="index.php?id=news&page=<?php echo $current_page;?>&sort=<?php echo $sort;?>&order=DESC"<?php if($order=='DESC') echo ' class="current"';?>><?php echo __('DESC', 'news');?></a> 
                <div class="news-line">/</div>
            </li>
        </ul>

        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td><?php echo __('Name', 'news'); ?></td>
                    <td><?php echo __('Author', 'news'); ?></td>
                    <td><?php echo __('Status', 'news'); ?></td>
                    <td><?php echo __('Hits', 'news'); ?></td>
                    <td><?php echo __('Date', 'news'); ?></td>
                    <td width="40%"><?php echo __('Actions', 'news'); ?></td>
                </tr>
            </thead>
            <tbody>
            <?php if (count($news_list) != 0): ?> 
            <?php foreach ($news_list as $row): ?>
             <tr>        
                <td><?php echo Html::anchor(Html::toText($row['name']), $site_url.'news/'.$row['id'].'/'.$row['slug'], 
                    array('target' => '_blank')); ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $status_array[$row['status']]; ?></td>
                <td><?php echo $row['hits']; ?></td>
                <td><?php echo Date::format($row['date'], "j.n.Y"); ?></td>
                <td>
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <?php 
                            echo (
                                Html::anchor(__('Edit', 'news'), 'index.php?id=news&action=edit&news_id='.$row['id'], 
                                    array('class' => 'btn btn-actions btn-actions-default')).Html::Nbsp(2).
                            
                                Html::anchor(__('Delete', 'news'), 'index.php?id=news&action=delete&news_id='.$row['id'].'&token='.Security::token(),
                                    array('class' => 'btn btn-actions', 
                                        'onclick' => "return confirmDelete('".__("Delete news: :news", 'news', 
                                            array(':news' => Html::toText($row['name'])))."')"))
                            );
                            ?>
                        </div>
                    </div>  
                </td>
             </tr> 
            <?php
            endforeach;
            endif;
            ?>
            </tbody>
        </table>
        <div id="news-paginator-admin"><?php News::paginator($current_page, $pages_count, 'index.php?id=news&sort='.$sort.'&order='.$order.'&page=');?></div>
    </div>
</div>