<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('News', 'news'); ?></h2>
        <br />

        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));

        echo (
            Html::anchor(__('Create news', 'news'), 'index.php?id=news&action=add_news', array('title' => __('Create new news', 'news'), 'class' => 'btn default btn-small')). Html::nbsp(3).
                Html::anchor(__('Settings', 'news'), 'index.php?id=news&action=settings', array('class' => 'btn default btn-small'))
        );
        ?>

        <br /><br />

        <table class="table table-bordered">
            <thead>
            <tr>
                <td width="3%"></td>
                <td><?php echo __('Name', 'news'); ?></td>
                <td><?php echo __('Author', 'news'); ?></td>
                <td><?php echo __('Status', 'news'); ?></td>
                <td><?php echo __('Access', 'news'); ?></td>
                <td><?php echo __('Date', 'news'); ?></td>
                <td width="20%"><?php echo __('Actions', 'news'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($items) != 0) {
                foreach ($items as $item) {
                    if ($item['parent'] != '') { $dash = Html::arrow('right').'&nbsp;&nbsp;'; } else { $dash = ""; }
                    $expand = NewsAdmin::$news->select('[slug="'.(string)$item['parent'].'"]', null);
                    if ($item['parent'] !== '' && isset($expand['expand']) && $expand['expand'] == '1') { $visibility = 'style="display:none;"'; } else { $visibility = ''; }
                    ?>
                    <tr <?php echo $visibility; ?> <?php if(trim($item['parent']) !== '') {?> rel="children_<?php echo $item['parent']; ?>" <?php } ?>>
                        <td>
                            <?php
                            if (count(NewsAdmin::$news->select('[parent="'.(string)$item['slug'].'"]', 'all')) > 0) {
                                if (isset($item['expand']) && $item['expand'] == '1') {
                                    echo '<a href="javascript:;" class="btn-expand parent" token="'.Security::token().'" rel="'.$item['slug'].'">+</a>';
                                } else {
                                    echo '<a href="javascript:;" class="btn-expand parent" token="'.Security::token().'" rel="'.$item['slug'].'">-</a>';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $_parent = (trim($item['parent']) == '') ? '' : $item['parent'];
                            $parent  = (trim($item['parent']) == '') ? '' : $item['parent'].'/';
                            echo (trim($item['parent']) == '') ? '' : '&nbsp;';
                            echo $dash.Html::anchor(Html::toText($item['title']), $opt['site_url'].'news/'.$parent.$item['slug'], array('target' => '_blank', 'rel' => 'children_'.$_parent));
                            ?>
                        </td>
                        <td>
                            <?php echo $item['author']; ?>
                        </td>
                        <td>
                            <?php echo $item['status']; ?>
                        </td>
                        <td>
                            <?php echo $item['access']; ?>
                        </td>
                        <td>
                            <?php echo Date::format($item['date'], "j.n.Y"); ?>
                        </td>
                        <td>
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo Html::anchor(__('Edit', 'news'), 'index.php?id=news&action=edit_news&uid='.$item['id'], array('class' => 'btn btn-actions')); ?>
                                    <a class="btn dropdown-toggle btn-actions" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php /*if ($item['parent'] == '') { ?>
                                            <li><a href="index.php?id=news&action=add_news&parent=<?php echo $item['slug']; ?>" title="<?php echo __('Create new news', 'news'); ?>"><?php echo __('Add', 'news'); ?></a></li>
                                        <?php } */?>
                                        <li><?php echo Html::anchor(__('Clone', 'news'), 'index.php?id=news&action=clone_news&uid='.$item['id'].'&token='.Security::token(), array('title' => __('Clone', 'news'))); ?></li>
                                    </ul>
                                    <?php echo Html::anchor(__('Delete', 'news'),
                                        'index.php?id=news&action=delete_news&uid='.$item['id'].'&token='.Security::token(),
                                        array('class' => 'btn btn-actions btn-actions-default', 'onclick' => "return confirmDelete('".__("Delete news: :news", 'news', array(':news' => Html::toText($item['title'])))."')"));
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
        <?php echo Dev::paginator($opt['page'], $opt['pages'], 'index.php?id=news&page=');?>
    </div>
</div>