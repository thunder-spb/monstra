<h2><?php echo __('Tags', 'catalog');?></h2>

<?php
if (Notification::get('success')) Alert::success(Notification::get('success'));
if (Notification::get('error')) Alert::success(Notification::get('error'));
?>
<table class="table table-bordered">
    <thead>
    <tr>
        <td><?php echo __('Title', 'catalog'); ?></td>
        <td><?php echo __('Sorting', 'catalog'); ?></td>
        <td width="20%"><?php echo __('Actions', 'catalog'); echo Html::Nbsp(2); ?><span class="btn btn-small" data-action="tag" data-key="0"><?php echo __('Add', 'catalog') ?></span></td>
    </tr>
    </thead>
    <tbody>
    <?php if (count($items) != 0): ?>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['sorting']; ?></td>
                <td>
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <span class="btn" data-action="tag" data-key="<?php echo $row['id']; ?>"><?php echo __('Edit', 'catalog') ?></span>
                            <?php
                            echo (
                                    Html::anchor(__('Delete', 'catalog'), 'index.php?id=catalog&action=delete&tag_id='.$row['id'].'&token='.Security::token(),
                                        array('class' => 'btn btn-actions',
                                            'onclick' => "return confirmDelete('".__("Delete tag: :catalog", 'catalog',
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

<div id="imgModal" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?php echo __('Edit tag', 'catalog'); ?></h3>
    </div>
    <div class="modal-body">
        <?php
        echo (
            Form::open(null, array('class' => 'form_validate form-horizontal')).
                Form::hidden('csrf', Security::token()).
                Form::hidden('catalog__uid', 0).
                '<div class="control-group">'.
                Form::label('catalog_title', __('Title', 'catalog'), array('class' => 'control-label')).
                '<div class="controls">'.
                Form::input('catalog_title', '', array('class' => 'required span10')).
                '</div>'.
                '</div>'.
                '<div class="control-group">'.
                Form::label('catalog_sort', __('Sorting', 'catalog'), array('class' => 'control-label')).
                '<div class="controls">'.
                Form::input('catalog_sort', '', array('class' => 'required span10')).
                '</div>'.
                '</div>'
        );
        ?>
    </div>
    <div class="modal-footer">
        <span data-action="close" class="btn"><?php echo __('Close', 'catalog')?></span>
        <?php
        echo (
            Form::submit('catalog_save_tag', __('Save', 'catalog'), array('class' => 'btn btn-primary')).
                Form::close()
        );
        ?>
    </div>
</div>