<?
if (count($tags) > 0):
foreach($tags as $row):
    $records = Catalog::$catalog->select('[tags='.$row['id'].']');
    if(count($records) > 0):
?>
    <h5><?php echo $row['title'] ?></h5>
    <ul class="unstyled">
        <?php foreach($records as $item):?>
        <li><a href="<? echo '/catalog/' . $item['slug']; ?>"><? echo $item['title']; ?></a></li>
        <?php
        endforeach;
        ?>
    </ul>
    <?php
    endif;
endforeach;
endif;
?>
