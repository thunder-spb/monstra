<?php 
	Navigation::add(__('News', 'news'), 'content', 'news', 10);

	Action::add('admin_themes_extra_index_template_actions','NewsAdmin::formComponent');
	Action::add('admin_themes_extra_actions','NewsAdmin::formComponentSave');

    class NewsAdmin extends Backend {

		/**
		 * News tables
		 *
		 * @var object
		 */
		public static $news = null;

		/**
		 * News admin function
		 */
		public static function main() {

			$site_url = Option::get('siteurl');

			$errors = array();

			$news = new Table('news');
			NewsAdmin::$news = $news;

			$users = new Table('users');
			$user = $users->select('[id='.Session::get('user_id').']', null);

			$user['firstname'] = Html::toText($user['firstname']);
			$user['lastname']  = Html::toText($user['lastname']);

			// Page author
			if ( ! empty($user['firstname'])) {
				$author = (empty($user['lastname'])) ? $user['firstname'] : $user['firstname'].' '.$user['lastname'];
			} else {
				$author = Session::get('user_login');
			}


			// Status array
			$status_array = array('published' => __('Published', 'news'),
				'draft'     => __('Draft', 'news'));


			// Access array
			$access_array = array('public'      => __('Public', 'news'),
				'registered'  => __('Registered', 'news'));

            $url_img = $site_url . 'public/uploads/news/';
            $dir_img = ROOT . DS . 'public' . DS . 'uploads' . DS . 'news' . DS;
            /**
             *  Upload image
             */
            if (Request::post('upload_file')) {
                if (Security::check(Request::post('csrf'))) {
                    $uid = Request::post('id');
                    $slug = Request::post('name');
                    if ($_FILES['file']) {
                        if($_FILES['file']['type'] == 'image/jpeg' ||
                            $_FILES['file']['type'] == 'image/png' ||
                            $_FILES['file']['type'] == 'image/gif') {

                            $img  = Image::factory($_FILES['file']['tmp_name']);

                            $wmax   = (int)Option::get('news_wmax');
                            $hmax   = (int)Option::get('news_hmax');
                            $width  = (int)Option::get('news_w');
                            $height = (int)Option::get('news_h');
                            $resize = Option::get('news_resize');

                            $ratio = $width/$height;

                            if ($img->width > $wmax or $img->height > $hmax) {
                                if ($img->height > $img->width) {
                                    $img->resize($wmax, $hmax, Image::HEIGHT);
                                } else {
                                    $img->resize($wmax, $hmax, Image::WIDTH);
                                }
                            }
                            $img->save($dir_img . $uid.'_o.jpg');

                            switch ($resize) {
                                case 'width' :   $img->resize($width, $height, Image::WIDTH);  break;
                                case 'height' :  $img->resize($width, $height, Image::HEIGHT); break;
                                case 'stretch' : $img->resize($width, $height); break;
                                default :
                                    // crop
                                    if (($img->width/$img->height) > $ratio) {
                                        $img->resize($width, $height, Image::HEIGHT)->crop($width, $height, round(($img->width-$width)/2),0);
                                    } else {
                                        $img->resize($width, $height, Image::WIDTH)->crop($width, $height, 0, 0);
                                    }
                                    break;
                            }
                            $img->save($dir_img . $uid.'_t.jpg');
                        }
                    }
                    Request::redirect('index.php?id=news&action=edit_news&upload=1&name='.$slug);
                } else { die('csrf detected!'); }
            }
            
			// Check for get actions
			// ---------------------------------------------
			if (Request::get('action')) {

				// Switch actions
				// -----------------------------------------
				switch (Request::get('action')) {

					// Settings
					// -------------------------------------
					case "settings":

						if (Request::post('news_submit_settings_cancel')) {
							Request::redirect('index.php?id=news');
						}

						if (Request::post('news_submit_settings')) {
							if (Security::check(Request::post('csrf'))) {
								Option::update(array(
									'news_limit'  => (int)Request::post('limit'),
									'news_limit_admin' => (int)Request::post('limit_admin'),
                                    'news_w' => (int)Request::post('width_thumb'),
                                    'news_h' => (int)Request::post('height_thumb'),
                                    'news_wmax'   => (int)Request::post('width_orig'),
                                    'news_hmax'   => (int)Request::post('height_orig'),
                                    'news_resize' => (string)Request::post('resize')
                                ));

								Notification::set('success', __('Your changes have been saved', 'news'));

								Request::redirect('index.php?id=news');
							} else { die('csrf detected!'); }
						}

						View::factory('news/views/backend/settings')->display();
						Action::run('admin_news_extra_settings_template');
						break;

					// Clone news
					// -------------------------------------    
					case "clone_news":


						if (Security::check(Request::get('token'))) {

							// Generate rand news name
							$rand_news_name = Request::get('name').'_clone_'.date("Ymd_His");

							// Get original news
							$orig_news = $news->select('[slug="'.Request::get('name').'"]', null);

							// Generate rand news title
							$rand_news_title = $orig_news['title'].' [copy]';

							// Clone news
							if ($news->insert(array('slug'         => $rand_news_name,
								'parent'       => $orig_news['parent'],
								'robots_index' => $orig_news['robots_index'],
								'robots_follow'=> $orig_news['robots_follow'],
								'status'       => $orig_news['status'],
								'access'       => (isset($orig_news['access'])) ? $orig_news['access'] : 'public',
								'expand'       => (isset($orig_news['expand'])) ? $orig_news['expand'] : '0',
								'title'        => $rand_news_title,
								'description'  => $orig_news['description'],
								'keywords'     => $orig_news['keywords'],
								'date'         => $orig_news['date'],
								'author'       => $orig_news['author']))) {

								// Get cloned news ID
								$last_id = $news->lastId();

								// Save cloned news content
								File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.news.txt',
									File::getContent(STORAGE . DS . 'news' . DS . $orig_news['id'] . '.news.txt'));

								// Save cloned news content
								File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.short.news.txt',
									File::getContent(STORAGE . DS . 'news' . DS . $orig_news['id'] . '.short.news.txt'));

								// Send notification
								Notification::set('success', __('The news <i>:news</i> cloned.', 'news', array(':news' => Security::safeName(Request::get('name'), '-', true))));
							}

							// Run add extra actions
							Action::run('admin_news_action_clone');

							// Redirect
							Request::redirect('index.php?id=news');

						} else { die('csrf detected!'); }

						break;

					// Add news
					// ------------------------------------- 
					case "add_news":

						// Add news                    
						if (Request::post('add_news') || Request::post('add_news_and_exit')) {

							if (Security::check(Request::post('csrf'))) {

								// Get parent news
								if (Request::post('parent') == '0') {
									$parent = '';
								} else {
									$parent = Request::post('parent');
								}

								// Prepare date
								if (Valid::date(Request::post('date'))) {
									$date = strtotime(Request::post('date'));
								} else {
									$date = time();
								}

								if (Request::post('robots_index'))  $robots_index = 'noindex';   else $robots_index = 'index';
								if (Request::post('robots_follow')) $robots_follow = 'nofollow'; else $robots_follow = 'follow';

								// If no errors then try to save
								if (count($errors) == 0) {

									// Insert new news
									if ($news->insert(array(
										'slug'         => Security::safeName(Request::post('name'), '-', true),
										'parent'       => $parent,
										'status'       => Request::post('status'),
										'access'       => Request::post('access'),
										'expand'       => '0',
										'robots_index' => $robots_index,
										'robots_follow'=> $robots_follow,
										'title'        => Request::post('title'),
										'description'  => Request::post('description'),
										'keywords'     => Request::post('keywords'),
										'date'         => $date,
										'author'       => $author,
										'hits'         => '0')
									)) {

										// Get inserted news ID                                                               
										$last_id = $news->lastId();

										// Save content
										File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.news.txt', XML::safe(Request::post('editor')));
										File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.short.news.txt', XML::safe(Request::post('short')));

										// Send notification
										Notification::set('success', __('Your news <i>:news</i> have been added.', 'news', array(':news' => Security::safeName(Request::post('title'), '-', true))));
									}

									// Run add extra actions
									Action::run('admin_news_action_add');

									// Redirect                           
									if (Request::post('add_news_and_exit')) {
										Request::redirect('index.php?id=news');
									} else {
										Request::redirect('index.php?id=news&action=edit_news&name='.Security::safeName(Request::post('name'), '-', true));
									}
								}

							} else { die('csrf detected!'); }

						}

						// Get all news
						$news_list = $news->select('[parent=""]');
						$news_array[] = '-none-';
						if (is_array($news_list))
						{
							foreach ($news_list as $item) {
								$news_array[$item['slug']] = $item['title'];
							}
						}

						// Save fields
						if (Request::post('name'))             $news_item['name']          = Request::post('name'); else $news_item['name'] = '';
						if (Request::post('title'))            $news_item['title']         = Request::post('title'); else $news_item['title'] = '';
						if (Request::post('keywords'))         $news_item['keywords']      = Request::post('keywords'); else $news_item['keywords'] = '';
						if (Request::post('description'))      $news_item['description']   = Request::post('description'); else $news_item['description'] = '';
						if (Request::post('editor'))           $news_item['content']       = Request::post('editor'); else $news_item['content'] = '';
						if (Request::post('short'))            $news_item['short']         = Request::post('short'); else $news_item['short'] = '';
						if (Request::post('status'))           $news_item['status']        = Request::post('status'); else $news_item['status'] = 'published';
						if (Request::post('access'))           $news_item['access']        = Request::post('access'); else $news_item['access'] = 'public';
						if (Request::post('parent'))           $news_item['parent']        = Request::post('parent'); else if(Request::get('parent')) $news_item['parent'] = Request::get('parent'); else $news_item['parent'] = '';
						if (Request::post('robots_index'))     $news_item['robots_index']  = true;
						else $news_item['robots_index'] = false;
						if (Request::post('robots_follow'))    $news_item['robots_follow'] = true;
						else $news_item['robots_follow'] = false;
						//--------------

						// Generate date
						$news_item['date'] = Date::format(time(), 'Y-m-d H:i:s');

						// Set Tabs State - news
						Notification::setNow('news', 'news');

						// Display view
						View::factory('news/views/backend/add')
							->assign('news', $news_item)
							->assign('news_array', $news_array)
							->assign('status_array', $status_array)
							->assign('access_array', $access_array)
							->assign('errors', $errors)
							->display();

						break;

					// Edit news
					// ------------------------------------- 
					case "edit_news":

						if (Request::post('edit_news') || Request::post('edit_news_and_exit')) {

							if (Security::check(Request::post('csrf'))) {

								// Get news parent
								if (Request::post('parent') == '0') {
									$parent = '';
								} else {
									$parent = Request::post('parent');
								}

								// Save fields
								if (Request::post('name'))             $news_item['name']          = Request::post('name'); else $news_item['name'] = '';
								if (Request::post('title'))            $news_item['title']         = Request::post('title'); else $news_item['title'] = '';
								if (Request::post('keywords'))         $news_item['keywords']      = Request::post('keywords'); else $news_item['keywords'] = '';
								if (Request::post('description'))      $news_item['description']   = Request::post('description'); else $news_item['description'] = '';
								if (Request::post('editor'))           $news_item['content']       = Request::post('editor'); else $news_item['content'] = '';
								if (Request::post('short'))            $news_item['short']         = Request::post('short'); else $news_item['short'] = '';
								if (Request::post('status'))           $news_item['status']        = Request::post('status'); else $news_item['status'] = 'published';
								if (Request::post('access'))           $news_item['access']        = Request::post('access'); else $news_item['access'] = 'public';
								if (Request::post('parent'))           $news_item['parent']        = Request::post('parent'); else if(Request::get('parent')) $news_item['parent'] = Request::get('parent'); else $news_item['parent'] = '';
								if (Request::post('robots_index'))     $news_item['robots_index']  = true;
								else $news_item['robots_index'] = false;
								if (Request::post('robots_follow'))    $news_item['robots_follow'] = true;
								else $news_item['robots_follow'] = false;
								//--------------

								// Prepare date
								if (Valid::date(Request::post('date'))) {
									$news_item['date'] = strtotime(Request::post('date'));
								} else {
									$news_item['date'] = time();
								}

								if (Request::post('robots_index'))  $robots_index = 'noindex';   else $robots_index = 'index';
								if (Request::post('robots_follow')) $robots_follow = 'nofollow'; else $robots_follow = 'follow';

								if (count($errors) == 0) {

									$data = array(
										'slug'         => Security::safeName(Request::post('name'), '-', true),
										'parent'       => $parent,
										'title'        => $news_item['title'],
										'description'  => $news_item['description'],
										'keywords'     => $news_item['keywords'],
										'robots_index' => $robots_index,
										'robots_follow'=> $robots_follow,
										'status'       => $news_item['status'],
										'access'       => $news_item['access'],
										'date'         => $news_item['date'],
										'author'       => $author
									);

									// Update parents in all childrens
									if ((Security::safeName(Request::post('name'), '-', true)) !== (Security::safeName(Request::post('old_name'), '-', true)) and (Request::post('old_parent') == '')) {

										$news->updateWhere('[parent="'.Request::get('name').'"]', array('parent' => Text::translitIt(trim(Request::post('name')))));

										if ($news->updateWhere('[slug="'.Request::get('name').'"]', $data)) {

											File::setContent(STORAGE . DS . 'news' . DS . Request::post('news_id') . '.news.txt', XML::safe(Request::post('editor')));
											File::setContent(STORAGE . DS . 'news' . DS . Request::post('news_id') . '.short.news.txt', XML::safe(Request::post('short')));
											Notification::set('success', __('Your changes to the news <i>:news</i> have been saved.', 'news', array(':news' => Security::safeName(Request::post('title'), '-', true))));
										}

										// Run edit extra actions
										Action::run('admin_news_action_edit');

									} else {

										if ($news->updateWhere('[slug="'.Request::get('name').'"]', $data)) {

											File::setContent(STORAGE . DS . 'news' . DS . Request::post('news_id') . '.news.txt', XML::safe(Request::post('editor')));
											File::setContent(STORAGE . DS . 'news' . DS . Request::post('news_id') . '.short.news.txt', XML::safe(Request::post('short')));
											Notification::set('success', __('Your changes to the news <i>:news</i> have been saved.', 'news', array(':news' => Security::safeName(Request::post('title'), '-', true))));
										}

										// Run edit extra actions
										Action::run('admin_news_action_edit');
									}

									// Redirect
									if (Request::post('edit_news_and_exit')) {
										Request::redirect('index.php?id=news');
									} else {
										Request::redirect('index.php?id=news&action=edit_news&name='.Security::safeName(Request::post('name'), '-', true));
									}
								}

							} else { die('csrf detected!'); }
						}


						// Get all news
						$news_list = $news->select();
						$news_array[] = '-none-';
						// Foreach news find news whithout parent
						foreach ($news_list as $item) {
							if (isset($item['parent'])) {
								$c_p = $item['parent'];
							} else {
								$c_p = '';
							}
							if ($c_p == '') {
								if ($item['slug'] !== Request::get('name')) {
									$news_array[$item['slug']] = $item['title'];
								}
							}
						}

						$item = $news->select('[slug="'.Request::get('name').'"]', null);

						if ($item) {

							$item['content'] = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $item['id'] . '.news.txt'));
							$item['short'] = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $item['id'] . '.short.news.txt'));

							if (Request::post('parent')) {
								// Get news parent
								if (Request::post('parent') == '-none-') {
									$news_item['parent'] = '';
								} else {
									$news_item['parent'] = Request::post('parent');
								}
								// Save field
								$news_item['parent'] = Request::post('parent');
							} else {
								$news_item['parent'] = $item['parent'];
							}

							// date
							$item['date'] = Date::format($item['date'], 'Y-m-d H:i:s');

                            if ((int)Request::get('upload') > 0)
                            {
                                Notification::setNow('upload', 'news');
                            }
                            else
                            {
                                Notification::setNow('news', 'news');
                            }
							// Display view
							View::factory('news/views/backend/edit')
								->assign('news', $item)
								->assign('news_array', $news_array)
								->assign('status_array', $status_array)
								->assign('access_array', $access_array)
                                ->assign('dir', $dir_img)
                                ->assign('url', $url_img)
								->assign('errors', $errors)
								->display();
						}

						break;

					// Delete news
					// ------------------------------------- 
					case "delete_news":

						// Error 404 news can not be removed                                               
						if (Request::get('name') !== 'error404') {

							if (Security::check(Request::get('token'))) {

								// Get specific news
								$item = $news->select('[slug="'.Request::get('name').'"]', null);

								//  Delete news and update <parent> fields
								if ($news->deleteWhere('[slug="'.$item['slug'].'" ]')) {

									$_news = $news->select('[parent="'.$item['slug'].'"]');

									if ( ! empty($_news)) {
										foreach($_news as $_news) {
											$news->updateWhere('[slug="'.$_news['slug'].'"]', array('parent' => ''));
										}
									}

									File::delete(STORAGE . DS . 'news' . DS . $item['id'] . '.news.txt');
									File::delete(STORAGE . DS . 'news' . DS . $item['id'] . '.short.news.txt');
									Notification::set('success', __('News <i>:news</i> deleted', 'news', array(':news' => Html::toText($item['title']))));
								}

								// Run delete extra actions
								Action::run('admin_news_action_delete');

								// Redirect
								Request::redirect('index.php?id=news');

							} else { die('csrf detected!'); }
						}

						break;
				}

				// Its mean that you can add your own actions for this plugin
				Action::run('admin_news_extra_actions');

			} else {

				// Index action
				// ------------------------------------- 

				// Init vars
				$news_array = array();
				$count = 0;

				// Get news
				$news_list = $news->select(null, 'all', null, array('slug', 'title', 'status', 'date', 'author', 'expand', 'access', 'parent'));

				// Loop
				foreach ($news_list as $item) {

					$news_array[$count]['title']   = $item['title'];
					$news_array[$count]['parent']  = $item['parent'];
					$news_array[$count]['status']  = $status_array[$item['status']];
					$news_array[$count]['access']  = isset($access_array[$item['access']]) ? $access_array[$item['access']] : $access_array['public']; // hack for old Monstra Versions
					$news_array[$count]['date']    = $item['date'];
					$news_array[$count]['author']  = $item['author'];
					$news_array[$count]['expand']  = $item['expand'];
					$news_array[$count]['slug']    = $item['slug'];

					if (isset($item['parent'])) {
						$c_p = $item['parent'];
					} else {
						$c_p = '';
					}

					if ($c_p != '') {

						$_news = $news->select('[slug="'.$item['parent'].'"]', null);

						if (isset($_news['title'])) {
							$_title = $_news['title'];
						} else {
							$_title = '';
						}

						$news_array[$count]['sort'] = $_title . ' ' . $item['title'];

					} else {

						$news_array[$count]['sort'] = $item['title'];

					}

					$_title = '';
					$count++;
				}

				// Sort news
				$news = Arr::subvalSort($news_array, 'sort');

				// Display view
				View::factory('news/views/backend/index')
					->assign('news', $news)
					->assign('site_url', $site_url)
					->display();
			}

		}

        
        /**
         * Form Component Save
         */
        public static function formComponentSave() {
            if (Request::post('sandbox_component_save')) {
                if (Security::check(Request::post('csrf'))) {
                    Option::update('sandbox_template', Request::post('sandbox_form_template'));
                    Request::redirect('index.php?id=themes');
                }
            }
        }


        /**
         * Form Component
         */
        public static function formComponent() {

            $_templates = Themes::getTemplates();
            foreach($_templates as $template) {
                $templates[basename($template, '.template.php')] = basename($template, '.template.php');
            }

            echo (
                Form::open().
                    Form::hidden('csrf', Security::token()).
                    Form::label('sandbox_form_template', __('Sandbox template', 'sandbox')).
                    Form::select('sandbox_form_template', $templates, Option::get('sandbox_template')).
                    Html::br().
                    Form::submit('sandbox_component_save', __('Save', 'sandbox'), array('class' => 'btn')).
                    Form::close()
            );
        }
	}