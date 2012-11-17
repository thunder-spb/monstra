<?php 

    // Admin Navigation: add new item
    Navigation::add(__('Slider', 'slider'), 'content', 'slider', 10);

    /**
     * Slider admin class
     */
    class SliderAdmin extends Backend {

		public static $dir_img = '';

		public static function main() {
			SliderAdmin::$dir_img = STORAGE . DS . 'slider' . DS;
			// Get table
			$tCat = new Table('slider_cat');
			$tImg = new Table('slider_img');

			// Select all records
			$catalog = $tCat->select(null, 'all');
			$img = $tImg->select(null, 'all');
			$cat = array();
			$url = array();
			foreach($catalog as $item)
			{
				$cat[$item['id']] = $item['title'];
				$url[$item['id']] = $item['url'];
			}

			if (Request::post('slider_submit_catalog')) {
				if (Security::check(Request::post('csrf'))) {
					$data = array(
						'title'		=>Request::post('title'),
						'sort'		=>Request::post('sort'),
						'url'		=>Request::post('url')
					);

					if ($tCat->insert($data))
					{
						File::setContent(SliderAdmin::$dir_img.$data['url'].'.txt', '');
						Request::redirect('index.php?id=slider#catalog');
					}
				}
			}

			if (Request::post('slider_submit_image')) {
				if (Security::check(Request::post('csrf'))) {
					$cid = Request::post('cat');
					$data = array(
						'img'		=>Request::post('img'),
						'url'		=>Request::post('url'),
						'title'		=>Request::post('title'),
						'sort'		=>Request::post('sort'),
						'cat'		=>Request::post('cat')
					);

					if ($tImg->insert($data))
					{
						$rCat = $tCat->select('[id='.$data['cat'].']');
						SliderAdmin::cache($tImg->select('[cat='.$cid.']'),  $rCat[0]['url']);
						Request::redirect('index.php?id=slider#photo');
					}
				}
			}

			Notification::setNow('upload', 'upload!');

			if (Request::get('action')) {
				switch (Request::get('action')) {
					case "del_cat":
						if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin'))) {
							// Delete catalog
							if (Request::get('uid')) {
								if (Security::check(Request::get('token'))) {
									$uid = (int)Request::get('uid');
									$tCat->delete($uid);
									$records = $tImg->select('[cat='.$uid.']');
									if ( ! empty($records)) {
										foreach($records as $item) {
											$tImg->deleteWhere('[id='.$item['id'].']');
										}
									}

									File::delete(SliderAdmin::$dir_img.Request::get('url').'.txt');
									Request::redirect('index.php?id=slider');
								}
								else {
									die('csrf detected!');
								}
							}
						}
					break;

					case "del_img":
						if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin'))) {
							// Delete image
							if (Request::get('uid')) {
								if (Security::check(Request::get('token'))) {
									$cid = (int)Request::get('cat');
									$tImg->delete((int)Request::get('uid'));
									SliderAdmin::cache($tImg->select('[cat='.$cid.']'), $cid);
									Request::redirect('index.php?id=slider');
								}
								else {
									die('csrf detected!');
								}
							}
						}
					break;

					case "edit_cat":
						if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin'))) {
							if (Request::post('slider_edit_catalog')) {
								$data = array(
									'title'		=>Request::post('title'),
									'sort'		=>Request::post('sort'),
									'url'		=>Request::post('url')
								);
								$id = Request::post('id');

								if (Security::check(Request::post('csrf'))) {
									if ($tCat->update($id, $data)) {
										Notification::set('success', __('Your changes have been saved.', 'users'));
										File::rename(SliderAdmin::$dir_img.$url[$id].'.txt', SliderAdmin::$dir_img.$data['url'].'.txt');
										Request::redirect('index.php?id=slider&action=edit_cat&uid='.$id);
									}
								}
							}
									// Get current cat record
							$cat = $tCat->select("[id='".(int)Request::get('uid')."']", null);
							if (Request::get('uid')) {
								// Display view
								View::factory('slider/views/backend/cat')
									->assign('catalog', $cat)
									->display();
							}
							else {
								die('csrf detected!');
							}
						}
					break;

					case "edit_img":
						if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin'))) {
							if (Request::post('slider_edit_image')) {
								$data = array(
									'small'		=>'',
									'img'		=>Request::post('img'),
									'url'		=>Request::post('url'),
									'title'		=>Request::post('title'),
									'sort'		=>Request::post('sort'),
									'cat'		=>Request::post('cat')
								);
								$id = Request::post('id');

								if (Security::check(Request::post('csrf'))) {
									if ($tImg->update($id, $data)) {
										Notification::set('success', __('Your changes have been saved.', 'users'));
										$rCat = $tCat->select('[id='.$data['cat'].']');
										$dCat = $tImg->select('[cat='.$data['cat'].']');

										SliderAdmin::cache($dCat, $rCat[0]['url']);
										Request::redirect('index.php?id=slider&action=edit_img&uid='.$id);
									}
								}
							}
							// Get current cat record
							$img = $tImg->select("[id='".(int)Request::get('uid')."']", null);
							if (Request::get('uid')) {
								// Display view
								View::factory('slider/views/backend/img')
									->assign('sImg', $img)
									->assign('sCat',$cat)
									->display();
							}
							else {
								die('csrf detected!');
							}
						}
						break;
				}
			}
			else
			{
				// Display view
				View::factory('slider/views/backend/index')
					->assign('catalog', $catalog)
					->assign('sImg', $img)
					->assign('sCat',$cat)
					->display();
			}
		}

		public static function cache($img, $cat){
			File::setContent(SliderAdmin::$dir_img.$cat.'.js', '$(window).load(function(){$(\'#sl_'.$cat.'\').flexslider();});');
			$print = '<div class="flexslider" id="sl_'.$cat.'">
	<ul class="slides">';
			foreach($img as $item)
			{
				$print .= '
		<li>
		';
				if ($item['url'] != '')
				{
					$print .= '	<a href="'.$item['url'].'"><img src="'.$item['img'].'" /></a>';
				}
				else
				{
					$print .= '	<img src="'.$item['img'].'" />';
				}

				if ($item['title'] != '')
				{
					$print .= '
			<p class="flex-caption">'.$item['title'].'</p>';
				}
				$print .= '
		</li>';
			}
			$print .= '
	</ul>
</div>';
			File::setContent(SliderAdmin::$dir_img.$cat.'.txt', $print);
		}
	}