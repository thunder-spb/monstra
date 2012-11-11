<?php 

    // Admin Navigation: add new item
    Navigation::add(__('Price', 'price'), 'content', 'price', 10);

	/**
	 * Price admin class
	 */
	class PriceAdmin extends Backend {

		/**
		 * Main function
		 */
		public static function main() {

			// Array of forbidden types
			$allowed_types = array('csv');

			// Get Site url
			$site_url = Option::get('siteurl');

			// Init vars
			if (Request::get('path')) $path = Request::get('path'); else $path = 'prices/';

			// Add slash if not exists
			if (substr($path, -1, 1) != '/') {
				$path .= '/';
				Request::redirect($site_url.'admin/index.php?id=price&path='.$path);
			}

			// Upload corectly!
			if ($path == 'prices' || $path == 'prices//') {
				$path = 'prices/';
				Request::redirect($site_url.'admin/index.php?id=price&path='.$path);
			}

			// Only 'prices' folder!
			if (strpos($path, 'prices') === false) {
				$path = 'prices/';
				Request::redirect($site_url.'admin/index.php?id=price&path='.$path);
			}

			// Set default path value if path is empty
			if ($path == '') {
				$path = 'prices/';
				Request::redirect($site_url.'admin/index.php?id=price&path='.$path);
			}

			$files_path = ROOT . DS . 'public' . DS . $path;

			$current = explode('/', $path);

			// Get information about current path
			$_list = PriceAdmin::fdir($files_path);

			$files_list = array();

			// Get files
			if (isset($_list['files'])) {
				foreach ($_list['files'] as $files) {
					$files_list[] = $files;
				}
			}

			// Delete file
			// -------------------------------------
			if (Request::get('id') == 'price' && Request::get('delete_file')) {

				if (Security::check(Request::get('token'))) {

					File::delete($files_path.Request::get('delete_file'));
					Notification::set('success', __('Delete File', 'price'));
					Request::redirect($site_url.'admin/index.php?id=price&path='.$path);

				} else { die('csrf detected!'); }
			}

			// Upload file
			// -------------------------------------
			if (Request::post('upload_file')) {

				if (Security::check(Request::post('csrf'))) {
					if ($_FILES['file']) {
						if (in_array(File::ext($_FILES['file']['name']), $allowed_types)) {
							if (Request::post('name')){
								$name = Request::post('name');
								Notification::set('success', __('Success ReLoad', 'price'));
							}
							else{
								$name = Text::random('kanekt', 10).'.'.File::ext($_FILES['file']['name']);
								Notification::set('success', __('Success UpLoad', 'price'));
							}
							move_uploaded_file($_FILES['file']['tmp_name'], $files_path.$name);
							Request::redirect($site_url.'admin/index.php?id=price&path='.$path);
						}
						else{
							Notification::set('error', __('Error', 'price'));
						}
					}
					else{
						Notification::set('error', __('Error', 'price'));
					}
				} else { die('csrf detected!'); }
			}
			// Edit file
			// -------------------------------------
			if (Request::get('id') == 'price' && Request::get('action') == 'edit' && Request::get('uid')) {
				// Display view
				View::factory('price/views/backend/file')
					->assign('name', Request::get('uid'))
					->display();
			}
			else{
				// Display view
				View::factory('price/views/backend/index')
					->assign('path', $path)
					->assign('current', $current)
					->assign('files_list', $files_list)
					->assign('allowed_types', $allowed_types)
					->assign('site_url', $site_url)
					->assign('files_path', $files_path)
					->display();
			}
		}


		/**
		 * Get directories and files in current path
		 */
		protected static function fdir($dir, $type = null) {
			$files = array();
			$c = 0;
			$_dir = $dir;
			if (is_dir($dir)) {
				$dir = opendir ($dir);
				while (false !== ($file = readdir($dir))) {
					if (($file !=".") && ($file !="..")) {
						$c++;
						if (is_dir($_dir.$file)) {
							$files['dirs'][$c] = $file;
						} else {
							$files['files'][$c] = $file;
						}
					}
				}
				closedir($dir);
				return $files;
			} else {
				return false;
			}
		}

	}