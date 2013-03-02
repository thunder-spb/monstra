<?php

Navigation::add(__('Gallery', 'gallery'), 'content', 'gallery', 10);

Action::add('admin_themes_extra_index_template_actions','GalleryAdmin::formComponent');
Action::add('admin_themes_extra_actions','GalleryAdmin::formComponentSave');
Action::add('admin_pre_render','GalleryAdmin::ajaxSave');

//Stylesheet::add('plugins/gallery/content/admin.css', 'backend', 11);

class GalleryAdmin extends Backend {
    public static $folder = null; // gallery table @object
    public static $items = null; // gallery table @object
    public static $resize = null;

    public static function main() {

        $site_url = Option::get('siteurl');
        $errors = array();

        GalleryAdmin::$resize = array(
            'width'   => __('Respect to the width', 'gallery'),
            'height'  => __('Respect to the height', 'gallery'),
            'crop'    => __('Similarly, cutting unnecessary', 'gallery'),
            'stretch' => __('Similarly with the expansion', 'gallery'),
        );

        GalleryAdmin::$items = new Table('gal_items');
        GalleryAdmin::$folder = new Table('gal_folder');

        Action::run('admin_gallery_extra_actions');

        $dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
        $url = $site_url . 'public/uploads/gallery/';

        if (Request::get('action')) {
            switch (Request::get('action')) {

                case "settings":

                    if (Request::post('gallery_submit_settings_cancel')) {
                        Request::redirect('index.php?id=gallery');
                    }

                    if (Request::post('gallery_submit_settings')) {
                        if (Security::check(Request::post('csrf'))) {
                            Option::update(array(
                                'gallery_limit'  => (int)Request::post('limit'),
                                'gallery_limit_admin' => (int)Request::post('limit_admin'),
                                'gallery_w' => (int)Request::post('width_thumb'),
                                'gallery_h' => (int)Request::post('height_thumb'),
                                'gallery_wmax'   => (int)Request::post('width_orig'),
                                'gallery_hmax'   => (int)Request::post('height_orig'),
                                'gallery_resize' => (string)Request::post('resize')
                            ));

                            Notification::set('success', __('Your changes have been saved', 'gallery'));

                            Request::redirect('index.php?id=gallery');
                        } else { die('csrf detected!'); }
                    }

                    View::factory('gallery/views/backend/settings')->display();
                    Action::run('admin_gallery_extra_settings_template');
                    break;

                case "items":
                    if (Request::post('gallery_save_image')) {
                        if (Security::check(Request::post('csrf'))) {
                            $data = array(
                                'title'         => Request::post('gallery_title'),
                                'description'   => Request::post('gallery_desc')
                            );

                            $id = (int)Request::post('gallery_id');

                            if(GalleryAdmin::$items->updateWhere('[id='.$id.']', $data)) {
                                Notification::set('success', __('Your changes to the album <i>:album</i> have been saved.', 'gallery', array(':album' => Request::post('gallery_title', '-', true))));
                            }
                            else {
                                Notification::set('error', __('Your changes to the album <i>:album</i> have been saved.', 'gallery', array(':album' => Request::post('gallery_title', '-', true))));
                            }
                            Request::redirect('index.php?id=gallery&action=items&gallery_id='.(int)Request::post('guid'));
                        } else { die('csrf detected!'); }
                    }

                    $limit = Option::get('gallery_limit_admin');
                    $id = (int)Request::get('gallery_id');
                    $records_all = GalleryAdmin::$items->select('[guid="'.$id.'"]', 'all', null, array('title', 'description', 'date', 'author'));
                    $album = GalleryAdmin::$folder->select('[id='.$id.']', null);
                    $count_gallery = count($records_all);
                    $pages = ceil($count_gallery/$limit);

                    $page = (Request::get('page')) ? (int)Request::get('page') : 1;
                    $sort = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
                    $order = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';

                    if ($page < 1) { $page = 1; }
                    elseif ($page > $pages) { $page = $pages; }

                    $start = ($page-1)*$limit;

                    $records_sort = Arr::subvalSort($records_all, $sort, $order);
                    if($count_gallery>0) $records = array_slice($records_sort, $start, $limit);
                    else $records = array();

                    View::factory('gallery/views/backend/items')
                        ->assign('gallery_list', $records)
                        ->assign('gallery_id', $album['id'])
                        ->assign('gallery_title', $album['title'])
                        ->assign('site_url', $site_url)
                        ->assign('current_page', $page)
                        ->assign('pages_count', $pages)
                        ->assign('sort', $sort)
                        ->assign('order', $order)
                        ->assign('dir', $dir)
                        ->assign('url', $url)
                        ->display();
                    break;

                case "add":
                    if (Request::post('add_album') || Request::post('add_album_and_exit')) {
                        if (Security::check(Request::post('csrf'))) {

                            if (count($errors) == 0) {

                                $sections = Request::post('gallery_sections') == null ? '0' : '1';
                                $data = array(
                                    'title'         => Request::post('gallery_title'),
                                    'slug'          => Security::safeName(Request::post('gallery_slug'), '-', true),
                                    'keywords'      => Request::post('gallery_keywords'),
                                    'description'   => Request::post('gallery_description'),
                                    'limit'         => (int)Request::post('gallery_limit'),
                                    'w'             => (int)Request::post('width_thumb'),
                                    'h'             => (int)Request::post('height_thumb'),
                                    'wmax'          => (int)Request::post('width_orig'),
                                    'hmax'          => (int)Request::post('height_orig'),
                                    'resize'        => (string)Request::post('resize'),
                                    'sections'      => $sections,
                                    'parent'        => 0
                                );

                                if(GalleryAdmin::$folder->insert($data)) {

                                    $last_id = GalleryAdmin::$folder->lastId();

                                    Notification::set('success', __('New album <i>:album</i> have been added.', 'gallery', array(':album' => Security::safeName($data['title'], '-', true))));
                                }

                                Action::run('admin_album_action_add');

                                if (Request::post('add_album')) {
                                    Request::redirect('index.php?id=gallery&action=edit&gallery_id='.$last_id);
                                } else {
                                    Request::redirect('index.php?id=gallery');
                                }
                            }
                        } else { die('csrf detected!'); }
                    }

                    $post['slug']          = '';
                    $post['title']         = '';
                    $post['keywords']      = '';
                    $post['description']   = '';
                    $post['w']             = Option::get('gallery_w');
                    $post['h']             = Option::get('gallery_h');
                    $post['wmax']          = Option::get('gallery_wmax');
                    $post['hmax']          = Option::get('gallery_hmax');
                    $post['limit']         = Option::get('gallery_limit');
                    $post['resize']        = Option::get('gallery_resize');
                    $post['sections']      = '0';

                    View::factory('gallery/views/backend/add')
                        ->assign('post', $post)
                        ->assign('errors', $errors)
                        ->display();
                    break;

                case "edit":
                    if(Request::get('gallery_id')) {

                        if (Request::post('save_album') || Request::post('save_album_and_exit')) {
                            if (Security::check(Request::post('csrf'))) {
                                if (count($errors) == 0) {

                                    $sections = Request::post('gallery_sections') == null ? '0' : '1';
                                    $data = array(
                                        'title'         => Request::post('gallery_title'),
                                        'slug'          => Security::safeName(Request::post('gallery_slug'), '-', true),
                                        'keywords'      => Request::post('gallery_keywords'),
                                        'description'   => Request::post('gallery_description'),
                                        'limit'         => (int)Request::post('gallery_limit'),
                                        'w'             => (int)Request::post('width_thumb'),
                                        'h'             => (int)Request::post('height_thumb'),
                                        'wmax'          => (int)Request::post('width_orig'),
                                        'hmax'          => (int)Request::post('height_orig'),
                                        'resize'        => (string)Request::post('resize'),
                                        'sections'      => $sections,
                                        'parent'        => 0
                                    );

                                    $id = (int)Request::post('gallery_id');

                                    if(GalleryAdmin::$folder->updateWhere('[id='.$id.']', $data)) {
                                        Notification::set('success', __('Your changes to the album <i>:album</i> have been saved.', 'gallery', array(':album' => Security::safeName(Request::post('gallery_title'), '-', true))));
                                    }

                                    Action::run('admin_album_action_edit');

                                    if (Request::post('save_album_and_exit')) {
                                        Request::redirect('index.php?id=gallery');
                                    } else {
                                        Request::redirect('index.php?id=gallery&action=edit&gallery_id='.$id);
                                    }
                                }
                            } else { die('csrf detected!'); }
                        }

                        $id = (int)Request::get('gallery_id');
                        $data = GalleryAdmin::$folder->select('[id="'.$id.'"]', null);

                        if (Request::post('reload_album')) {
                            $items = GalleryAdmin::$items->select('[guid="'.$id.'"]', 'all', null, array('id'));

                            if (count($items) > 0)
                            {
                                foreach($items as $item)
                                {
                                    GalleryAdmin::ReSize($dir.$item['id'].'.jpg', $dir.'thumbnail'.DS.$item['id'].'.jpg', $data);
                                }
                                Notification::set('success', __('Resize image to the album <i>:album</i> success.', 'gallery', array(':album' => $data['title'])));
                            }
                        }

                        if($data) {
                            $post['slug']          = (Request::post('gallery_slug'))            ? Request::post('gallery_slug')             : $data['slug'];
                            $post['title']         = (Request::post('gallery_title'))           ? Request::post('gallery_title')            : $data['title'];
                            $post['keywords']      = (Request::post('gallery_keywords'))        ? Request::post('gallery_keywords')         : $data['keywords'];
                            $post['description']   = (Request::post('gallery_description'))     ? Request::post('gallery_description')      : $data['description'];
                            $post['limit']         = (Request::post('gallery_limit'))           ? Request::post('gallery_limit')            : $data['limit'];
                            $post['sections']      = (Request::post('gallery_sections'))        ? Request::post('gallery_sections')         : $data['sections'];
                            $post['w']             = ((int)Request::post('width_thumb'))        ? (int)Request::post('width_thumb')         : $data['w'];
                            $post['h']             = ((int)Request::post('height_thumb'))       ? (int)Request::post('width_thumb')         : $data['h'];
                            $post['wmax']          = ((int)Request::post('width_orig'))         ? (int)Request::post('width_thumb')         : $data['wmax'];
                            $post['hmax']          = ((int)Request::post('height_orig'))        ? (int)Request::post('width_thumb')         : $data['hmax'];
                            $post['resize']        = ((int)Request::post('resize'))             ? (int)Request::post('resize')              : $data['resize'];

                            View::factory('gallery/views/backend/edit')
                                ->assign('gallery_id', $id)
                                ->assign('post', $post)
                                ->assign('errors', $errors)
                                ->display();
                        }
                    }
                    break;

                case "delete":
                    if (Request::get('gallery_id')) {
                        if (Security::check(Request::get('token'))) {
                            $id = (int)Request::get('gallery_id');

                            $item = GalleryAdmin::$items->select('[guid='.$id.']');
                            $data = GalleryAdmin::$folder->select('[id='.$id.']', null);

                            if (GalleryAdmin::$folder->deleteWhere('[id='.$id.']')) {
                                if ($item != null)
                                {
                                    foreach($item as $row)
                                    {
                                        if (GalleryAdmin::$items->deleteWhere('[id='.$row['id'].']')) {
                                            File::delete($dir.$row['id'].'.jpg');
                                            File::delete($dir. 'thumbnail'. DS .$row['id'].'.jpg');
                                        }
                                    }
                                }
                                Notification::set('success', __('Album <i>:album</i> deleted', 'gallery',
                                    array(':album' => Html::toText($data['title']))));
                            }

                            Action::run('admin_gallery_action_delete_album');
                            Request::redirect('index.php?id=gallery');

                        } else { die('csrf detected!'); }
                    }

                    if (is_array(Request::post('items'))) {
                        if (Security::check(Request::post('token'))) {

                            foreach(Request::post('items') as $row)
                            {
                                if (GalleryAdmin::$items->deleteWhere('[id='.$row.']')) {
                                    File::delete($dir.$row.'.jpg');
                                    File::delete($dir. 'thumbnail'. DS .$row.'.jpg');
                                }
                            }
                        } else { die('csrf detected!'); }
                    }
                    break;
            }

        } else {
            $limit = Option::get('gallery_limit_admin');
            $records_all = GalleryAdmin::$folder->select(null, 'all', null, array('title', 'slug'));
            $count_gallery = count($records_all);
            $pages = ceil($count_gallery/$limit);

            $page = (Request::get('page')) ? (int)Request::get('page') : 1;
//			$sort = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
//			$order = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';

            if ($page < 1) { $page = 1; }
            elseif ($page > $pages) { $page = $pages; }

            $start = ($page-1)*$limit;

//			$records_sort = Arr::subvalSort($records_all, $sort, $order);
            $records_sort = $records_all;
            if($count_gallery>0) $records = array_slice($records_sort, $start, $limit);
            else $records = array();

            View::factory('gallery/views/backend/index')
                ->assign('gallery_list', $records)
                ->assign('site_url', $site_url)
                ->assign('current_page', $page)
                ->assign('pages_count', $pages)
//				->assign('sort', $sort)
//				->assign('order', $order)
                ->display();
        }
    }

    /**
     *  Ajax save
     */
        public static function ajaxSave() {
            GalleryAdmin::$items = new Table('gal_items');
            GalleryAdmin::$folder = new Table('gal_folder');

            $gid = (int)Request::get('guid');
            $dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;

            // save settings
            if (Request::get('upload') == 'files') {
                require(ROOT . DS . 'plugins' . DS . 'dev' . DS . 'UploadHandler.php');
                $site_url = Option::get('siteurl');
                $url = $site_url . 'public/uploads/gallery/';
                $opt = array(
                    'upload_dir' => $dir,
                    'upload_url' => $url
                );

                $upload_handler = new UploadHandler($opt);
                exit();
            }
            if (Request::get('rename')) {
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

                $data = array(
                    'title'        => date("Ymd_His"),
                    'description'  => '',
                    'date'         => time(),
                    'author'       => $author,
                    'guid'         => $gid
                );

                $folder = GalleryAdmin::$folder->select('[id='.$gid.']', null);

                if(GalleryAdmin::$items->insert($data)) {

                    $last_id = GalleryAdmin::$items->lastId();
                    $file = Request::post('file');
                    $type = Request::post('type');

                    switch($type)
                    {
                        case "image/jpeg":
                            $type = ".jpg";
                        break;

                        case "image/png":
                            $type = ".jpg";
                        break;

                        case "image/gif":
                            $type = ".jpg";
                        break;
                    }

                    File::rename($dir.$file, $dir.$last_id.$type);
                    File::rename($dir . 'thumbnail'. DS .$file, $dir . 'thumbnail'. DS .$last_id.$type);

                    GalleryAdmin::ReSize($dir.$last_id.$type, $dir.'thumbnail'.DS.$last_id.$type, $folder);
                }

                $data = GalleryAdmin::$folder->select('[id="'.$gid.'"]', null);
                Notification::set('success', __('Your items to <i>:album</i> have been added.', 'gallery', array(':album' => $data['title'])));
                exit();
            }
            if (Request::get('image') == 'edit') {
                $item = GalleryAdmin::$items->select('[id="'.$gid.'"]', 'all', null, array('title', 'description'));

                $json_data = array ('id'=>$gid,'title'=>$item[0]['title'],'desc'=>$item[0]['description']);
                echo json_encode($json_data);
                exit();
            }
        }

     /**
     * Form Component Save
     */
    public static function formComponentSave() {
        if (Request::post('gallery_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('gallery_template', Request::post('gallery_form_template'));
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
                Form::label('gallery_form_template', __('Gallery template', 'gallery')).
                Form::select('gallery_form_template', $templates, Option::get('gallery_template')).
                Html::br().
                Form::submit('gallery_component_save', __('Save', 'gallery'), array('class' => 'btn')).
                Form::close()
        );
    }

    private static function ReSize($file, $thumb, $folder)
    {
        $img  = Image::factory($file);

        $wmax   = (int)$folder['wmax'];
        $hmax   = (int)$folder['hmax'];
        $width  = (int)$folder['w'];
        $height = (int)$folder['h'];
        $resize = $folder['resize'];
        $ratio = $width/$height;

        if ($img->width > $wmax or $img->height > $hmax) {
            if ($img->height > $img->width) {
                $img->resize($wmax, $hmax, Image::HEIGHT);
            } else {
                $img->resize($wmax, $hmax, Image::WIDTH);
            }
        }
        $img->save($file);

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
        $img->save($thumb);
    }
}