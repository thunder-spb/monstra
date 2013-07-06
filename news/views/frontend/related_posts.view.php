<?php
if (count($related_posts) > 0)
{
?>
    <br><br>
    <b><?php echo __('Related posts', 'news'); ?>:</b>
    <div>
        <?php foreach($related_posts as $related_post) { ?>
            <a href="<?php echo Option::get('siteurl'); ?>news/<?php echo $related_post['slug']; ?>"><?php echo $related_post['title']; ?></a><br>
        <?php } ?>
    </div>
<?php
}
