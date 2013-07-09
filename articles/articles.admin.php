<?php
Navigation::add(__('Articles', 'articles'), 'content', 'articles', 10);

Action::add('admin_themes_extra_index_template_actions','ArticlesAdmin::formComponent');
Action::add('admin_themes_extra_actions','ArticlesAdmin::formComponentSave');

class ArticlesAdmin extends Backend {

    /**
     * Articles tables
     *
     * @var object
     */
    public static $articles = null;

    /**
     * Articles admin function
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

        $articles = new Table('articles');
        ArticlesAdmin::$articles = $articles;

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
        $opt['status'] = array('published' => __('Published', 'articles'), 'draft' => __('Draft', 'articles'));

        // Access array
        $opt['access'] = array('public'   => __('Public', 'articles'), 'registered'  => __('Registered', 'articles'));

        $opt['url'] = $opt['site_url'] . 'public/uploads/articles/';
        $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'articles' . DS;


        // Check for get actions
        // ---------------------------------------------
        if (Request::get('action')) {

            // Switch actions
            // -----------------------------------------
            switch (Request::get('action')) {

                // Settings
                // -------------------------------------
                case "settings":

                    if (Request::post('article_submit_settings_cancel')) {
                        Request::redirect('index.php?id=articles');
                    }

                    if (Request::post('article_submit_settings')) {
                        if (Security::check(Request::post('csrf'))) {
                            Option::update(array(
                                'article_limit'        => (int)Request::post('limit'),
                                'article_limit_admin'  => (int)Request::post('limit_admin'),
                                'article_w'            => (int)Request::post('width_thumb'),
                                'article_h'            => (int)Request::post('height_thumb'),
                                'article_wmax'         => (int)Request::post('width_orig'),
                                'article_hmax'         => (int)Request::post('height_orig'),
                                'article_resize'       => (string)Request::post('resize')
                            ));

                            Notification::set('success', __('Your changes have been saved', 'articles'));

                            Request::redirect('index.php?id=articles');
                        } else { die('csrf detected!'); }
                    }

                    View::factory('articles/views/backend/settings')->display();
                    Action::run('admin_articles_extra_settings_template');
                    break;

                // Clone articles
                // -------------------------------------
                case "clone_articles":

                    if (Security::check(Request::get('token'))) {

                        // Generate rand articles name
                        $rand_articles_name = Request::get('uid').'_clone_'.date("Ymd_His");

                        // Get original articles
                        $orig_article = $articles->select('[id="'.Request::get('uid').'"]', null);

                        // Generate rand articles title
                        $rand_articles_title = $orig_article['title'].' [copy]';

                        // Clone articles
                        if ($articles->insert(array(
                            'slug'         => $rand_articles_name,
                            'parent'       => $orig_article['parent'],
                            'robots_index' => $orig_article['robots_index'],
                            'robots_follow'=> $orig_article['robots_follow'],
                            'status'       => $orig_article['status'],
                            'template'     => $orig_article['template'],
                            'access'       => (isset($orig_article['access'])) ? $orig_article['access'] : 'public',
                            'expand'       => (isset($orig_article['expand'])) ? $orig_article['expand'] : '0',
                            'title'        => $rand_articles_title,
                            'description'  => $orig_article['description'],
                            'keywords'     => $orig_article['keywords'],
                            'tags'         => $orig_article['tags'],
                            'date'         => time(),
                            'author'       => $author
                        ))) {

                            // Get cloned articles ID
                            $last_id = $articles->lastId();

                            // Save cloned articles content
                            File::setContent(STORAGE . DS . 'articles' . DS . $last_id . '.articles.txt',
                            File::getContent(STORAGE . DS . 'articles' . DS . $orig_article['id'] . '.articles.txt'));

                            // Save cloned articles content
                            File::setContent(STORAGE . DS . 'articles' . DS . $last_id . '.short.article.txt',
                            File::getContent(STORAGE . DS . 'articles' . DS . $orig_article['id'] . '.short.article.txt'));

                            // Send notification
                            Notification::set('success', __('The article <i>:article</i> cloned.', 'articles', array(':article' => Security::safeName(Request::get('slug'), '-', true))));
                        }

                        // Run add extra actions
                        Action::run('admin_articles_action_clone');

                        // Redirect
                        Request::redirect('index.php?id=articles');

                    } else { die('csrf detected!'); }

                    break;

                // Add articles
                // -------------------------------------
                case "add_articles":

                    // Add articles
                    if (Request::post('add_articles') || Request::post('add_articles_and_exit')) {

                        if (Security::check(Request::post('csrf'))) {

                            // Get parent articles
                            if (Request::post('article_parent') == '0') {
                                $parent = '';
                            } else {
                                $parent = Request::post('article_parent');
                            }

                            // Prepare date
                            if (Valid::date(Request::post('article_date'))) {
                                $date = strtotime(Request::post('article_date'));
                            } else {
                                $date = time();
                            }

                            if (Request::post('article_robots_index'))  $robots_index = 'noindex';   else $robots_index = 'index';
                            if (Request::post('article_robots_follow')) $robots_follow = 'nofollow'; else $robots_follow = 'follow';
                            $slug = (Request::post('article_slug') == "") ? Request::post('article_title') : Request::post('article_slug');

                            // If no errors then try to save
                            if (count($errors) == 0) {

                                $last_id =  0;
                                // Insert new articles
                                if ($articles->insert(array(
                                        'slug'         => Security::safeName($slug, '-', true),
                                        'parent'       => $parent,
                                        'status'       => Request::post('article_status'),
                                        'template'     => Request::post('article_template'),
                                        'access'       => Request::post('article_access'),
                                        'expand'       => '0',
                                        'robots_index' => $robots_index,
                                        'robots_follow'=> $robots_follow,
                                        'title'        => Request::post('article_title'),
                                        'description'  => Request::post('article_description'),
                                        'tags'         => Request::post('article_tags'),
                                        'keywords'     => Request::post('article_keywords'),
                                        'date'         => $date,
                                        'author'       => $author,
                                        'hits'         => '0')
                                )) {

                                    // Get inserted articles ID
                                    $last_id = $articles->lastId();

                                    // Save content
                                    File::setContent(STORAGE . DS . 'articles' . DS . $last_id . '.articles.txt', XML::safe(Request::post('editor')));
                                    File::setContent(STORAGE . DS . 'articles' . DS . $last_id . '.short.article.txt', XML::safe(Request::post('article_short')));
                                    ArticlesAdmin::UploadImage($last_id, $_FILES);

                                    // Send notification
                                    Notification::set('success', __('Your article <i>:article</i> have been added.', 'articles', array(':article' => Security::safeName(Request::post('article_title'), '-', true))));
                                }

                                // Run add extra actions
                                Action::run('admin_articles_action_add');

                                // Redirect
                                if (Request::post('add_articles_and_exit')) {
                                    Request::redirect('index.php?id=articles');
                                } else {
                                    Request::redirect('index.php?id=articles&action=edit_articles&uid='.$last_id);
                                }
                            }

                        } else { die('csrf detected!'); }

                    }

                    // Get all articles
                    //$article_list = $articles->select('[parent=""]');
                    $opt['list'][] = '-none-';
                    /*if (is_array($article_list))
                    {
                        foreach ($article_list as $item) {
                            $opt['list'][$item['slug']] = $item['title'];
                        }
                    }*/

                    // Save fields
                    if (Request::post('slug'))             $article_item['slug']          = Request::post('slug');         else $article_item['slug'] = '';
                    if (Request::post('title'))            $article_item['title']         = Request::post('title');        else $article_item['title'] = '';
                    if (Request::post('keywords'))         $article_item['keywords']      = Request::post('keywords');     else $article_item['keywords'] = '';
                    if (Request::post('tags'))             $article_item['tags']          = Request::post('tags');         else $article_item['tags'] = '';
                    if (Request::post('description'))      $article_item['description']   = Request::post('description');  else $article_item['description'] = '';
                    if (Request::post('editor'))           $article_item['content']       = Request::post('editor');       else $article_item['content'] = '';
                    if (Request::post('templates'))        $article_item['template']      = Request::post('templates');    else $article_item['template'] = 'index';
                    if (Request::post('short'))            $article_item['short']         = Request::post('short');        else $article_item['short'] = '';
                    if (Request::post('status'))           $article_item['status']        = Request::post('status');       else $article_item['status'] = 'published';
                    if (Request::post('access'))           $article_item['access']        = Request::post('access');       else $article_item['access'] = 'public';
                    if (Request::post('parent'))           $article_item['parent']        = Request::post('parent');       else if(Request::get('parent')) $article_item['parent'] = Request::get('parent'); else $article_item['parent'] = '';
                    if (Request::post('robots_index'))     $article_item['robots_index']  = true;                          else $article_item['robots_index'] = false;
                    if (Request::post('robots_follow'))    $article_item['robots_follow'] = true;                          else $article_item['robots_follow'] = false;
                    //--------------

                    // Generate date
                    $article_item['date'] = Date::format(time(), 'Y-m-d H:i:s');

                    // Set Tabs State - articles
                    Notification::setNow('articles', 'articles');

                    // Display view
                    View::factory('articles/views/backend/add')
                        ->assign('item', $article_item)
                        ->assign('opt', $opt)
                        ->assign('errors', $errors)
                        ->display();

                    break;

                // Edit article
                // -------------------------------------
                case "edit_articles":

                    if (Request::post('edit_articles') || Request::post('edit_articles_and_exit')) {

                        if (Security::check(Request::post('csrf'))) {

                            // Get articles parent
                            if (Request::post('article_parent') == '0') {
                                $parent = '';
                            } else {
                                $parent = Request::post('article_parent');
                            }

                            $id = (int)Request::post('article_id');

                            // Prepare date
                            if (Valid::date(Request::post('date'))) {
                                $date = strtotime(Request::post('date'));
                            } else {
                                $date = time();
                            }

                            if (Request::post('robots_index'))  $robots_index = 'noindex';   else $robots_index = 'index';
                            if (Request::post('robots_follow')) $robots_follow = 'nofollow'; else $robots_follow = 'follow';
                            $slug = (Request::post('article_slug') == "") ? Request::post('article_title') : Request::post('article_slug');

                            if (count($errors) == 0) {

                                $data = array(
                                    'slug'         => Security::safeName($slug, '-', true),
                                    'parent'       => $parent,
                                    'title'        => Request::post('article_title'),
                                    'description'  => Request::post('article_description'),
                                    'tags'         => Request::post('article_tags'),
                                    'keywords'     => Request::post('article_keywords'),
                                    'robots_index' => $robots_index,
                                    'robots_follow'=> $robots_follow,
                                    'status'       => Request::post('article_status'),
                                    'template'     => Request::post('article_template'),
                                    'access'       => Request::post('article_access'),
                                    'date'         => $date,
                                    'author'       => $author
                                );

                                // Update parents in all childrens
                                if ((Security::safeName(Request::post('slug'), '-', true)) !== (Security::safeName(Request::post('old_name'), '-', true)) and (Request::post('old_parent') == '')) {

                                    //$articles->updateWhere('[parent="'.Request::get('slug').'"]', array('parent' => Text::translitIt(trim(Request::post('slug')))));

                                    if ($articles->updateWhere('[id="'.$id.'"]', $data)) {
                                        ArticlesAdmin::UploadImage($id, $_FILES);
                                        File::setContent(STORAGE . DS . 'articles' . DS . $id . '.articles.txt', XML::safe(Request::post('editor')));
                                        File::setContent(STORAGE . DS . 'articles' . DS . $id . '.short.article.txt', XML::safe(Request::post('article_short')));
                                        Notification::set('success', __('Your changes to the articles <i>:article</i> have been saved.', 'articles', array(':article' => Security::safeName(Request::post('article_title'), '-', true))));
                                    }

                                    // Run edit extra actions
                                    Action::run('admin_articles_action_edit');

                                } else {

                                    if ($articles->updateWhere('[id="'.$id.'"]', $data)) {
                                        ArticlesAdmin::UploadImage($id, $_FILES);
                                        File::setContent(STORAGE . DS . 'articles' . DS . $id . '.articles.txt', XML::safe(Request::post('editor')));
                                        File::setContent(STORAGE . DS . 'articles' . DS . $id . '.short.article.txt', XML::safe(Request::post('article_short')));
                                        Notification::set('success', __('Your changes to the articles <i>:article</i> have been saved.', 'articles', array(':article' => Security::safeName(Request::post('article_title'), '-', true))));
                                    }

                                    // Run edit extra actions
                                    Action::run('admin_articles_action_edit');
                                }

                                // Redirect
                                if (Request::post('edit_articles_and_exit')) {
                                    Request::redirect('index.php?id=articles');
                                } else {
                                    Request::redirect('index.php?id=articles&action=edit_articles&uid='.$id);
                                }
                            }

                        } else { die('csrf detected!'); }
                    }


                    // Get all articles
                    //$article_list = $articles->select();
                    $opt['list'][] = '-none-';
                    // Foreach articles find articles whithout parent
                    /*foreach ($article_list as $item) {
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

                    $item = $articles->select('[id="'.Request::get('uid').'"]', null);

                    if ($item) {

                        $item['content'] = Text::toHtml(File::getContent(STORAGE . DS . 'articles' . DS . $item['id'] . '.articles.txt'));
                        $item['short'] = Text::toHtml(File::getContent(STORAGE . DS . 'articles' . DS . $item['id'] . '.short.article.txt'));

                        if (Request::post('parent')) {
                            // Get articles parent
                            if (Request::post('parent') == '-none-') {
                                $item['parent'] = '';
                            } else {
                                $item['parent'] = Request::post('parent');
                            }
                        }

                        // date
                        $item['date'] = Date::format($item['date'], 'Y-m-d H:i:s');
                        // Set Tabs State - articles
                        Notification::setNow('articles', 'articles');
                        // Display view
                        View::factory('articles/views/backend/edit')
                            ->assign('item', $item)
                            ->assign('opt', $opt)
                            ->assign('errors', $errors)
                            ->display();
                    }

                    break;

                // Delete article
                // -------------------------------------
                case "delete_articles":

                    // Error 404 articles can not be removed
                    if (Request::get('slug') !== 'error404') {

                        if (Security::check(Request::get('token'))) {

                            // Get specific articles
                            $item = $articles->select('[id="'.Request::get('uid').'"]', null);

                            //  Delete article and update <parent> fields
                            if ($articles->deleteWhere('[slug="'.$item['slug'].'" ]')) {

                                $_articles = $articles->select('[parent="'.$item['slug'].'"]');

                                if ( ! empty($_articles)) {
                                    foreach($_articles as $_articles) {
                                        $articles->updateWhere('[slug="'.$_articles['slug'].'"]', array('parent' => ''));
                                    }
                                }

                                File::delete($opt['dir'] . $item['id'] . '.jpg');
                                File::delete($opt['dir']. 'thumbnail' . DS . $item['id'] . '.jpg');
                                File::delete(STORAGE . DS . 'articles' . DS . $item['id'] . '.articles.txt');
                                File::delete(STORAGE . DS . 'articles' . DS . $item['id'] . '.short.article.txt');
                                Notification::set('success', __('Articles <i>:article</i> deleted', 'articles', array(':article' => Html::toText($item['title']))));
                            }

                            // Run delete extra actions
                            Action::run('admin_articles_action_delete');

                            // Redirect
                            Request::redirect('index.php?id=articles');

                        } else { die('csrf detected!'); }
                    }

                    break;
            }

            // Its mean that you can add your own actions for this plugin
            Action::run('admin_articles_extra_actions');

        } else {

            // Index action
            // -------------------------------------

            // Init vars
            $count = 0;
            $items = array();
            $limit = Option::get('article_limit_admin');
            $records_all = $articles->select(null, 'all', null, array('slug', 'title', 'status', 'date', 'author', 'expand', 'access', 'parent'));
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

                    $_articles = $articles->select('[slug="'.$item['parent'].'"]', null);

                    if (isset($_articles['title'])) {
                        $_title = $_articles['title'];
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
            View::factory('articles/views/backend/index')
                ->assign('items', $items)
                ->assign('opt', $opt)
                ->display();
        }

    }


    /**
     * Form Component Save
     */
    public static function formComponentSave() {
        if (Request::post('article_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('article_template', Request::post('article_form_template'));
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
                Form::label('article_form_template', __('Articles template', 'articles')).
                Form::select('article_form_template', $templates, Option::get('article_template')).
                Html::br().
                Form::submit('article_component_save', __('Save', 'articles'), array('class' => 'btn')).
                Form::close()
        );
    }

    private static function UploadImage($uid, $_FILES)
    {
        $dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'articles' . DS;

        if ($_FILES['article_file']) {
            if($_FILES['article_file']['type'] == 'image/jpeg' ||
                $_FILES['article_file']['type'] == 'image/png' ||
                $_FILES['article_file']['type'] == 'image/gif') {

                $img  = Image::factory($_FILES['article_file']['tmp_name']);
                $file['wmax']   = (int)Option::get('article_wmax');
                $file['hmax']   = (int)Option::get('article_hmax');
                $file['w']      = (int)Option::get('article_w');
                $file['h']      = (int)Option::get('article_h');
                $file['resize'] = Option::get('article_resize');
                DevAdmin::ReSize($img, $dir, $uid.'.jpg', $file);
            }
        }
    }
}