<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Articles', 'articles'); ?></h2><br />

        <?php 
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        echo (Html::anchor(__('Add articles', 'articles'), 'index.php?id=articles&action=add', array('class' => 'btn default btn-small'))).Html::Nbsp(2);
        echo (Html::anchor(__('Settings', 'articles'), 'index.php?id=articles&action=settings', array('class' => 'btn default btn-small')));
        ?>
        <br /><br />
        
        
        <ul class="articles-sort">
            <li><b><?php echo __('Sort by:', 'articles');?></b> &nbsp;</li>
            
            <li><a href="index.php?id=articles&page=<?php echo $current_page;?>&sort=date&order=<?php echo $order;?>"<?php if($sort=='date') echo ' class="current"';?>><?php echo __('by date', 'articles');?></a> <div class="articles-line">/</div></li>
            <li><a href="index.php?id=articles&page=<?php echo $current_page;?>&sort=id&order=<?php echo $order;?>"<?php if($sort=='id') echo ' class="current"';?>><?php echo __('by number', 'articles');?></a> <div class="articles-line">/</div></li>
            <li><a href="index.php?id=articles&page=<?php echo $current_page;?>&sort=hits&order=<?php echo $order;?>"<?php if($sort=='hits') echo ' class="current"';?>><?php echo __('by views', 'articles');?></a> <div class="articles-line">/</div></li>
            <li><a href="index.php?id=articles&page=<?php echo $current_page;?>&sort=status&order=<?php echo $order;?>"<?php if($sort=='status') echo ' class="current"';?>><?php echo __('by status', 'articles');?></a></li>
            
            <li class="articles-right">
                <a href="index.php?id=articles&page=<?php echo $current_page;?>&sort=<?php echo $sort;?>&order=ASC"<?php if($order=='ASC') echo ' class="current"';?>><?php echo __('ASC', 'articles');?></a>
            </li>
            <li class="articles-right">
                <a href="index.php?id=articles&page=<?php echo $current_page;?>&sort=<?php echo $sort;?>&order=DESC"<?php if($order=='DESC') echo ' class="current"';?>><?php echo __('DESC', 'articles');?></a> 
                <div class="articles-line">/</div>
            </li>
        </ul>

        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td><?php echo __('Name', 'articles'); ?></td>
                    <td><?php echo __('Author', 'articles'); ?></td>
                    <td><?php echo __('Status', 'articles'); ?></td>
                    <td><?php echo __('Hits', 'articles'); ?></td>
                    <td><?php echo __('Date', 'articles'); ?></td>
                    <td width="40%"><?php echo __('Actions', 'articles'); ?></td>
                </tr>
            </thead>
            <tbody>
            <?php if (count($articles_list) != 0): ?> 
            <?php foreach ($articles_list as $row): ?>
             <tr>        
                <td><?php echo Html::anchor(Html::toText($row['name']), $site_url.'articles/'.$row['id'].'/'.$row['slug'], 
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
                                Html::anchor(__('Edit', 'articles'), 'index.php?id=articles&action=edit&articles_id='.$row['id'], 
                                    array('class' => 'btn btn-actions btn-actions-default')).Html::Nbsp(2).
                            
                                Html::anchor(__('Delete', 'articles'), 'index.php?id=articles&action=delete&articles_id='.$row['id'].'&token='.Security::token(),
                                    array('class' => 'btn btn-actions', 
                                        'onclick' => "return confirmDelete('".__("Delete articles: :articles", 'articles', 
                                            array(':articles' => Html::toText($row['name'])))."')"))
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
        <div id="articles-paginator-admin"><?php Articles::paginator($current_page, $pages_count, 'index.php?id=articles&sort='.$sort.'&order='.$order.'&page=');?></div>
    </div>
</div>