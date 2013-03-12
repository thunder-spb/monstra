<?php

Navigation::add(__('Gallery', 'gallery'), 'content', 'gallery', 10);

Action::add('admin_themes_extra_index_template_actions','GalleryAdmin::formComponent');
Action::add('admin_themes_extra_actions','GalleryAdmin::formComponentSave');
Action::add('admin_pre_render','GalleryAdmin::ajaxSave');

class GalleryAdmin extends Backend {
    public static $folder = null; // gallery table @object
    public static $items = null; // gallery table @object
    public static $resize = null;
    public static $media = null;
    public static $opt = array();

    public static function main() {

        GalleryAdmin::$opt['site_url'] = Option::get('siteurl');
        $errors = array();

        GalleryAdmin::$resize = array(
            'width'   => __('Respect to the width', 'gallery'),
            'height'  => __('Respect to the height', 'gallery'),
            'crop'    => __('Similarly, cutting unnecessary', 'gallery'),
            'stretch' => __('Similarly with the expansion', 'gallery'),
        );

        GalleryAdmin::$media = array(
            ''              => 'Image',
            'youtube'       => 'Youtube',
            'vimeo'         => 'Vimeo',
            'metacafe'      => 'Metacafe',
            'dailymotion'   => 'Dailymotion',
            'twitvid'       => 'Twitvid',
            'twitpic'       => 'Twitpic',
            'instagram'     => 'Instagram',
        );

        GalleryAdmin::$items = new Table('gal_items');
        GalleryAdmin::$folder = new Table('gal_folder');

        GalleryAdmin::$opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
        GalleryAdmin::$opt['url'] = GalleryAdmin::$opt['site_url'] . 'public/uploads/gallery/';

        /**
         *  Upload image
         */
        if (Request::post('upload_file')) {
            if (Security::check(Request::post('csrf'))) {
                $uid = (int)Request::post('id');
                if ($_FILES['file']) {
                    if($_FILES['file']['type'] == 'image/jpeg' ||
                        $_FILES['file']['type'] == 'image/png' ||
                        $_FILES['file']['type'] == 'image/gif') {

                        $img  = Image::factory($_FILES['file']['tmp_name']);
                        $options = GalleryAdmin::$folder->select('[id='.$uid.']', null);
                        DevAdmin::ReSize($img, GalleryAdmin::$opt['dir'], 'album_'.$uid.'.jpg', $options);
                    }
                }
                Request::redirect('index.php?id=gallery&action=edit&gallery_id='.$uid);
            } else { die('csrf detected!'); }
        }

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

                            File::setContent(STORAGE . DS . 'gallery' . DS .'album.0.txt', XML::safe(Request::post('editor')));

                            Notification::set('success', __('Your changes have been saved', 'gallery'));

                            Request::redirect('index.php?id=gallery');
                        } else { die('csrf detected!'); }
                    }

                    View::factory('gallery/views/backend/settings')->display();
                    break;

                case "items":
                    if (Request::post('gallery_save_image')) {
                        if (Security::check(Request::post('csrf'))) {
                            $data = array(
                                'title'         => Request::post('gallery_title'),
                                'description'   => Request::post('gallery_desc'),
                                'media'         => Request::post('gallery_media')
                            );

                            $id = (int)Request::post('uid');
                            $gid = Request::post('gid');

                            if ($id == 0){
                                $data = array(
                                    'title'         => Request::post('gallery_title'),
                                    'description'   => Request::post('gallery_desc'),
                                    'media'         => Request::post('gallery_media'),
                                    'date'          => time(),
                                    'author'        => Session::get('user_login'),
                                    'guid'          => $gid
                                );

                                if(GalleryAdmin::$items->insert($data)) {
                                    Notification::set('success', __('Your item <i>:item</i> have been added.', 'gallery', array(':item' => Request::post('gallery_title'))));
                                }
                                else {
                                    Notification::set('success', __('Your item <i>:item</i> have been not added.', 'gallery', array(':item' => Request::post('gallery_title'))));
                                }
                            }
                            else {
                                if(GalleryAdmin::$items->updateWhere('[id='.$id.']', $data)) {
                                    GalleryAdmin::SetMedia(Request::post('gallery_media'), $gid, $id);

                                    Notification::set('success', __('Your changes to the album <i>:album</i> have been saved.', 'gallery', array(':album' => Request::post('gallery_title', '-', true))));
                                }
                                else {
                                    Notification::set('error', __('Your changes to the album <i>:album</i> have been not saved.', 'gallery', array(':album' => Request::post('gallery_title', '-', true))));
                                }
                            }

                            Request::redirect('index.php?id=gallery&action=items&gallery_id='.$gid);
                        } else { die('csrf detected!'); }
                    }

                    $limit = Option::get('gallery_limit_admin');
                    $id = (int)Request::get('gallery_id');
                    $records_all = GalleryAdmin::$items->select('[guid="'.$id.'"]', 'all', null, array('title', 'description', 'date', 'author'));
                    $album = GalleryAdmin::$folder->select('[id='.$id.']', null);
                    GalleryAdmin::$opt['id'] = $album['id'];
                    GalleryAdmin::$opt['title'] = $album['title'];
                    $cnt_all = count($records_all);
                    GalleryAdmin::$opt['pages'] = ceil($cnt_all/$limit);

                    GalleryAdmin::$opt['page'] = (Request::get('page')) ? (int)Request::get('page') : 1;
                    GalleryAdmin::$opt['sort'] = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
                    GalleryAdmin::$opt['order'] = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';

                    if (GalleryAdmin::$opt['page'] < 1) { GalleryAdmin::$opt['page'] = 1; }
                    elseif (GalleryAdmin::$opt['page'] > GalleryAdmin::$opt['pages']) { GalleryAdmin::$opt['page'] = GalleryAdmin::$opt['pages']; }

                    $start = (GalleryAdmin::$opt['page']-1)*$limit;

                    $records_sort = Arr::subvalSort($records_all, GalleryAdmin::$opt['sort'], GalleryAdmin::$opt['order']);
                    if($cnt_all>0) $records = array_slice($records_sort, $start, $limit);
                    else $records = array();

                    View::factory('gallery/views/backend/items')
                        ->assign('items', $records)
                        ->assign('opt', GalleryAdmin::$opt)
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
                                    File::setContent(STORAGE . DS . 'gallery' . DS .'album.'.$last_id.'.txt', XML::safe(Request::post('editor')));

                                    Notification::set('success', __('New album <i>:album</i> have been added.', 'gallery', array(':album' => Security::safeName($data['title'], '-', true))));
                                }

                                if (Request::post('add_album')) {
                                    Request::redirect('index.php?id=gallery&action=edit&gallery_id='.$last_id);
                                } else {
                                    Request::redirect('index.php?id=gallery');
                                }
                            }
                        } else { die('csrf detected!'); }
                    }

                    $post['slug']          = (Request::post('gallery_slug'))            ? Request::post('gallery_slug')             : '';
                    $post['title']         = (Request::post('gallery_title'))           ? Request::post('gallery_title')            : '';
                    $post['keywords']      = (Request::post('gallery_keywords'))        ? Request::post('gallery_keywords')         : '';
                    $post['description']   = (Request::post('gallery_description'))     ? Request::post('gallery_description')      : '';
                    $post['limit']         = (Request::post('gallery_limit'))           ? Request::post('gallery_limit')            : Option::get('gallery_limit');
                    $post['sections']      = (Request::post('gallery_sections'))        ? Request::post('gallery_sections')         : '0';
                    $post['w']             = ((int)Request::post('width_thumb'))        ? (int)Request::post('width_thumb')         : Option::get('gallery_w');
                    $post['h']             = ((int)Request::post('height_thumb'))       ? (int)Request::post('width_thumb')         : Option::get('gallery_h');
                    $post['wmax']          = ((int)Request::post('width_orig'))         ? (int)Request::post('width_thumb')         : Option::get('gallery_wmax');
                    $post['hmax']          = ((int)Request::post('height_orig'))        ? (int)Request::post('width_thumb')         : Option::get('gallery_hmax');
                    $post['resize']        = ((int)Request::post('resize'))             ? (int)Request::post('resize')              : Option::get('gallery_resize');

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

                                    $id = (int)Request::get('gallery_id');

                                    if(GalleryAdmin::$folder->updateWhere('[id='.$id.']', $data)) {
                                        File::setContent(STORAGE . DS . 'gallery' . DS .'album.'.$id.'.txt', XML::safe(Request::post('editor')));

                                        Notification::set('success', __('Your changes to the album <i>:album</i> have been saved.', 'gallery', array(':album' => Security::safeName(Request::post('gallery_title'), '-', true))));
                                    }

                                    if (Request::post('save_album_and_exit')) {
                                        Request::redirect('index.php?id=gallery');
                                    } else {
                                        Request::redirect('index.php?id=gallery&action=edit&gallery_id='.$id);
                                    }
                                }
                            } else { die('csrf detected!'); }
                        }

                        $post['id'] = $id = (int)Request::get('gallery_id');
                        $data = GalleryAdmin::$folder->select('[id="'.$post['id'].'"]', null);

                        if (Request::post('reload_album')) {
                            $pic = GalleryAdmin::$items->select('[guid="'.$post['id'].'"]', 'all', null, array('id'));

                            if (count($pic) > 0)
                            {
                                $album = GalleryAdmin::$folder->select('[id="'.Request::post('gallery_id').'"]', null);
                                foreach($pic as $item)
                                {
                                    if (File::exists(GalleryAdmin::$opt['dir'].$item['id'].'.jpg')){
                                        $img = Image::factory(GalleryAdmin::$opt['dir'].$item['id'].'.jpg');
                                        DevAdmin::ReSize($img, GalleryAdmin::$opt['dir'], $item['id'].'.jpg', $album);
                                    }
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
                                ->assign('post', $post)
                                ->assign('opt', GalleryAdmin::$opt)
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
                                            File::delete(GalleryAdmin::$opt['dir'].$row['id'].'.jpg');
                                            File::delete(GalleryAdmin::$opt['dir']. 'thumbnail'. DS .$row['id'].'.jpg');
                                        }
                                    }
                                }
                                Notification::set('success', __('Album <i>:album</i> deleted', 'gallery',
                                    array(':album' => Html::toText($data['title']))));
                            }

                            Request::redirect('index.php?id=gallery');

                        } else { die('csrf detected!'); }
                    }
                    die('no action');
                    break;
            }

        } else {
            $limit = Option::get('gallery_limit_admin');
            $records_all = GalleryAdmin::$folder->select(null, 'all', null, array('title', 'slug'));
            $count_gallery = count($records_all);
            GalleryAdmin::$opt['pages'] = ceil($count_gallery/$limit);

            GalleryAdmin::$opt['page'] = (Request::get('page')) ? (int)Request::get('page') : 1;
//			$opt['sort'] = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
//			$opt['order'] = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';

            if (GalleryAdmin::$opt['page'] < 1) { GalleryAdmin::$opt['page'] = 1; }
            elseif (GalleryAdmin::$opt['page'] > GalleryAdmin::$opt['pages']) { GalleryAdmin::$opt['page'] = GalleryAdmin::$opt['pages']; }

            $start = (GalleryAdmin::$opt['page']-1)*$limit;

//			$records_sort = Arr::subvalSort($records_all, $opt['sort'], $opt['order']);
            $records_sort = $records_all;
            if($count_gallery>0) $records = array_slice($records_sort, $start, $limit);
            else $records = array();

            View::factory('gallery/views/backend/index')
                ->assign('items', $records)
                ->assign('opt', GalleryAdmin::$opt)
//				->assign('sort', $opt['sort'])
//				->assign('order', $opt['order'])
                ->display();
        }
    }

    /**
     *  Ajax save
     */
        public static function ajaxSave() {
            $items = new Table('gal_items');
            $folder = new Table('gal_folder');

            $gid = (int)Request::get('guid');
            $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;

            // save settings
            if (Request::get('upload') == 'files') {
                require(ROOT . DS . 'plugins' . DS . 'dev' . DS . 'UploadHandler.php');
                $opt['site_url'] = Option::get('siteurl');
                $opt['url'] = $opt['site_url'] . 'public/uploads/gallery/';
                $opt = array(
                    'upload_dir' => $opt['dir'],
                    'upload_url' => $opt['url']
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
                    'media'        => '',
                    'date'         => time(),
                    'author'       => $author,
                    'guid'         => $gid
                );

                $options = $folder->select('[id='.$gid.']', null);

                if($items->insert($data)) {

                    $last_id = $items->lastId();
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

                    File::rename($opt['dir'].$file, $opt['dir'].$last_id.$type);
                    File::rename($opt['dir'] . 'thumbnail'. DS .$file, $opt['dir'] . 'thumbnail'. DS .$last_id.$type);

                    $img = Image::factory($opt['dir'].$last_id.$type);

                    DevAdmin::ReSize($img, $opt['dir'], $last_id.$type, $options);
                }

                $data = $folder->select('[id="'.$gid.'"]', null);
                Notification::set('success', __('Your items to <i>:album</i> have been added.', 'gallery', array(':album' => $data['title'])));
                exit();
            }

            if (Request::get('image') == 'edit') {
                $item = $items->select('[id="'.$gid.'"]', 'all', null, array('title', 'description', 'media'));

                $json_data = array ('id'=>$gid,'title'=>$item[0]['title'],'desc'=>$item[0]['description'],'media'=>$item[0]['media']);
                echo json_encode($json_data);
                exit();
            }

            if (Request::get('delete'))
            {
                if (is_array(Request::post('items'))) {
                    if (Security::check(Request::post('token'))) {

                        foreach(Request::post('items') as $row)
                        {
                            if ($items->deleteWhere('[id='.$row.']')) {
                                File::delete($opt['dir'].$row.'.jpg');
                                File::delete($opt['dir']. 'thumbnail'. DS .$row.'.jpg');
                            }
                        }
                        die('test');
                    } else { die('csrf detected!'); }
                }
                exit('no action');
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

    private static function SetMedia($media, $gid, $id)
    {
        $url = parse_url($media);
        $img_url = '';

        switch($url['host'])
        {
            case 'www.youtube.com':
                $img_url = GalleryAdmin::youtube($media);
                break;
            case 'youtube.com':
                $img_url = GalleryAdmin::youtube($media);
                break;

            case 'www.vimeo.com':
                $img_url = GalleryAdmin::vimeo($media);
                break;
            case 'vimeo.com':
                $img_url = GalleryAdmin::vimeo($media);
                break;

            case 'www.metacafe.com':
                $img_url = GalleryAdmin::metacafe($media);
                break;
            case 'metacafe.com':
                $img_url = GalleryAdmin::metacafe($media);
                break;

            case 'www.dailymotion.com':
                $img_url = GalleryAdmin::dailymotion($media);
                break;
            case 'dailymotion.com':
                $img_url = GalleryAdmin::dailymotion($media);
                break;

            case 'www.twitpic.com':
                $img_url = GalleryAdmin::twitpic($media);
                break;
            case 'twitpic.com':
                $img_url = GalleryAdmin::twitpic($media);
                break;

            case 'www.instagr.am':
                $img_url = GalleryAdmin::instagram($media);
                break;
            case 'instagr.am':
                $img_url = GalleryAdmin::instagram($media);
                break;
        }

        if ($img_url != '')
        {
            $file = file_get_contents($img_url);
            File::setContent(STORAGE . DS . 'gallery' . DS .'test.jpg', $file);
            $img = Image::factory(STORAGE . DS . 'gallery' . DS .'test.jpg', $file);
            $options = GalleryAdmin::$folder->select('[id='.$gid.']', null);
            DevAdmin::ReSize($img, GalleryAdmin::$opt['dir'], $id.'.jpg', $options);
        }
    }

    private static function youtube($media)
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $media, $matches);
        if ($matches[0] != '')
            return 'http://img.youtube.com/vi/'.$matches[0].'/0.jpg';
        return '';
    }

    private static function vimeo($media)
    {
        preg_match('#http://(?:\w+.)?vimeo.com/(?:video/|moogaloop\.swf\?clip_id=|)(\w+)#i', $media, $matches);
        if ($matches[1] != '')
        {
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$matches[1].php"));
            return $hash[0]['thumbnail_medium'];
        }
        return '';
    }

    private static function metacafe($media)
    {
        preg_match('#http://(?:www\.)?metacafe.com/(?:watch|fplayer)/(\w+)/#i', $media, $matches);
        if ($matches[1] != '')
            return 'http://www.metacafe.com/thumb/'.$matches[1].'.jpg';
        return '';
    }

    private static function dailymotion($media)
    {
        preg_match('#http://(?:\w+.)?dailymotion.com/video/([A-Za-z0-9]+)#s', $media, $matches);
        if ($matches[1] != '')
        {
            $hash = json_decode(file_get_contents("https://api.dailymotion.com/video/$matches[1]?fields=thumbnail_large_url"));
            return $hash->thumbnail_large_url;
        }
        return '';
    }

    private static function twitpic($media)
    {
        preg_match('#http://(?:\w+.)?twitpic.com/([A-Za-z0-9]+)#i', $media, $matches);
        if ($matches[1] != '')
            return 'http://twitpic.com/show/thumb/'.$matches[1];
        return '';
    }

    private static function instagram($media)
    {
        $hash = json_decode(file_get_contents("http://api.instagram.com/oembed?url=".$media));
        return $hash->url;
    }
}