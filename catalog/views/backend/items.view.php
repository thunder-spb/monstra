<div class="row-fluid">
    <div class="span12">
        <h2><a href="<?php echo Url::base(); ?>/index.php?id=catalog"><?php echo __('Catalog', 'catalog'); ?></a>: <?php echo $opt['catalog']['title'] ?></h2><br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));
        echo (Html::anchor(__('Add Item', 'catalog'), 'index.php?id=catalog&action=add&catalog_id='.$opt['cid'], array('class' => 'btn default btn-small'))).Html::Nbsp(2);
        ?>
        <br /><br />


        <ul class="breadcrumb">
            <li><b><?php echo __('Sort by:', 'catalog');?></b> &nbsp;</li>

            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog&action=items&catalog_id=<?php echo $opt['cid'];?>&page=<?php echo $opt['page'];?>&sort=date&order=<?php echo $opt['order'];?>"<?php if($opt['sort']=='date') echo ' class="active"';?>><?php echo __('by date', 'catalog');?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog&action=items&catalog_id=<?php echo $opt['cid'];?>&page=<?php echo $opt['page'];?>&sort=id&order=<?php echo $opt['order'];?>"<?php if($opt['sort']=='id') echo ' class="active"';?>><?php echo __('by number', 'catalog');?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog&action=items&catalog_id=<?php echo $opt['cid'];?>&page=<?php echo $opt['page'];?>&sort=hits&order=<?php echo $opt['order'];?>"<?php if($opt['sort']=='hits') echo ' class="active"';?>><?php echo __('by views', 'catalog');?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog&action=items&catalog_id=<?php echo $opt['cid'];?>&page=<?php echo $opt['page'];?>&sort=status&order=<?php echo $opt['order'];?>"<?php if($opt['sort']=='status') echo ' class="active"';?>><?php echo __('by status', 'catalog');?></a> <span class="divider">/</span></li>
            <li>&nbsp; <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog&action=items&catalog_id=<?php echo $opt['cid'];?>&page=<?php echo $opt['page'];?>&sort=<?php echo $opt['sort'];?>&order=ASC"<?php if($opt['order']=='ASC') echo ' class="active"';?>><?php echo __('by ASC', 'catalog');?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=catalog&action=items&catalog_id=<?php echo $opt['cid'];?>&page=<?php echo $opt['page'];?>&sort=<?php echo $opt['sort'];?>&order=DESC"<?php if($opt['order']=='DESC') echo ' class="active"';?>><?php echo __('by DESC', 'catalog');?></a> <span class="divider">/</span></li>
        </ul>


        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?php echo __('Title', 'catalog'); ?></td>
                <td><?php echo __('Photo', 'catalog'); ?></td>
                <td><?php echo __('Price', 'catalog'); ?></td>
                <td><?php echo __('ShortCode', 'catalog'); ?></td>
                <td><?php echo __('Status', 'catalog'); ?></td>
                <td><?php echo __('Hits', 'catalog'); ?></td>
                <td><?php echo __('Date', 'catalog'); ?></td>
                <td width="20%"><?php echo __('Actions', 'catalog'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php if (count($items) != 0): ?>
                <?php foreach ($items as $row): ?>
                <tr>
                    <td><?php echo Html::anchor($row['title'], $opt['site_url'].'catalog/'.$opt['catalog']['slug'].'/item/'.$row['id'], array('target' => '_blank')); ?></td>
                    <td class="image">
                        <?php if (File::exists($opt['dir'].$row['id'].'.jpg')) { ?>
                            <a href="#" rel="<?php echo $opt['url'].$row['id'].'.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].$row['id'].'.jpg' ?>"></a>
                        <?php } ?>
                    </td>
                    <td><?php echo $row['price']; ?></td>
                    <td>{catalog list="item" uid=<?php echo $row['id']; ?>}</td>
                    <td><?php echo $opt['status'][$row['status']]; ?></td>
                    <td><?php echo $row['hits']; ?></td>
                    <td><?php echo Date::format($row['date'], "j.n.Y"); ?></td>
                    <td>
                        <div class="btn-toolbar">
                            <div class="btn-group">
                                <?php
                                echo (
                                    Html::anchor(__('Edit', 'catalog'), 'index.php?id=catalog&action=edit&item_id='.$row['id'],
                                        array('class' => 'btn btn-actions btn-actions-default')).Html::Nbsp(2).

                                        Html::anchor(__('Delete', 'catalog'), 'index.php?id=catalog&action=delete&item_id='.$row['id'].'&token='.Security::token(),
                                            array('class' => 'btn btn-actions',
                                                'onclick' => "return confirmDelete('".__("Delete item: :catalog", 'catalog',
                                                    array(':catalog' => Html::toText($row['name'])))."')"))
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
        <?php Dev::paginator($opt['page'], $opt['pages'], 'index.php?id=catalog&action=items&catalog_id='.$opt['cid'].'&sort='.$opt['sort'].'&order='.$opt['order'].'&page=');?>
    </div>
</div>
<div id="previewLightbox" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class='lightbox-header'>
        <button type="button" class="close" data-dismiss="lightbox" aria-hidden="true">&times;</button>
    </div>
    <div class='lightbox-content'>
        <img />
    </div>
</div>