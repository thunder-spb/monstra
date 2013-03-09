<?php
foreach($records as $row):
	if ($opt["title"] == 0)
	{
		echo "<div class=\"cTitle\">".$row['title']."</div>";
	}
	if ($opt["img"] == 0 && file_exists($opt["dir"].$row['id'].'_t.jpg'))
	{
		?><div class="cFoto"><a href="<?php echo $opt["url"].$row['id'].'_o.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt["url"].$row['id'].'_t.jpg' ?>"></a></div><?php
	}
	if ($opt["price"] == 0)
	{
		echo "<div class=\"cPrice\">".$row['price']." ".$row['currency']."</div>";
	}
	if ($opt["desc"] == 0)
	{
		echo "<div class=\"desc\">".Text::toHtml(File::getContent(STORAGE . DS . 'catalog' . DS . $row['id'] . '.catalog.txt'))."</div>";
	}
endforeach;