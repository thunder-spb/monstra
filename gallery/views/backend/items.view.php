<div class="row-fluid">
    <div class="span12">
        <h2><a href="<?php echo Url::base(); ?>/index.php?id=gallery"><?php echo __('Gallery', 'gallery'); ?></a>: <?php echo $opt['title'] ?></h2><br />
        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));
        echo (
            Form::open(null, array('enctype' => 'multipart/form-data', 'id' => 'fileupload')).
            Form::hidden('csrf', Security::token()).
            Form::hidden('gid', $opt['id']).
            Form::hidden('upf', '0')
        );
        ?>
            <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
            <div class="fileupload-buttonbar">
                <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-info" data-action="add_media">
                    <i class="icon-plus icon-white"></i>
                    <span><?php echo __('Add video', 'gallery'); ?></span>
                </span>
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span><?php echo __('Add images', 'gallery'); ?></span>
                    <input type="file" name="files[]" multiple>
                </span>
                <span class="btn btn-primary upload">
                    <i class="icon-upload icon-white"></i>
                    <span class="start"><?php echo __('Start upload', 'gallery'); ?></span>
                </span>
                </div>

            </div>
            <!-- The global progress information -->
            <div id="progress" class="span5 fileupload-progress fade">
                <div class="progress progress-striped">
                  <div class="bar" style="width: 0%;"></div>
                </div>
            </div>
            <br><br>
        <?php echo Form::close(); ?>
        <ul class="breadcrumb">
            <li><b><?php echo __('Sort by:', 'gallery');?></b> &nbsp;</li>

            <li><a href="<?php echo Url::base(); ?>/index.php?id=gallery&action=items&gallery_id=<?php echo $opt['id'];?>&page=<?php echo $opt['page'];?>&sort=date&order=<?php echo $opt['order'];?>"<?php if($opt['sort']=='date') echo ' class="active"';?>><?php echo __('by date', 'gallery');?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=gallery&action=items&gallery_id=<?php echo $opt['id'];?>&page=<?php echo $opt['page'];?>&sort=id&order=<?php echo $opt['order'];?>"<?php if($opt['sort']=='id') echo ' class="active"';?>><?php echo __('by number', 'gallery');?></a> <span class="divider">/</span></li>
            <li>&nbsp; <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=gallery&action=items&gallery_id=<?php echo $opt['id'];?>&page=<?php echo $opt['page'];?>&sort=<?php echo $opt['sort'];?>&order=ASC"<?php if($opt['order']=='ASC') echo ' class="active"';?>><?php echo __('by ASC', 'gallery');?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo Url::base(); ?>/index.php?id=gallery&action=items&gallery_id=<?php echo $opt['id'];?>&page=<?php echo $opt['page'];?>&sort=<?php echo $opt['sort'];?>&order=DESC"<?php if($opt['order']=='DESC') echo ' class="active"';?>><?php echo __('by DESC', 'gallery');?></a> <span class="divider">/</span></li>
        </ul>


        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?php echo __('Title', 'gallery'); ?></td>
                <td><?php echo __('Description', 'gallery'); ?></td>
                <td><?php echo __('Photo', 'gallery'); ?></td>
                <td><?php echo __('Date', 'gallery'); ?></td>
                <td width="15%">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <span class="btn btn-actions btn-actions-default"><input type="checkbox" data-action="checked"></span>
                            <a class="btn dropdown-toggle btn-actions" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                            <ul class="dropdown-menu"><li>
                            <?php
                                echo '<a href="#" data-action="delete" data-confirm="'.__("Are you sure you want to delete all the pictures?", 'gallery').'">
                            '.__('Delete all checked', 'gallery').
                                '</a>';
                                ?>
                            </li></ul>
                        </div>
                    </div>
                </td>
            </tr>
            </thead>
            <tbody>
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $row): ?>
                <tr>
                    <td><?php echo $row['title'];//Html::anchor(Html::toText($row['name']), $site_url.'gallery/'.$row['id'].'/'.$row['slug'], array('target' => '_blank')); ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php
                        if (file_exists($opt['dir'].$row['id'].'.jpg'))
                        {
                            ?>
                            <a href="<?php echo $opt['url'].$row['id'].'.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].'thumbnail/'.$row['id'].'.jpg' ?>"></a>
                            <?php } ?>
                    </td>
                    <td><?php echo Date::format($row['date'], "j.n.Y"); ?></td>
                    <td>
                        <div class="btn-toolbar">
                            <div class="btn-group">
                                <?php
                                echo (
                                    '<span class="btn"><input type="checkbox" name="key" value="'.$row['id'].'"></span>'.
                                    '<span class="btn" data-action="image" data-key="'.$row['id'].'">'.__('Edit', 'gallery').'</span>'
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
        <?php echo Dev::paginator($opt['page'], $opt['pages'], 'index.php?id=gallery&action=items&gallery_id='.$opt['id'].'&sort='.$opt['sort'].'&order='.$opt['order'].'&page=');?>
    </div>
</div>
<div id="imgModal" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?php echo __('Media', 'gallery'); ?></h3>
    </div>
    <div class="modal-body">
        <?php
        echo (
            Form::open(null, array('class' => 'form_validate form-horizontal')).
            Form::hidden('csrf', Security::token()).
            Form::hidden('uid', 0).
            Form::hidden('gid', $opt['id']).
            '<div class="control-group">'.
                Form::label('gallery_title', __('Title', 'gallery'), array('class' => 'control-label')).
                '<div class="controls">'.
                Form::input('gallery_title', '', array('class' => 'required span10')).
                '</div>'.
            '</div>'.
            '<div class="control-group">'.
                Form::label('gallery_media', __('Media', 'gallery'), array('class' => 'control-label')).
                '<div class="controls">'.
                Form::input('gallery_media', '', array('class' => 'span10')).
            '</div>'.
            '</div>'.
                '<div class="control-group">'.
                Form::label('gallery_desc', __('Description', 'gallery'), array('class' => 'control-label')).
                '<div class="controls">'.
                Form::textarea('gallery_desc', '', array('class' => 'required span10')).
                '</div>'.
            '</div>'
        );
        ?>
    </div>
    <div class="modal-footer">
        <span data-action="close" class="btn"><?php echo __('Close', 'gallery'); ?></span>
        <?php
        echo (
            Form::submit('gallery_save_image', __('Save', 'gallery'), array('class' => 'btn btn-primary')).
            Form::close()
        );
        ?>
    </div>
</div>
