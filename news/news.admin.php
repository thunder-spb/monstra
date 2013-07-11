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
        $templates_path = THEMES_SITE;
        $opt['site_url'] = Option::get('siteurl');

        // Get all templates
        $templates_list = File::scan($templates_path, '.template.php');
        foreach ($templates_list as $file) {
            $opt['templates'][basename($file, '.template.php')] = basename($file, '.template.php');
        }
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
        $opt['status'] = array('published' => __('Published', 'news'), 'draft' => __('Draft', 'news'));

        // Access array
        $opt['access'] = array('public'   => __('Public', 'news'), 'registered'  => __('Registered', 'news'));

        $opt['url'] = $opt['site_url'] . 'public/uploads/news/';
        $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'news' . DS;


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
                        $rand_news_name = Request::get('uid').'_clone_'.date("Ymd_His");

                        // Get original news
                        $orig_news = $news->select('[id="'.Request::get('uid').'"]', null);

                        // Generate rand news title
                        $rand_news_title = $orig_news['title'].' [copy]';

                        // Clone news
                        if ($news->insert(array(
                            'slug'         => $rand_news_name,
                            'parent'       => $orig_news['parent'],
                            'robots_index' => $orig_news['robots_index'],
                            'robots_follow'=> $orig_news['robots_follow'],
                            'status'       => $orig_news['status'],
                            'template'     => $orig_news['template'],
                            'access'       => (isset($orig_news['access'])) ? $orig_news['access'] : 'public',
                            'expand'       => (isset($orig_news['expand'])) ? $orig_news['expand'] : '0',
                            'title'        => $rand_news_title,
                            'description'  => $orig_news['description'],
                            'keywords'     => $orig_news['keywords'],
                            'tags'         => $orig_news['tags'],
                            'date'         => time(),
                            'author'       => $author
                        ))) {

                            // Get cloned news ID
                            $last_id = $news->lastId();

                            // Save cloned news content
                            File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.news.txt',
                            File::getContent(STORAGE . DS . 'news' . DS . $orig_news['id'] . '.news.txt'));

                            // Save cloned news content
                            File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.short.news.txt',
                            File::getContent(STORAGE . DS . 'news' . DS . $orig_news['id'] . '.short.news.txt'));

                            // Send notification
                            Notification::set('success', __('The news <i>:news</i> cloned.', 'news', array(':news' => Security::safeName(Request::get('slug'), '-', true))));
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
                            if (Request::post('news_parent') == '0') {
                                $parent = '';
                            } else {
                                $parent = Request::post('news_parent');
                            }

                            // Prepare date
                            if (Valid::date(Request::post('news_date'))) {
                                $date = strtotime(Request::post('news_date'));
                            } else {
                                $date = time();
                            }

                            if (Request::post('news_robots_index'))  $robots_index = 'noindex';   else $robots_index = 'index';
                            if (Request::post('news_robots_follow')) $robots_follow = 'nofollow'; else $robots_follow = 'follow';
                            $slug = (Request::post('news_slug') == "") ? Request::post('news_title') : Request::post('news_slug');

                            // If no errors then try to save
                            if (count($errors) == 0) {

                                $last_id =  0;
                                // Insert new news
                                if ($news->insert(array(
                                        'slug'         => Security::safeName($slug, '-', true),
                                        'parent'       => $parent,
                                        'status'       => Request::post('news_status'),
                                        'template'     => Request::post('news_template'),
                                        'access'       => Request::post('news_access'),
                                        'expand'       => '0',
                                        'robots_index' => $robots_index,
                                        'robots_follow'=> $robots_follow,
                                        'title'        => Request::post('news_title'),
                                        'description'  => Request::post('news_description'),
                                        'tags'         => Request::post('news_tags'),
                                        'keywords'     => Request::post('news_keywords'),
                                        'date'         => $date,
                                        'author'       => $author,
                                        'hits'         => '0')
                                )) {

                                    // Get inserted news ID
                                    $last_id = $news->lastId();

                                    // Save content
                                    File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.news.txt', XML::safe(Request::post('editor')));
                                    File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.short.news.txt', XML::safe(Request::post('news_short')));
                                    NewsAdmin::UploadImage($last_id, $_FILES);

                                    // Send notification
                                    Notification::set('success', __('Your news <i>:news</i> have been added.', 'news', array(':news' => Security::safeName(Request::post('news_title'), '-', true))));
                                }

                                // Run add extra actions
                                Action::run('admin_news_action_add');

                                // Redirect
                                if (Request::post('add_news_and_exit')) {
                                    Request::redirect('index.php?id=news');
                                } else {
                                    Request::redirect('index.php?id=news&action=edit_news&uid='.$last_id);
                                }
                            }

                        } else { die('csrf detected!'); }

                    }

                    // Get all news
                    //$news_list = $news->select('[parent=""]');
                    $opt['list'][] = '-none-';
                    /*if (is_array($news_list))
                    {
                        foreach ($news_list as $item) {
                            $opt['list'][$item['slug']] = $item['title'];
                        }
                    }*/

                    // Save fields
                    if (Request::post('slug'))             $news_item['slug']          = Request::post('slug');         else $news_item['slug'] = '';
                    if (Request::post('title'))            $news_item['title']         = Request::post('title');        else $news_item['title'] = '';
                    if (Request::post('keywords'))         $news_item['keywords']      = Request::post('keywords');     else $news_item['keywords'] = '';
                    if (Request::post('tags'))             $news_item['tags']          = Request::post('tags');         else $news_item['tags'] = '';
                    if (Request::post('description'))      $news_item['description']   = Request::post('description');  else $news_item['description'] = '';
                    if (Request::post('editor'))           $news_item['content']       = Request::post('editor');       else $news_item['content'] = '';
                    if (Request::post('templates'))        $news_item['template']      = Request::post('templates');    else $news_item['template'] = 'index';
                    if (Request::post('short'))            $news_item['short']         = Request::post('short');        else $news_item['short'] = '';
                    if (Request::post('status'))           $news_item['status']        = Request::post('status');       else $news_item['status'] = 'published';
                    if (Request::post('access'))           $news_item['access']        = Request::post('access');       else $news_item['access'] = 'public';
                    if (Request::post('parent'))           $news_item['parent']        = Request::post('parent');       else if(Request::get('parent')) $news_item['parent'] = Request::get('parent'); else $news_item['parent'] = '';
                    if (Request::post('robots_index'))     $news_item['robots_index']  = true;                          else $news_item['robots_index'] = false;
                    if (Request::post('robots_follow'))    $news_item['robots_follow'] = true;                          else $news_item['robots_follow'] = false;
                    //--------------

                    // Generate date
                    $news_item['date'] = Date::format(time(), 'Y-m-d H:i:s');

                    // Set Tabs State - news
                    Notification::setNow('news', 'news');

                    // Display view
                    View::factory('news/views/backend/add')
                        ->assign('item', $news_item)
                        ->assign('opt', $opt)
                        ->assign('errors', $errors)
                        ->display();

                    break;

                // Edit news
                // -------------------------------------
                case "edit_news":

                    if (Request::post('edit_news') || Request::post('edit_news_and_exit')) {

                        if (Security::check(Request::post('csrf'))) {

                            // Get news parent
                            if (Request::post('news_parent') == '0') {
                                $parent = '';
                            } else {
                                $parent = Request::post('news_parent');
                            }

                            $id = (int)Request::post('news_id');

                            // Prepare date
                            if (Valid::date(Request::post('news_date'))) {
                                $date = strtotime(Request::post('news_date'));
                            } else {
                                $date = time();
                            }

                            if (Request::post('robots_index'))  $robots_index = 'noindex';   else $robots_index = 'index';
                            if (Request::post('robots_follow')) $robots_follow = 'nofollow'; else $robots_follow = 'follow';
                            $slug = (Request::post('news_slug') == "") ? Request::post('news_title') : Request::post('news_slug');

                            if (count($errors) == 0) {

                                $data = array(
                                    'slug'         => Security::safeName($slug, '-', true),
                                    'parent'       => $parent,
                                    'title'        => Request::post('news_title'),
                                    'description'  => Request::post('news_description'),
                                    'tags'         => Request::post('news_tags'),
                                    'keywords'     => Request::post('news_keywords'),
                                    'robots_index' => $robots_index,
                                    'robots_follow'=> $robots_follow,
                                    'status'       => Request::post('news_status'),
                                    'template'     => Request::post('news_template'),
                                    'access'       => Request::post('news_access'),
                                    'date'         => $date,
                                    'author'       => $author
                                );

                                // Update parents in all childrens
                                if ((Security::safeName(Request::post('slug'), '-', true)) !== (Security::safeName(Request::post('old_name'), '-', true)) and (Request::post('old_parent') == '')) {

                                    //$news->updateWhere('[parent="'.Request::get('slug').'"]', array('parent' => Text::translitIt(trim(Request::post('slug')))));

                                    if ($news->updateWhere('[id="'.$id.'"]', $data)) {
                                        NewsAdmin::UploadImage($id, $_FILES);
                                        File::setContent(STORAGE . DS . 'news' . DS . $id . '.news.txt', XML::safe(Request::post('editor')));
                                        File::setContent(STORAGE . DS . 'news' . DS . $id . '.short.news.txt', XML::safe(Request::post('news_short')));
                                        Notification::set('success', __('Your changes to the news <i>:news</i> have been saved.', 'news', array(':news' => Security::safeName(Request::post('news_title'), '-', true))));
                                    }

                                    // Run edit extra actions
                                    Action::run('admin_news_action_edit');

                                } else {

                                    if ($news->updateWhere('[id="'.$id.'"]', $data)) {
                                        NewsAdmin::UploadImage($id, $_FILES);
                                        File::setContent(STORAGE . DS . 'news' . DS . $id . '.news.txt', XML::safe(Request::post('editor')));
                                        File::setContent(STORAGE . DS . 'news' . DS . $id . '.short.news.txt', XML::safe(Request::post('news_short')));
                                        Notification::set('success', __('Your changes to the news <i>:news</i> have been saved.', 'news', array(':news' => Security::safeName(Request::post('news_title'), '-', true))));
                                    }

                                    // Run edit extra actions
                                    Action::run('admin_news_action_edit');
                                }

                                // Redirect
                                if (Request::post('edit_news_and_exit')) {
                                    Request::redirect('index.php?id=news');
                                } else {
                                    Request::redirect('index.php?id=news&action=edit_news&uid='.$id);
                                }
                            }

                        } else { die('csrf detected!'); }
                    }


                    // Get all news
                    //$news_list = $news->select();
                    $opt['list'][] = '-none-';
                    // Foreach news find news whithout parent
                    /*foreach ($news_list as $item) {
                        if (isset($item['parent'])) {
                            $c_p = $item['parent'];
                        } else {
                            $c_p = '';
                        }
                        if ($c_p == '') {
                            if ($item['slug'] !== Request::get('slug')) {
                                $opt['list'][$item['slug']] = $item['title'];
                            }
                        }
                    }*/

                    $item = $news->select('[id="'.Request::get('uid').'"]', null);

                    if ($item) {

                        $item['content'] = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $item['id'] . '.news.txt'));
                        $item['short'] = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $item['id'] . '.short.news.txt'));

                        if (Request::post('parent')) {
                            // Get news parent
                            if (Request::post('parent') == '-none-') {
                                $item['parent'] = '';
                            } else {
                                $item['parent'] = Request::post('parent');
                            }
                        }

                        // date
                        $item['date'] = Date::format($item['date'], 'Y-m-d H:i:s');
                        // Set Tabs State - news
                        Notification::setNow('news', 'news');
                        // Display view
                        View::factory('news/views/backend/edit')
                            ->assign('item', $item)
                            ->assign('opt', $opt)
                            ->assign('errors', $errors)
                            ->display();
                    }

                    break;

                // Delete news
                // -------------------------------------
                case "delete_news":

                    // Error 404 news can not be removed
                    if (Request::get('slug') !== 'error404') {

                        if (Security::check(Request::get('token'))) {

                            // Get specific news
                            $item = $news->select('[id="'.Request::get('uid').'"]', null);

                            //  Delete news and update <parent> fields
                            if ($news->deleteWhere('[slug="'.$item['slug'].'" ]')) {

                                $_news = $news->select('[parent="'.$item['slug'].'"]');

                                if ( ! empty($_news)) {
                                    foreach($_news as $news_item) {
                                        $news->updateWhere('[slug="'.$news_item['slug'].'"]', array('parent' => ''));
                                    }
                                }

                                File::delete($opt['dir'] . $item['id'] . '.jpg');
                                File::delete($opt['dir']. 'thumbnail' . DS . $item['id'] . '.jpg');
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
            $count = 0;
            $items = array();
            $limit = Option::get('news_limit_admin');
            $records_all = $news->select(null, 'all', null, array('slug', 'title', 'status', 'date', 'author', 'expand', 'access', 'parent'));
            $count_catalog = count($records_all);
            $opt['pages'] = ceil($count_catalog/$limit);

            $opt['page'] = (Request::get('page')) ? (int)Request::get('page') : 1;
//			$sort = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
//			$order = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';

            if ($opt['page'] < 1) { $opt['page'] = 1; }
            elseif ($opt['page'] > $opt['pages']) { $opt['page'] = $opt['pages']; }

            $start = ($opt['page']-1)*$limit;

//			$records_sort = Arr::subvalSort($records_all, $sort, $order);
            $records_sort = $records_all;
            if($count_catalog>0) $records = array_slice($records_sort, $start, $limit);
            else $records = array();

            // Loop
            foreach ($records as $item) {

                $items[$count]['title']   = $item['title'];
                $items[$count]['parent']  = $item['parent'];
                $items[$count]['status']  = $opt['status'][$item['status']];
                $items[$count]['access']  = isset($opt['access'][$item['access']]) ? $opt['access'][$item['access']] : $opt['access']['public']; // hack for old Monstra Versions
                $items[$count]['date']    = $item['date'];
                $items[$count]['author']  = $item['author'];
                $items[$count]['expand']  = $item['expand'];
                $items[$count]['slug']    = $item['slug'];
                $items[$count]['id']      = $item['id'];

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

                    $items[$count]['sort'] = $_title . ' ' . $item['title'];

                } else {

                    $items[$count]['sort'] = $item['title'];

                }

                $_title = '';
                $count++;
            }

            // Display view
            View::factory('news/views/backend/index')
                ->assign('items', $items)
                ->assign('opt', $opt)
                ->display();
        }

    }


    /**
     * Form Component Save
     */
    public static function formComponentSave() {
        if (Request::post('news_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('news_template', Request::post('news_form_template'));
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
                Form::label('news_form_template', __('News template', 'news')).
                Form::select('news_form_template', $templates, Option::get('news_template')).
                Html::br().
                Form::submit('news_component_save', __('Save', 'news'), array('class' => 'btn')).
                Form::close()
        );
    }

    private static function UploadImage($uid, $_FILES)
    {
        $dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'news' . DS;

        if ($_FILES['news_file']) {
            if($_FILES['news_file']['type'] == 'image/jpeg' ||
                $_FILES['news_file']['type'] == 'image/png' ||
                $_FILES['news_file']['type'] == 'image/gif') {

                $img  = Image::factory($_FILES['news_file']['tmp_name']);
                $file['wmax']   = (int)Option::get('news_wmax');
                $file['hmax']   = (int)Option::get('news_hmax');
                $file['w']      = (int)Option::get('news_w');
                $file['h']      = (int)Option::get('news_h');
                $file['resize'] = Option::get('news_resize');
                DevAdmin::ReSize($img, $dir, $uid.'.jpg', $file);
            }
        }
    }
}