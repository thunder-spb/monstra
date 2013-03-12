<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Gallery', 'gallery'); ?></h2><br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));
        echo (Html::anchor(__('Add album', 'gallery'), 'index.php?id=gallery&action=add', array('class' => 'btn default btn-small'))).Html::Nbsp(2);
        //echo (Html::anchor(__('Desc', 'gallery'), 'index.php?id=gallery&action=desc', array('class' => 'btn default btn-small'))).Html::Nbsp(2);
        echo (Html::anchor(__('Settings', 'gallery'), 'index.php?id=gallery&action=settings', array('class' => 'btn default btn-small')));
        ?>
        <br /><br />

        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?php echo __('Title', 'gallery'); ?></td>
                <td><?php echo __('Slug', 'gallery'); ?></td>
                <td><?php echo __('ShortCode', 'gallery'); ?></td>
                <td width="20%"><?php echo __('Actions', 'gallery'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php if (count($items) != 0): ?>
                <?php foreach ($items as $row): ?>
                <tr>
                    <td><?php echo Html::anchor($row['title'], $opt['site_url'].'gallery/'.$row['slug'], array('target' => '_blank')); ?></td>
                    <td><?php echo $row['slug']; ?></td>
                    <td>{gallery list="album" slug="<?php echo $row['slug']; ?>" sort="date" order="DESC"}</td>
                    <td>
                        <div class="btn-toolbar">
                            <div class="btn-group">
                                <?php echo Html::anchor(__('View', 'gallery'), 'index.php?id=gallery&action=items&gallery_id='.$row['id'], array('class' => 'btn btn-actions btn-actions-default')); ?>
                                <a class="btn dropdown-toggle btn-actions" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><?php echo Html::anchor(__('Edit', 'gallery'), 'index.php?id=gallery&action=edit&gallery_id='.$row['id']); ?></li>
                                    <li>
                                        <?php echo Html::anchor(__('Delete', 'gallery'), 'index.php?id=gallery&action=delete&gallery_id='.$row['id'].'&token='.Security::token(),
                                        array(
                                            'onclick' => "return confirmDelete('".__("Delete album: :gallery", 'gallery',
                                                array(':gallery' => Html::toText($row['title'])))."')"))
                                        ?>
                                    </li>
                                </ul>
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
        <?php echo Dev::paginator($opt['page'], $opt['pages'], 'index.php?id=gallery&page=');?>
    </div>
</div>