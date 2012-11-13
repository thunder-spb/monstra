<?php 

    Navigation::add(__('News', 'news'), 'content', 'news', 10);
    
    Action::add('admin_themes_extra_index_template_actions','NewsAdmin::formComponent');
    Action::add('admin_themes_extra_actions','NewsAdmin::formComponentSave');
    
    Stylesheet::add('plugins/news/news/admin.css', 'backend', 11);
    
    class NewsAdmin extends Backend {

	    public static function main() {
            
            $site_url = Option::get('siteurl');
            $errors = array();
            $status_array = array('published' => __('Published', 'news'), 'draft' => __('Draft', 'news'));
            
            $news = new Table('news'); 
            $users = new Table('users');  
            
            $user = $users->select('[id='.Session::get('user_id').']', null);

            $user['firstname'] = Html::toText($user['firstname']);
            $user['lastname']  = Html::toText($user['lastname']);

            if (isset($user['firstname']) && trim($user['firstname']) !== '') {
                if (trim($user['lastname']) !== '') $lastname = ' '.$user['lastname']; else $lastname = '';
                $author = $user['firstname'] . $lastname;
            } else {
                $author = Session::get('user_login');
            }
            
            Action::run('admin_news_extra_actions');
            
            if (Request::get('action')) {
                switch (Request::get('action')) {
                    
                    case "settings":
                        
                        if (Request::post('news_submit_settings_cancel')) {
                            Request::redirect('index.php?id=news');
                        }
                        
                        if (Request::post('news_submit_settings')) {
                            if (Security::check(Request::post('csrf'))) {
                                Option::update(array(
                                    'news_limit'  => (int)Request::post('limit'), 
                                    'news_limit_admin' => (int)Request::post('limit_admin')
                                ));
                                
                                Notification::set('success', __('Your changes have been saved', 'news'));
                                                
                                Request::redirect('index.php?id=news');
                            } else { die('csrf detected!'); }
                        }
            
                        View::factory('news/views/backend/settings')->display();
                        Action::run('admin_news_extra_settings_template'); 
                        break;
                        
                    case "edit":
                        
                        if(Request::get('news_id')) {
                            
                            if (Request::post('edit_news') || Request::post('edit_news_and_exit')) {
                                if (Security::check(Request::post('csrf'))) {
                                
                                    if (trim(Request::post('news_name')) == '') $errors['news_empty_name'] = __('Required field', 'news');
                                
                                    if (trim(Request::post('news_slug')) == '') $news_slug = trim(Request::post('news_name')); 
                                    else $news_slug = trim(Request::post('news_slug'));

                                    if (Valid::date(Request::post('news_date'))) $date = strtotime(Request::post('news_date'));
                                    else $date = time();
                                    
                                    // paranoia
                                    if (Request::post('news_id')) {
                                        if (Request::post('news_id') == Request::get('news_id')) {
                                            $id = (int)Request::post('news_id');
                                        } else {
                                            $errors['news_empty_id'] = 'error: post id != get id';
                                        }
                                    } else {
                                        $errors['news_empty_id'] = 'error: empty id';
                                    }
                                    
                                    if (count($errors) == 0) {
                                
                                        $data = array(
                                            'name'         => Request::post('news_name'),
                                            'title'        => Request::post('news_title'), 
                                            'h1'           => Request::post('news_h1'), 
                                            'description'  => Request::post('news_description'),
                                            'keywords'     => Request::post('news_keywords'),
                                            'slug'         => Security::safeName($news_slug, '-', true),
                                            'date'         => $date,
                                            'author'       => $author,
                                            'status'       => Request::post('status')
                                        );
                                                            
                                        if($news->updateWhere('[id='.$id.']', $data)) {
                                            File::setContent(STORAGE . DS . 'news' . DS . $id . '.news.txt', XML::safe(Request::post('editor')));
                                            Notification::set('success', __('Your changes to the news <i>:news</i> have been saved.', 'news', 
                                                array(':news' => Security::safeName(Request::post('news_name'), '-', true))));
                                        }

                                        Action::run('admin_news_action_edit');   
                   
                                        if (Request::post('edit_news_and_exit')) {                
                                            Request::redirect('index.php?id=news');                                       
                                        } else {
                                            Request::redirect('index.php?id=news&action=edit&news_id='.$id); 
                                        } 
                                    }
                                }
                            }
                            
                            $id = (int)Request::get('news_id');
                            $data = $news->select('[id="'.$id.'"]', null);
                            
                            if($data) {
                                
                                $news_content = File::getContent(STORAGE . DS . 'news' . DS . $id . '.news.txt');
                                
                                $post_name          = (Request::post('news_name'))        ? Request::post('news_name')        : $data['name'];
                                $post_slug          = (Request::post('news_slug'))        ? Request::post('news_slug')        : $data['slug'];
                                $post_h1            = (Request::post('news_h1'))          ? Request::post('news_h1')          : $data['h1'];
                                $post_title         = (Request::post('news_title'))       ? Request::post('news_title')       : $data['title'];
                                $post_keywords      = (Request::post('news_keywords'))    ? Request::post('news_keywords')    : $data['keywords'];
                                $post_description   = (Request::post('news_description')) ? Request::post('news_description') : $data['description'];
                                $status             = (Request::post('status'))           ? Request::post('status')           : $data['status'];
                                $date               = (Request::post('news_date'))        ? Request::post('news_date')        : $data['date']; $post_content       = (Request::post('editor'))           ? Request::post('editor') : Text::toHtml($news_content);
                                
                                $date = Date::format($date, 'Y-m-d H:i:s');
                                
                                Notification::setNow('news', 'news');
                                
                                View::factory('news/views/backend/edit')
                                    ->assign('news_id', $id)
                                    ->assign('post_name', $post_name)
                                    ->assign('post_slug', $post_slug)
                                    ->assign('post_h1', $post_h1)
                                    ->assign('post_title', $post_title)
                                    ->assign('post_description', $post_description)
                                    ->assign('post_keywords', $post_keywords)
                                    ->assign('post_content', $post_content)
                                    ->assign('status_array', $status_array)
                                    ->assign('status', $status)
                                    ->assign('date', $date)
                                    ->assign('errors', $errors)                                    
                                    ->display();
                            }
                        }
                        break;
                        
                    case "add":
                    
                        if (Request::post('add_news') || Request::post('add_news_and_exit')) {
                            if (Security::check(Request::post('csrf'))) {
                                
                                if (trim(Request::post('news_name')) == '') $errors['news_empty_name'] = __('Required field', 'news');
                                
                                if (trim(Request::post('news_slug')) == '') $news_slug = trim(Request::post('news_name')); 
                                else $news_slug = trim(Request::post('news_slug'));

                                if (Valid::date(Request::post('news_date'))) $date = strtotime(Request::post('news_date'));
                                else $date = time();
                                
                                if (count($errors) == 0) {
                                
                                    $data = array(
                                        'name'         => Request::post('news_name'),
                                        'title'        => Request::post('news_title'), 
                                        'h1'           => Request::post('news_h1'), 
                                        'description'  => Request::post('news_description'),
                                        'keywords'     => Request::post('news_keywords'),
                                        'slug'         => Security::safeName($news_slug, '-', true),
                                        'date'         => $date,
                                        'author'       => $author,
                                        'status'       => Request::post('status'),
                                        'hits' => 0
                                    );
                                                            
                                    if($news->insert($data)) {
                                                           
                                        $last_id = $news->lastId();

                                        File::setContent(STORAGE . DS . 'news' . DS . $last_id . '.news.txt', XML::safe(Request::post('editor')));
                                        
                                        Notification::set('success', __('Your changes to the news <i>:news</i> have been saved.', 'news', array(':news' => Security::safeName(Request::post('news_name'), '-', true))));
                                    }

                                    Action::run('admin_news_action_add');   
                   
                                    if (Request::post('add_news_and_exit')) {                
                                        Request::redirect('index.php?id=news');                                       
                                    } else {
                                        Request::redirect('index.php?id=news&action=edit&news_id='.$last_id); 
                                    } 
                                }
                            } else { die('csrf detected!'); }
                        }
                        
                        $post_name          = (Request::post('news_name'))        ? Request::post('news_name')        : '';
                        $post_slug          = (Request::post('news_slug'))        ? Request::post('news_slug')        : '';
                        $post_h1            = (Request::post('news_h1'))          ? Request::post('news_h1')          : '';
                        $post_title         = (Request::post('news_title'))       ? Request::post('news_title')       : '';
                        $post_keywords      = (Request::post('news_keywords'))    ? Request::post('news_keywords')    : '';
                        $post_description   = (Request::post('news_description')) ? Request::post('news_description') : '';
                        $post_content       = (Request::post('editor'))           ? Request::post('editor')           : '';
                        
                        $date = Date::format(time(), 'Y-m-d H:i:s');
                        Notification::setNow('news', 'news');

                        View::factory('news/views/backend/add')
                            ->assign('post_name', $post_name)
                            ->assign('post_slug', $post_slug)
                            ->assign('post_h1', $post_h1)
                            ->assign('post_title', $post_title)
                            ->assign('post_description', $post_description)
                            ->assign('post_keywords', $post_keywords)
                            ->assign('post_content', $post_content)
                            ->assign('status_array', $status_array)
                            ->assign('date', $date)
                            ->assign('errors', $errors)                                    
                            ->display();
                        break;

                    case "delete":
                    
                        if (Request::get('news_id')) {
                            if (Security::check(Request::get('token'))) {
                                $id = (int)Request::get('news_id');
                                
                                $data = $news->select('[id='.$id.']', null);
                                
                                if ($news->deleteWhere('[id='.$id.']')) {
                                    File::delete(STORAGE . DS . 'news' . DS . $id . '.news.txt');
                                    Notification::set('success', __('News <i>:news</i> deleted', 'news', 
                                        array(':news' => Html::toText($data['name']))));
                                }

                                Action::run('admin_pages_action_delete');
                                Request::redirect('index.php?id=news');

                            } else { die('csrf detected!'); }
                        } 
                        break;
                }
                
            } else {
                $limit = Option::get('news_limit_admin');
                $records_all = $news->select(null, 'all', null, array('name', 'slug', 'status', 'date', 'author', 'hits'));
                $count_news = count($records_all);
                $pages = ceil($count_news/$limit);
            
                $page = (Request::get('page')) ? (int)Request::get('page') : 1;
                $sort = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
                $order = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';
                
                if ($page < 1) { $page = 1; } 
                elseif ($page > $pages) { $page = $pages; }
            
                $start = ($page-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, $sort, $order);
                if($count_news>0) $records = array_slice($records_sort, $start, $limit);
                else $records = array();

                View::factory('news/views/backend/index')
                    ->assign('news_list', $records)
                    ->assign('site_url', $site_url)
                    ->assign('status_array', $status_array)
                    ->assign('current_page', $page)
                    ->assign('pages_count', $pages)
                    ->assign('sort', $sort)
                    ->assign('order', $order)
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
                $t = basename($template, '.template.php');
                $templates[$t] = $t;
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
	}