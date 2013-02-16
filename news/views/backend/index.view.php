<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('News', 'news'); ?></h2>
        <br />

        <?php if (Notification::get('success')) Alert::success(Notification::get('success')); ?>

        <?php
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
                    <td width="40%"><?php echo __('Actions', 'news'); ?></td>
                </tr>
            </thead>
            <tbody>
            <?php
                if (count($news) != 0) { 
                        foreach ($news as $news) {
                            if ($news['parent'] != '') { $dash = Html::arrow('right').'&nbsp;&nbsp;'; } else { $dash = ""; }
                $expand = NewsAdmin::$news->select('[slug="'.(string)$news['parent'].'"]', null);
                if ($news['parent'] !== '' && isset($expand['expand']) && $expand['expand'] == '1') { $visibility = 'style="display:none;"'; } else { $visibility = ''; }
             ?>
             <tr <?php echo $visibility; ?> <?php if(trim($news['parent']) !== '') {?> rel="children_<?php echo $news['parent']; ?>" <?php } ?>>  
                <td> 
                <?php
                    if (count(NewsAdmin::$news->select('[parent="'.(string)$news['slug'].'"]', 'all')) > 0) {
                        if (isset($news['expand']) && $news['expand'] == '1') {
                            echo '<a href="javascript:;" class="btn-expand parent" token="'.Security::token().'" rel="'.$news['slug'].'">+</a>';
                        } else {
                            echo '<a href="javascript:;" class="btn-expand parent" token="'.Security::token().'" rel="'.$news['slug'].'">-</a>';
                        }
                    }
                ?>
                </td> 
                <td>
                    <?php
                        $_parent = (trim($news['parent']) == '') ? '' : $news['parent'];
                        $parent  = (trim($news['parent']) == '') ? '' : $news['parent'].'/';
                        echo (trim($news['parent']) == '') ? '' : '&nbsp;';
                        echo $dash.Html::anchor(Html::toText($news['title']), $site_url.'news/'.$parent.$news['slug'], array('target' => '_blank', 'rel' => 'children_'.$_parent));
                    ?>
                </td>
                <td>
                    <?php echo $news['author']; ?>
                </td>
                <td>
                    <?php echo $news['status']; ?>
                </td>
                <td>
                    <?php echo $news['access']; ?>
                </td>
                <td>
                    <?php echo Date::format($news['date'], "j.n.Y"); ?>
                </td>
                <td>
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <?php echo Html::anchor(__('Edit', 'news'), 'index.php?id=news&action=edit_news&name='.$news['slug'], array('class' => 'btn btn-actions')); ?>
                            <a class="btn dropdown-toggle btn-actions" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php if ($news['parent'] == '') { ?>
                                    <li><a href="index.php?id=news&action=add_news&parent=<?php echo $news['slug']; ?>" title="<?php echo __('Create new news', 'news'); ?>"><?php echo __('Add', 'news'); ?></a></li>
                                <?php } ?>
                                <li><?php echo Html::anchor(__('Clone', 'news'), 'index.php?id=news&action=clone_news&name='.$news['slug'].'&token='.Security::token(), array('title' => __('Clone', 'news'))); ?></li>
                            </ul>    
                            <?php echo Html::anchor(__('Delete', 'news'),
                                       'index.php?id=news&action=delete_news&name='.$news['slug'].'&token='.Security::token(),
                                       array('class' => 'btn btn-actions btn-actions-default', 'onclick' => "return confirmDelete('".__("Delete news: :news", 'news', array(':news' => Html::toText($news['title'])))."')"));
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

        <form>
            <input type="hidden" name="url" value="<?php echo Option::get('siteurl'); ?>admin/index.php?id=news">
        </form>

    </div>
</div>