<div class="row-fluid">
    <div class="span12">
        <h2><?php echo __('Catalog', 'catalog'); ?></h2><br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));
        echo (Html::anchor(__('Add Catalog', 'catalog'), 'index.php?id=catalog&action=cat_add', array('class' => 'btn default btn-small'))).Html::Nbsp(2);
        //echo (Html::anchor(__('Desc', 'catalog'), 'index.php?id=catalog&action=desc', array('class' => 'btn default btn-small'))).Html::Nbsp(2);
        echo (Html::anchor(__('Settings', 'catalog'), 'index.php?id=catalog&action=settings', array('class' => 'btn default btn-small')));
        ?>
        <br /><br />

        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?php echo __('Title', 'catalog'); ?></td>
                <td><?php echo __('Slug', 'catalog'); ?></td>
                <td><?php echo __('ShortCode', 'catalog'); ?></td>
                <td width="30%"><?php echo __('Actions', 'catalog'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php if (count($items) != 0): ?>
                <?php foreach ($items as $row): ?>
                <tr>
                    <td><a href="/catalog/<?php echo $row['slug']; ?>" target="_blank"><?php echo $row['title']; ?></td>
                    <td><?php echo $row['slug']; ?></td>
                    <td>{catalog list="cat" uid=<?php echo $row['id']; ?>}</td>
                    <td>
                        <div class="btn-toolbar">
                            <div class="btn-group">
                                <?php
                                echo (
                                    Html::anchor(__('View', 'catalog'), 'index.php?id=catalog&action=items&catalog_id='.$row['id'],
                                        array('class' => 'btn btn-actions btn-actions-default')).Html::Nbsp(2).

                                        Html::anchor(__('Edit', 'catalog'), 'index.php?id=catalog&action=cat_edit&catalog_id='.$row['id'],
                                            array('class' => 'btn btn-actions btn-actions-default')).Html::Nbsp(2).

                                        Html::anchor(__('Delete', 'catalog'), 'index.php?id=catalog&action=delete&catalog_id='.$row['id'].'&token='.Security::token(),
                                            array('class' => 'btn btn-actions',
                                                'onclick' => "return confirmDelete('".__("Delete catalog: :catalog", 'catalog',
                                                    array(':catalog' => Html::toText($row['title'])))."')"))
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
        <?php Catalog::paginator($opt['page'], $opt['pages'], 'index.php?id=catalog&page=');?>
    </div>
</div>