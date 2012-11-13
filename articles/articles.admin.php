<?php 

    Navigation::add(__('Articles', 'articles'), 'content', 'articles', 10);
    
    Action::add('admin_themes_extra_index_template_actions','ArticlesAdmin::formComponent');
    Action::add('admin_themes_extra_actions','ArticlesAdmin::formComponentSave');
    
    Stylesheet::add('plugins/articles/content/admin.css', 'backend', 11);
    
    class ArticlesAdmin extends Backend {

	    public static function main() {
            
            $site_url = Option::get('siteurl');
            $errors = array();
            $status_array = array('published' => __('Published', 'articles'), 'draft' => __('Draft', 'articles'));
            
            $articles = new Table('articles'); 
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
            
            Action::run('admin_articles_extra_actions');
            
            if (Request::get('action')) {
                switch (Request::get('action')) {
                    
                    case "settings":
                        
                        if (Request::post('articles_submit_settings_cancel')) {
                            Request::redirect('index.php?id=articles');
                        }
                        
                        if (Request::post('articles_submit_settings')) {
                            if (Security::check(Request::post('csrf'))) {
                                Option::update(array(
                                    'articles_limit'  => (int)Request::post('limit'), 
                                    'articles_limit_admin' => (int)Request::post('limit_admin')
                                ));
                                
                                Notification::set('success', __('Your changes have been saved', 'articles'));
                                                
                                Request::redirect('index.php?id=articles');
                            } else { die('csrf detected!'); }
                        }
            
                        View::factory('articles/views/backend/settings')->display();
                        Action::run('admin_articles_extra_settings_template'); 
                        break;
                        
                    case "edit":
                        
                        if(Request::get('articles_id')) {
                            
                            if (Request::post('edit_articles') || Request::post('edit_articles_and_exit')) {
                                if (Security::check(Request::post('csrf'))) {
                                
                                    if (trim(Request::post('articles_name')) == '') $errors['articles_empty_name'] = __('Required field', 'articles');
                                
                                    if (trim(Request::post('articles_slug')) == '') $articles_slug = trim(Request::post('articles_name')); 
                                    else $articles_slug = trim(Request::post('articles_slug'));

                                    if (Valid::date(Request::post('articles_date'))) $date = strtotime(Request::post('articles_date'));
                                    else $date = time();
                                    
                                    // paranoia
                                    if (Request::post('articles_id')) {
                                        if (Request::post('articles_id') == Request::get('articles_id')) {
                                            $id = (int)Request::post('articles_id');
                                        } else {
                                            $errors['articles_empty_id'] = 'error: post id != get id';
                                        }
                                    } else {
                                        $errors['articles_empty_id'] = 'error: empty id';
                                    }
                                    
                                    if (count($errors) == 0) {
                                
                                        $data = array(
                                            'name'         => Request::post('articles_name'),
                                            'title'        => Request::post('articles_title'), 
                                            'h1'           => Request::post('articles_h1'), 
                                            'description'  => Request::post('articles_description'),
                                            'keywords'     => Request::post('articles_keywords'),
                                            'slug'         => Security::safeName($articles_slug, '-', true),
                                            'date'         => $date,
                                            'author'       => $author,
                                            'status'       => Request::post('status')
                                        );
                                                            
                                        if($articles->updateWhere('[id='.$id.']', $data)) {
                                            File::setContent(STORAGE . DS . 'articles' . DS . $id . '.articles.txt', XML::safe(Request::post('editor')));
                                            Notification::set('success', __('Your changes to the articles <i>:articles</i> have been saved.', 'articles', 
                                                array(':articles' => Security::safeName(Request::post('articles_name'), '-', true))));
                                        }

                                        Action::run('admin_articles_action_edit');   
                   
                                        if (Request::post('edit_articles_and_exit')) {                
                                            Request::redirect('index.php?id=articles');                                       
                                        } else {
                                            Request::redirect('index.php?id=articles&action=edit&articles_id='.$id); 
                                        } 
                                    }
                                }
                            }
                            
                            $id = (int)Request::get('articles_id');
                            $data = $articles->select('[id="'.$id.'"]', null);
                            
                            if($data) {
                                
                                $articles_content = File::getContent(STORAGE . DS . 'articles' . DS . $id . '.articles.txt');
                                
                                $post_name          = (Request::post('articles_name'))        ? Request::post('articles_name')        : $data['name'];
                                $post_slug          = (Request::post('articles_slug'))        ? Request::post('articles_slug')        : $data['slug'];
                                $post_h1            = (Request::post('articles_h1'))          ? Request::post('articles_h1')          : $data['h1'];
                                $post_title         = (Request::post('articles_title'))       ? Request::post('articles_title')       : $data['title'];
                                $post_keywords      = (Request::post('articles_keywords'))    ? Request::post('articles_keywords')    : $data['keywords'];
                                $post_description   = (Request::post('articles_description')) ? Request::post('articles_description') : $data['description'];
                                $status             = (Request::post('status'))           ? Request::post('status')           : $data['status'];
                                $date               = (Request::post('articles_date'))        ? Request::post('articles_date')        : $data['date']; $post_content       = (Request::post('editor'))           ? Request::post('editor') : Text::toHtml($articles_content);
                                
                                $date = Date::format($date, 'Y-m-d H:i:s');
                                
                                Notification::setNow('articles', 'articles');
                                
                                View::factory('articles/views/backend/edit')
                                    ->assign('articles_id', $id)
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
                    
                        if (Request::post('add_articles') || Request::post('add_articles_and_exit')) {
                            if (Security::check(Request::post('csrf'))) {
                                
                                if (trim(Request::post('articles_name')) == '') $errors['articles_empty_name'] = __('Required field', 'articles');
                                
                                if (trim(Request::post('articles_slug')) == '') $articles_slug = trim(Request::post('articles_name')); 
                                else $articles_slug = trim(Request::post('articles_slug'));

                                if (Valid::date(Request::post('articles_date'))) $date = strtotime(Request::post('articles_date'));
                                else $date = time();
                                
                                if (count($errors) == 0) {
                                
                                    $data = array(
                                        'name'         => Request::post('articles_name'),
                                        'title'        => Request::post('articles_title'), 
                                        'h1'           => Request::post('articles_h1'), 
                                        'description'  => Request::post('articles_description'),
                                        'keywords'     => Request::post('articles_keywords'),
                                        'slug'         => Security::safeName($articles_slug, '-', true),
                                        'date'         => $date,
                                        'author'       => $author,
                                        'status'       => Request::post('status'),
                                        'hits' => 0
                                    );
                                                            
                                    if($articles->insert($data)) {
                                                           
                                        $last_id = $articles->lastId();

                                        File::setContent(STORAGE . DS . 'articles' . DS . $last_id . '.articles.txt', XML::safe(Request::post('editor')));
                                        
                                        Notification::set('success', __('Your changes to the articles <i>:articles</i> have been saved.', 'articles', array(':articles' => Security::safeName(Request::post('articles_name'), '-', true))));
                                    }

                                    Action::run('admin_articles_action_add');   
                   
                                    if (Request::post('add_articles_and_exit')) {                
                                        Request::redirect('index.php?id=articles');                                       
                                    } else {
                                        Request::redirect('index.php?id=articles&action=edit&articles_id='.$last_id); 
                                    } 
                                }
                            } else { die('csrf detected!'); }
                        }
                        
                        $post_name          = (Request::post('articles_name'))        ? Request::post('articles_name')        : '';
                        $post_slug          = (Request::post('articles_slug'))        ? Request::post('articles_slug')        : '';
                        $post_h1            = (Request::post('articles_h1'))          ? Request::post('articles_h1')          : '';
                        $post_title         = (Request::post('articles_title'))       ? Request::post('articles_title')       : '';
                        $post_keywords      = (Request::post('articles_keywords'))    ? Request::post('articles_keywords')    : '';
                        $post_description   = (Request::post('articles_description')) ? Request::post('articles_description') : '';
                        $post_content       = (Request::post('editor'))           ? Request::post('editor')           : '';
                        
                        $date = Date::format(time(), 'Y-m-d H:i:s');
                        Notification::setNow('articles', 'articles');

                        View::factory('articles/views/backend/add')
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
                    
                        if (Request::get('articles_id')) {
                            if (Security::check(Request::get('token'))) {
                                $id = (int)Request::get('articles_id');
                                
                                $data = $articles->select('[id='.$id.']', null);
                                
                                if ($articles->deleteWhere('[id='.$id.']')) {
                                    File::delete(STORAGE . DS . 'articles' . DS . $id . '.articles.txt');
                                    Notification::set('success', __('Articles <i>:articles</i> deleted', 'articles', 
                                        array(':articles' => Html::toText($data['name']))));
                                }

                                Action::run('admin_pages_action_delete');
                                Request::redirect('index.php?id=articles');

                            } else { die('csrf detected!'); }
                        } 
                        break;
                }
                
            } else {
                $limit = Option::get('articles_limit_admin');
                $records_all = $articles->select(null, 'all', null, array('name', 'slug', 'status', 'date', 'author', 'hits'));
                $count_articles = count($records_all);
                $pages = ceil($count_articles/$limit);
            
                $page = (Request::get('page')) ? (int)Request::get('page') : 1;
                $sort = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
                $order = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';
                
                if ($page < 1) { $page = 1; } 
                elseif ($page > $pages) { $page = $pages; }
            
                $start = ($page-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, $sort, $order);
                if($count_articles>0) $records = array_slice($records_sort, $start, $limit);
                else $records = array();

                View::factory('articles/views/backend/index')
                    ->assign('articles_list', $records)
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
            if (Request::post('articles_component_save')) {
                if (Security::check(Request::post('csrf'))) {
                    Option::update('articles_template', Request::post('articles_form_template'));
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
                Form::label('articles_form_template', __('Articles template', 'articles')).
                Form::select('articles_form_template', $templates, Option::get('articles_template')).
                Html::br().
                Form::submit('articles_component_save', __('Save', 'articles'), array('class' => 'btn')).        
                Form::close()
            );
        }
	}