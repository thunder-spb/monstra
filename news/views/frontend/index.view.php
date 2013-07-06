<h1><?php echo __('News', 'news');?></h1>

<div id="news">

    <?php if(count($records)>0):?>

    <ul class="thumbnails">
        <?php
        foreach($records as $item):
            $url_item = $opt["site_url"].'news/'.$item["slug"];
            ?>
            <li class="span12">
                <div class="thumbnail media">
                    <?php if (File::exists($opt["dir"].$item['id'].'.jpg')) { ?>
                        <a class="pull-left" href="<?php echo $url_item; ?>"><img class="img-polaroid" alt="<?php echo $item['title'] ?>" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
                    <?php }
                    else{ ?>
                        <a class="pull-left" href="<?php echo $url_item; ?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
                    <?php }?>
                    <div class="media-body breadcrumb">
                        <h4 class="media-heading"><?php echo $item['title'] ?></h4>
                        <p><?php echo News::getNewsContent($item['id'], false); ?></p>
                        <p><i class="icon-calendar"></i><?php echo Date::format($item['date'], "j.n.Y"); ?> <a href="<?php echo $url_item; ?>"><?php echo __('Read more', 'news') ?></a></p>
                    </div>
                </div>
            </li>
        <?php
        endforeach; ?>
    </ul>
    <?php endif;
    echo News::getTags();
    if (Request::get('tag')) {
        $page_url[0] = $opt["site_url"].'news/page/';
        $page_url[1] = '?tag='.Request::get('tag');
    }
    else{
        $page_url = $opt["site_url"].'news/page/';
    }
    ?>
    <?php echo Dev::paginator($opt['page'], $opt['pages'], $page_url);?>
</div><!-- /news -->