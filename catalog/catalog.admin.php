<?php

Navigation::add(__('Catalog', 'catalog'), 'content', 'catalog', 10);

Action::add('admin_themes_extra_index_template_actions','CatalogAdmin::formComponent');
Action::add('admin_themes_extra_actions','CatalogAdmin::formComponentSave');
Action::add('admin_pre_render','CatalogAdmin::ajaxQuery');

class CatalogAdmin extends Backend {

    public static function main() {

        $opt['site_url'] = Option::get('siteurl');
        $errors = array();
        $opt['status'] = array('published' => __('Published', 'catalog'), 'draft' => __('Draft', 'catalog'));

        $items = new Table('cat_items');
        $folders = new Table('cat_folder');
        $tags = new Table('cat_tag');
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

        $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
        $opt['url'] = $opt['site_url'] . 'public/uploads/catalog/';

        if (Request::get('action')) {
            switch (Request::get('action')) {

                case "settings":

                    if (Request::post('catalog_submit_settings_cancel')) {
                        Request::redirect('index.php?id=catalog');
                    }

                    if (Request::post('catalog_submit_settings')) {
                        if (Security::check(Request::post('csrf'))) {
                            Option::update(array(
                                'catalog_limit'  => (int)Request::post('limit'),
                                'catalog_limit_admin' => (int)Request::post('limit_admin'),
                                'catalog_w' => (int)Request::post('width_thumb'),
                                'catalog_h' => (int)Request::post('height_thumb'),
                                'catalog_wmax'   => (int)Request::post('width_orig'),
                                'catalog_hmax'   => (int)Request::post('height_orig'),
                                'catalog_resize' => (string)Request::post('resize'),
                                'catalog_currency' => (string)Request::post('currency')
                            ));

                            Notification::set('success', __('Your changes have been saved', 'catalog'));

                            Request::redirect('index.php?id=catalog');
                        } else { die('csrf detected!'); }
                    }

                    View::factory('catalog/views/backend/settings')->display();
                    break;

                case "tag":

                    if (Request::post('catalog_submit_tag_cancel')) {
                        Request::redirect('index.php?id=catalog');
                    }

                    if (Request::post('catalog_save_tag')) {
                        if (Security::check(Request::post('csrf'))) {
                            $id = (int)Request::post('edit_uid');
                            $data = array(
                                'title'        => Request::post('catalog_title'),
                                'sorting'      => (int)Request::post('catalog_sort')
                            );

                            if ($id > 0)
                            {
                                if($tags->updateWhere('[id='.$id.']', $data)) {
                                    Notification::set('success', __('Tag <i>:item</i> have been saved.', 'catalog', array(':item' => Request::post('catalog_title'))));
                                }
                            }
                            else
                            {
                                if($tags->insert($data)) {
                                    Notification::set('success', __('New tag <i>:item</i> have been added.', 'catalog', array(':item' => Request::post('catalog_title'))));
                                }
                            }

                            Request::redirect('index.php?id=catalog&action=tag');
                        } else { die('csrf detected!'); }
                    }

                    $records_all = $tags->select(null, 'all');
                    $records_sort = Arr::subvalSort($records_all, 'sorting');
                    View::factory('catalog/views/backend/tag')
                        ->assign('items', $records_sort)
                        ->display();
                    break;

                case "items":
                    $limit = Option::get('catalog_limit_admin');
                    $id = (int)Request::get('catalog_id');
                    $records_all = $items->select('[catalog="'.$id.'"]', 'all', null, array('name', 'price', 'title', 'h1', 'description', 'keywords', 'slug', 'date', 'author', 'status', 'catalog', 'hits'));
                    $opt['catalog'] = $folders->select('[id='.$id.']', null);
                    $cnt = count($records_all);
                    $opt['pages'] = ceil($cnt/$limit);

                    $opt['cid'] = (Request::get('catalog_id')) ? (int)Request::get('catalog_id') : 1;
                    $opt['page'] = (Request::get('page')) ? (int)Request::get('page') : 1;
                    $opt['sort'] = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
                    $opt['order'] = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';

                    if ($opt['page'] < 1) { $opt['page'] = 1; }
                    elseif ($opt['page'] > $opt['pages']) { $opt['page'] = $opt['pages']; }

                    $start = ($opt['page']-1)*$limit;

                    $records_sort = Arr::subvalSort($records_all, $opt['sort'], $opt['order']);
                    if($cnt>0) $records = array_slice($records_sort, $start, $limit);
                    else $records = array();

                    View::factory('catalog/views/backend/items')
                        ->assign('items', $records)
                        ->assign('opt', $opt)
                        ->display();
                    break;

                case "cat_add":
                    if (Request::post('exit'))
                    {
                        Request::redirect('index.php?id=catalog');
                    }
                    $tag_list = $tags->select(null, 'all', null, array('id', 'title'));
                    if (count($tag_list) == 0)
                    {
                        Notification::set('error', __('Add tag for create catalog', 'catalog'));
                        Request::redirect('index.php?id=catalog&action=tag');
                    }
                    else
                    {
                        $opt['tags'] = array();
                        foreach ($tag_list as $row)
                        {
                            $opt['tags'][$row['id']] = $row['title'];
                        }
                    }

                    if (Request::post('add_catalog') || Request::post('add_catalog_and_exit')) {
                        if (Security::check(Request::post('csrf'))) {

                            if (count($errors) == 0) {

                                $data = array(
                                    'title'         => (string)Request::post('catalog_title'),
                                    'slug'          => Security::safeName(Request::post('catalog_slug'), '-', true),
                                    'description'   => (string)Request::post('catalog_description'),
                                    'keywords'      => (string)Request::post('catalog_keywords'),
                                    'tags'          => (int)Request::post('catalog_tag'),
                                    'parent'        => 0
                                );

                                if($folders->insert($data)) {
                                    $last_id = $folders->lastId();
                                    File::setContent(STORAGE . DS . 'catalog' . DS .'catalog.'. $last_id .'.txt', XML::safe(Request::post('editor')));
                                    CatalogAdmin::UploadImage('cat_'.$last_id, $_FILES);
                                    Notification::set('success', __('New catalog <i>:catalog</i> have been added.', 'catalog', array(':catalog' => $data['title'])));
                                }

                                if (Request::post('add_catalog')) {
                                    Request::redirect('index.php?id=catalog&action=cat_edit&catalog_id='.$last_id);
                                } else {
                                    Request::redirect('index.php?id=catalog');
                                }
                            }
                        } else { die('csrf detected!'); }
                    }

                    $post['slug']          = (Request::post('catalog_slug'))        ? Request::post('catalog_slug')        : '';
                    $post['title']         = (Request::post('catalog_title'))       ? Request::post('catalog_title')       : '';
                    $post['description']   = (Request::post('catalog_description')) ? Request::post('catalog_description') : '';
                    $post['keywords']      = (Request::post('catalog_keywords'))    ? Request::post('catalog_keywords')    : '';

                    View::factory('catalog/views/backend/cat_add')
                        ->assign('post', $post)
                        ->assign('opt', $opt)
                        ->assign('errors', $errors)
                        ->display();
                    break;

                case "cat_edit":
                    if(Request::get('catalog_id')) {

                        if (Request::post('edit_catalog') || Request::post('edit_catalog_and_exit')) {
                            if (Security::check(Request::post('csrf'))) {
                                if (count($errors) == 0) {

                                    $data = array(
                                        'title'         => (string)Request::post('catalog_title'),
                                        'slug'          => Security::safeName(Request::post('catalog_slug'), '-', true),
                                        'description'   => (string)Request::post('catalog_description'),
                                        'keywords'      => (string)Request::post('catalog_keywords'),
                                        'tags'          => (int)Request::post('catalog_tag'),
                                        'parent' => 0
                                    );

                                    $id = (int)Request::post('catalog_id');

                                    if($folders->updateWhere('[id='.$id.']', $data)) {
                                        CatalogAdmin::UploadImage('cat_'.$id, $_FILES);
                                        File::setContent(STORAGE . DS . 'catalog' . DS .'catalog.'. $id .'.txt', XML::safe(Request::post('editor')));
                                        Notification::set('success', __('Your changes to the catalog <i>:catalog</i> have been saved.', 'catalog', array(':catalog' => Request::post('catalog_title'))));
                                    }

                                    if (Request::post('edit_catalog_and_exit')) {
                                        Request::redirect('index.php?id=catalog');
                                    } else {
                                        Request::redirect('index.php?id=catalog&action=cat_edit&catalog_id='.$id);
                                    }
                                }
                            } else { die('csrf detected!'); }
                        }

                        $tag_list = $tags->select(null, 'all', null, array('id', 'title'));
                        $opt['tags'] = array();
                        foreach ($tag_list as $row)
                        {
                            $opt['tags'][$row['id']] = $row['title'];
                        }

                        $post['cid'] = (int)Request::get('catalog_id');
                        $data = $folders->select('[id="'.$post['cid'].'"]', null);

                        if($data) {
                            $post['slug']          = (Request::post('catalog_slug'))        ? Request::post('catalog_slug')        : $data['slug'];
                            $post['title']         = (Request::post('catalog_title'))       ? Request::post('catalog_title')       : $data['title'];
                            $post['description']   = (Request::post('catalog_description')) ? Request::post('catalog_description') : $data['description'];
                            $post['keywords']      = (Request::post('catalog_keywords'))    ? Request::post('catalog_keywords')    : $data['keywords'];
                            $post['tags']          = (Request::post('catalog_tags'))        ? Request::post('catalog_tag')         : $data['tags'];
                            $post['content']       = (Request::post('editor'))              ? Request::post('editor')              : Text::toHtml(File::getContent(STORAGE . DS . 'catalog' . DS. 'catalog.'. $post['cid'] .'.txt'));

                            View::factory('catalog/views/backend/cat_edit')
                                ->assign('post', $post)
                                ->assign('opt', $opt)
                                ->assign('errors', $errors)
                                ->display();
                        }
                    }
                    break;

                case "add":

                    $opt['cid'] = (int)Request::get('catalog_id');
                    $folder = $folders->select('[id='.$opt['cid'].']', null);
                    $opt['title'] = $folder['title'];

                    if (Request::post('exit'))
                    {
                        Request::redirect('index.php?id=catalog&action=items&catalog_id='.$opt['cid']);
                    }
                    if (Request::post('add_item') || Request::post('add_item_and_exit')) {
                        if (Security::check(Request::post('csrf'))) {

                            if (Valid::date(Request::post('catalog_date'))) $date = strtotime(Request::post('catalog_date'));
                            else $date = time();

                            if (count($errors) == 0) {

                                $data = array(
                                    'title'        => Request::post('catalog_title'),
                                    'price'        => Request::post('catalog_price'),
                                    'currency'     => Request::post('catalog_currency'),
                                    'h1'           => Request::post('catalog_h1'),
                                    'description'  => Request::post('catalog_description'),
                                    'keywords'     => Request::post('catalog_keywords'),
                                    'short'        => Request::post('catalog_short'),
                                    'date'         => $date,
                                    'author'       => $author,
                                    'status'       => Request::post('catalog_status'),
                                    'catalog'      => $opt['cid'],
                                    'hits' => 0
                                );

                                if($items->insert($data)) {

                                    $last_id = $items->lastId();

                                    File::setContent(STORAGE . DS . 'catalog' . DS . 'item.' . $last_id . '.txt', XML::safe(Request::post('editor')));
                                    CatalogAdmin::UploadImage($last_id, $_FILES);
                                    Notification::set('success', __('New item <i>:item</i> have been added.', 'catalog', array(':item' => Request::post('catalog_title'))));
                                }

                                if (Request::post('add_item') && isset($last_id)) {
                                    Request::redirect('index.php?id=catalog&action=edit&item_id='.$last_id);
                                } else {
                                    Request::redirect('index.php?id=catalog&action=items&catalog_id='.$opt['cid']);
                                }
                            }
                        } else { die('csrf detected!'); }
                    }

                    $post['title']        = (Request::post('catalog_title'))       ? Request::post('catalog_title')       : '';
                    $post['price']        = (Request::post('catalog_price'))       ? Request::post('catalog_price')       : '';
                    $post['currency']     = (Request::post('catalog_currency'))    ? Request::post('catalog_currency')    : Option::get('catalog_currency');
                    $post['h1']           = (Request::post('catalog_h1'))          ? Request::post('catalog_h1')          : '';
                    $post['description']  = (Request::post('catalog_description')) ? Request::post('catalog_description') : '';
                    $post['keywords']     = (Request::post('catalog_keywords'))    ? Request::post('catalog_keywords')    : '';
                    $post['short']        = (Request::post('catalog_short'))       ? Request::post('catalog_short')       : '';
                    $post['content']      = (Request::post('editor'))              ? Request::post('editor')              : '';

                    $opt['date'] = Date::format(time(), 'Y-m-d H:i:s');
                    Notification::setNow('catalog', 'catalog');

                    View::factory('catalog/views/backend/add')
                        ->assign('post', $post)
                        ->assign('opt', $opt)
                        ->assign('errors', $errors)
                        ->display();
                    break;

                case "edit":

                    if(Request::get('item_id')) {

                        if (Request::post('edit_item') || Request::post('edit_item_and_exit')) {
                            if (Security::check(Request::post('csrf'))) {
                                if (Valid::date(Request::post('catalog_date'))) $date = strtotime(Request::post('catalog_date'));
                                else $date = time();

                                $id = (int)Request::post('item_id');

                                if (count($errors) == 0) {

                                    $data = array(
                                        'title'        => Request::post('catalog_title'),
                                        'price'        => Request::post('catalog_price'),
                                        'currency'     => Request::post('catalog_currency'),
                                        'h1'           => Request::post('catalog_h1'),
                                        'description'  => Request::post('catalog_description'),
                                        'keywords'     => Request::post('catalog_keywords'),
                                        'short'        => Request::post('catalog_short'),
                                        'date'         => $date,
                                        'author'       => $author,
                                        'status'       => Request::post('catalog_status')
                                    );

                                    if($items->updateWhere('[id='.$id.']', $data)) {
                                        CatalogAdmin::UploadImage($id, $_FILES);
                                        File::setContent(STORAGE . DS . 'catalog' . DS . 'item.' . $id . '.txt', XML::safe(Request::post('editor')));
                                        Notification::set('success', __('Your changes to the item <i>:item</i> have been saved.', 'catalog',
                                            array(':item' =>Request::post('catalog_title'))));
                                    }

                                    if (Request::post('edit_item_and_exit')) {
                                        Request::redirect('index.php?id=catalog&action=items&catalog_id='.Request::post('cat_id'));
                                    } else {
                                        Request::redirect('index.php?id=catalog&action=edit&item_id='.$id);
                                    }
                                }
                            }
                        }

                        $opt['id'] = (int)Request::get('item_id');
                        $data = $items->select('[id="'.$opt['id'].'"]', null);

                        if($data) {

                            $item_content = File::getContent(STORAGE . DS . 'catalog' . DS . 'item.' . $opt['id'] . '.txt');
                            $opt['cid'] = $data['catalog'];

                            $post['title']         = (Request::post('catalog_title'))       ? Request::post('catalog_title')       : $data['title'];
                            $post['price']         = (Request::post('catalog_price'))       ? Request::post('catalog_price')       : $data['price'];
                            $post['currency']      = (Request::post('catalog_currency'))    ? Request::post('catalog_currency')    : $data['currency'];
                            $post['h1']            = (Request::post('catalog_h1'))          ? Request::post('catalog_h1')          : $data['h1'];
                            $post['keywords']      = (Request::post('catalog_keywords'))    ? Request::post('catalog_keywords')    : $data['keywords'];
                            $post['description']   = (Request::post('catalog_description')) ? Request::post('catalog_description') : $data['description'];
                            $post['status']        = (Request::post('catalog_status'))      ? Request::post('catalog_status')      : $data['status'];
                            $post['date']          = (Request::post('catalog_date'))        ? Request::post('catalog_date')        : $data['date'];
                            $post['short']         = (Request::post('catalog_short'))       ? Request::post('catalog_short')       : $data['short'];
                            $post['content']       = (Request::post('editor'))              ? Request::post('editor')              : Text::toHtml($item_content);

                            $opt['date'] = Date::format($post['date'], 'Y-m-d H:i:s');

                            if ((int)Request::get('upload') > 0)
                            {
                                Notification::setNow('upload', 'catalog');
                            }
                            else
                            {
                                Notification::setNow('catalog', 'catalog');
                            }

                            View::factory('catalog/views/backend/edit')
                                ->assign('post', $post)
                                ->assign('opt', $opt)
                                ->assign('errors', $errors)
                                ->display();
                        }
                    }
                    break;

                case "delete":

                    if (Request::get('catalog_id')) {
                        if (Security::check(Request::get('token'))) {
                            $id = (int)Request::get('catalog_id');

                            $data = $folders->select('[id='.$id.']', null);

                            if ($folders->deleteWhere('[id='.$id.']')) {
                                File::delete($opt['dir'] . $id . '.jpg');
                                File::delete($opt['dir']. 'thumbnail' . DS . $id . '.jpg');
                                File::delete(STORAGE . DS . 'catalog' . DS . 'catalog.' . $id . '.txt');
                                Notification::set('success', __('Catalog <i>:catalog</i> deleted', 'catalog',
                                    array(':catalog' => $data['title'])));
                            }

                            $data = $items->select('[catalog='.$id.']');

                            if (count($data) > 0)
                            {
                                foreach ($data as $item)
                                {
                                    if ($items->deleteWhere('[id='.$item['id'].']')) {
                                        File::delete($opt['dir'] . $item['id'] . '.jpg');
                                        File::delete($opt['dir']. 'thumbnail' . DS . $item['id'] . '.jpg');
                                        File::delete(STORAGE . DS . 'catalog' . DS . 'item.' . $item['id'] . '.txt');
                                    }
                                }
                            }

                            Request::redirect('index.php?id=catalog');

                        } else { die('csrf detected!'); }
                    }

                    if (Request::get('item_id')) {
                        if (Security::check(Request::get('token'))) {
                            $id = (int)Request::get('item_id');

                            $data = $items->select('[id='.$id.']', null);

                            if ($items->deleteWhere('[id='.$id.']')) {
                                File::delete($opt['dir'] . $id . '.jpg');
                                File::delete($opt['dir']. 'thumbnail' . DS . $id . '.jpg');
                                File::delete(STORAGE . DS . 'catalog' . DS . 'item.' . $id . '.txt');
                                Notification::set('success', __('Item in <i>:catalog</i> deleted', 'catalog',
                                    array(':catalog' => Html::toText($data['title']))));
                            }

                            Request::redirect('index.php?id=catalog&action=items&catalog_id='.$data['catalog']);

                        } else { die('csrf detected!'); }
                    }

                    if (Request::get('tag_id')) {
                        if (Security::check(Request::get('token'))) {
                            $id = (int)Request::get('tag_id');

                            $data = $tags->select('[id='.$id.']', null);
                            $folder = $folders->select('[tag='.$id.']', null);

                            if (count($folder) > 0)
                            {
                                Notification::set('error', __('You can not remove the tag :tag, as it contained elements.', 'catalog',
                                    array(':tag' => Html::toText($data['title']))));
                            }
                            else
                            {
                                if ($tags->deleteWhere('[id='.$id.']')) {
                                    Notification::set('success', __('Tag <i>:tag</i> deleted', 'catalog',
                                        array(':tag' => Html::toText($data['title']))));
                                }
                            }

                            Request::redirect('index.php?id=catalog&action=items&action=tag');

                        } else { die('csrf detected!'); }
                    }
                    break;
            }

        } else {
            $limit = Option::get('catalog_limit_admin');
            $records_all = $folders->select(null, 'all', null, array('title', 'slug'));
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

            View::factory('catalog/views/backend/index')
                ->assign('items', $records)
                ->assign('opt', $opt)
//				->assign('sort', $sort)
//				->assign('order', $order)
                ->display();
        }
    }

    public static function UploadImage($uid, $_FILES) {
        $dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;

        if ($_FILES['file']) {
            if($_FILES['file']['type'] == 'image/jpeg' ||
                $_FILES['file']['type'] == 'image/png' ||
                $_FILES['file']['type'] == 'image/gif') {

                $img  = Image::factory($_FILES['file']['tmp_name']);
                $file['wmax']   = (int)Option::get('catalog_wmax');
                $file['hmax']   = (int)Option::get('catalog_hmax');
                $file['w']      = (int)Option::get('catalog_w');
                $file['h']      = (int)Option::get('catalog_h');
                $file['resize'] = Option::get('catalog_resize');
                DevAdmin::ReSize($img, $dir, $uid.'.jpg', $file);
            }
        }
    }
    /**
     *  Ajax save
     */
    public static function ajaxQuery() {
        $tags = new Table('cat_tag');

        $dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'popps' . DS;
        $id = (int)Request::get('tid');

        if (Request::get('edit') == 'tag') {
            if ($id == 0)
            {
                echo json_encode(array('id'=>$id,'h3'=>__('Add tag', 'catalog')));
                exit();
            }

            $item = $tags->select('[id="'.$id.'"]', null);

            if ($item != null)
            {
                $json_data = array ('id'=>$id,'title'=>$item['title'],'sort'=>$item['sorting'],'h3'=>__('Edit tag', 'catalog'));
            }
            else
            {
                $json_data = array ('id'=>$id,'title'=>'','sort'=>'','h3'=>__('Edit tag', 'catalog'));
            }
            echo json_encode($json_data);
            exit();
        }
    }

    /**
     * Form Component Save
     */
    public static function formComponentSave() {
        if (Request::post('catalog_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('catalog_template', Request::post('catalog_form_template'));
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
                Form::label('catalog_form_template', __('Catalog template', 'catalog')).
                Form::select('catalog_form_template', $templates, Option::get('catalog_template')).
                Html::br().
                Form::submit('catalog_component_save', __('Save', 'catalog'), array('class' => 'btn')).
                Form::close()
        );
    }
}