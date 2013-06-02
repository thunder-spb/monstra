<?php

/**
 *  Gallery plugin
 *
 *  @package Monstra
 *  @subpackage Plugins
 *  @copyright Copyright (C) KANekT @ http://kanekt.ru
 *  @license http://creativecommons.org/licenses/by-nc/3.0/
 *  Creative Commons Attribution-NonCommercial 3.0
 *  Donate Web Money Z104136428007 R346491122688
 *  Yandex Money 410011782214621
 *
 */


// Register plugin
Plugin::register( __FILE__,
    __('Gallery', 'gallery'),
    __('Gallery plugin for Monstra', 'gallery'),
    '1.5.0',
    'KANekT',
    'http://kanekt.ru/',
    'gallery');


// Load Sandbox Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {

    Plugin::admin('gallery');

}

Javascript::add('plugins/gallery/js/back.js', 'backend', 18);
Javascript::add('plugins/gallery/js/front.js', 'frontend', 18);

/*
 * Register for Developer Helper
 */
Registry::set('dev_valid_backend', 1);
Registry::set('dev_file_upload', 1);
Registry::set('dev_fancy_frontend', 1);
Registry::set('dev_bootstrap_file_upload', 1);
Registry::set('dev_migrate_frontend', 1);
Registry::set('dev_responsiveslides', 1);

Shortcode::add('gallery', 'Gallery::_shortcode');
/**
 * Sandbox simple class
 */
class Gallery extends Frontend {

    public static $gallery = null; // gallery table @object
    public static $folder = null; // gallery table @object
    public static $items = null; // gallery table @object
    public static $meta = null; // meta tags gallery @array
    public static $template = ''; // gallery template content @string

    public static $sort = 'date';
    public static $order = 'DESC';
    public static $opt = array();

    public static function main(){
        Gallery::$items = new Table('gal_items');
        Gallery::$folder = new Table('gal_folder');
        $uri = Uri::segments();
        Gallery::$opt['site_url'] = Option::get('siteurl');
        Gallery::$opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
        Gallery::$opt['url'] = Option::get('siteurl') . 'public/uploads/gallery/';

        if (Request::get('slug') && $uri[0] == 'gallery') {
            $images = Gallery::getList(Request::get('slug'),Request::get('page'), false);
            $pages = Gallery::paginator_ajax(Request::get('page'),Request::get('pages'),Request::get('slug'),Request::get('sort'),Request::get('order'));
            $json_data = array ('pages'=>$pages,'images'=>$images);
            echo json_encode($json_data);
            exit();
        }
        else if($uri[0] == 'gallery' && isset($uri[1])) {
            if (isset($uri[2])){
                Gallery::viewItem($uri[2],$uri[1]);
            } else {
                Gallery::viewGallery($uri[1]);
            }
        }
        else if($uri[0] == 'gallery' && !isset($uri[1])) {
            Gallery::viewIndex();
        }
        else {
            Gallery::error404();
        }
    }

    /**
     * Shortcode gallery
    */
    public static function _shortcode($attributes) {
        extract($attributes);
        Gallery::$opt['site_url'] = Option::get('siteurl');
        Gallery::$opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
        Gallery::$opt['url'] = Option::get('siteurl') . 'public/uploads/gallery/';

        Gallery::$sort = (isset($sort)) ? $sort : 'date';
        $count = (isset($count)) ? $count : 5;
        Gallery::$order = (isset($order)) ? strtoupper($order) : 'DESC';
        Gallery::$items = new Table('gal_items');
        Gallery::$folder = new Table('gal_folder');

        if (isset($list) && (isset($slug))) {
            switch ($list) {
                case 'album':
                    return '<div name=image>'.Gallery::viewGallery($slug, false).'</div>';
                case 'slider':
                    return Gallery::viewSlider($slug, false);
                case 'top':
                    return '<div name=image>'.Gallery::viewTop($slug, $count).'</div>';
            }
        }
        if (isset($list)) {
            switch ($list) {
                case 'last':
                    return '<div name=image>'.Gallery::viewLast($count, '', false).'</div>';
            }
        }
        return '';
    }

    /**
     * <?php echo Gallery::getGallery('test', 5); ?>
     */
    public static function getGallery($slug, $limit=5){
        Gallery::$items = new Table('gal_items');
        Gallery::$folder = new Table('gal_folder');
        Gallery::$opt['site_url'] = Option::get('siteurl');
        Gallery::$opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
        Gallery::$opt['url'] = Option::get('siteurl') . 'public/uploads/gallery/';

        return '<div name=image>'.Gallery::viewTop($slug, $limit).'</div>';
    }

    /**
     * get Get Gallery by Slug
     * {gallery list="album" slug="test" sort="date" order="DESC"}
     */
    private static function viewGallery($slug, $display = true){

        $page = (int)Request::get('page');
        if ($page == 0)
            $page = 1;
        $images = Gallery::getList($slug,$page,$display);

        if ($display)
        {
            Gallery::$template = $images;
        }
        else
        {
            return $images;
        }
        return '';
    }

    /**
     * <?php echo Gallery::Last(5); ?>
     */	
    public static function Last($count, $album = '')
    {
		Gallery::$items = new Table('gal_items');
		Gallery::$opt['site_url'] = Option::get('siteurl');
		Gallery::$opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
		Gallery::$opt['url'] = Option::get('siteurl') . 'public/uploads/gallery/';
		return Gallery::viewLast($count, $album);
	}	
	
    /**
     * {gallery list="last" count=5}
     */
    private static function viewLast($count, $album, $display = true)
    {
        if ($album == '')
        {
            $records = Gallery::$items->select(null, 'all');
        }
        else
        {
            $records = Gallery::$items->select('[guid="'.$album.'"]', null);
        }
        $records_sort = Arr::subvalSort($records, 'id', 'DESC');

        if(count($records_sort)>0) {
            if($count == 0) {
                $count = 5;
            }

            $records = array_slice($records_sort, 0, $count);

            if(count($records)>0) {

                $output = View::factory('gallery/views/frontend/last')
                    ->assign('records', $records)
                    ->assign('opt', Gallery::$opt)
                    ->render();

                if ($display){
                    return $output;
                }
                else{
                    echo $output;
                }
            }
        }
    }

    /**
     * {gallery list="top" count=5}
     */
    private static function viewTop($slug, $limit=5)
    {
        Gallery::$meta = Gallery::$folder->select('[slug="'.$slug.'"]', null);
        if (isset(Gallery::$meta["id"]))
        {
            $id = Gallery::$meta["id"];

            Gallery::$opt['slug'] = $slug;
            Gallery::$opt['title'] = Gallery::$meta['title'];
            Gallery::$opt['id'] = $id;

            $records = Gallery::$items->select('[guid="'.$id.'"]', 'all', null, array('id','title','description','date','media'));
            $records_sort = Arr::subvalSort($records, Gallery::$sort, Gallery::$order);

            $records = array_slice($records_sort, 0, $limit);

                $output = View::factory('gallery/views/frontend/images')
                    ->assign('records', $records)
                    ->assign('opt', Gallery::$opt)
                    ->render();

                return $output;
        }
        return '';
    }

    private static function getList($slug, $page, $view = true)
    {
        Gallery::$meta = Gallery::$folder->select('[slug="'.$slug.'"]', null);
        if (isset(Gallery::$meta["id"]))
        {
            $id = Gallery::$meta["id"];
            $limit    = Gallery::$meta['limit'];

            Gallery::$opt['slug'] = $slug;
            Gallery::$opt['title'] = Gallery::$meta['title'];
            Gallery::$opt['id'] = $id;

            $records_all = Gallery::$items->select('[guid="'.$id.'"]', 'all', null, array('id','title','description','date','media'));

            $count_items = count($records_all);

            Gallery::$opt['pages'] = ceil($count_items/$limit);
            Gallery::$opt['page'] = $page;

            if(Gallery::$opt['page'] < 1 or Gallery::$opt['page'] > Gallery::$opt['pages']) {
                Gallery::error404();
            } else {

                $start = (Gallery::$opt['page']-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, Gallery::$sort, Gallery::$order);

                if($count_items > 0) $records = array_slice($records_sort, $start, $limit);
                else $records = array();

                if ($view)
                {
                    $output = View::factory('gallery/views/frontend/images')
                        ->assign('records', $records)
                        ->assign('opt', Gallery::$opt)
                        ->render();

                    return $output.Dev::paginator(Gallery::$opt['page'],Gallery::$opt['pages'],$slug.'?page=');
                }
                else
                {
                    $opt{'slug'} = $slug;
                    $output = View::factory('gallery/views/frontend/short')
                        ->assign('items', $records)
                        ->assign('opt', Gallery::$opt)
                        ->render();
                    return $output;
                }
            }
        }
        return '';
    }

    private static function viewIndex()
    {
        $records = Gallery::$folder->select(null, 'all');
        $output = View::factory('gallery/views/frontend/index')
            ->assign('records', $records)
            ->assign('opt', Gallery::$opt)
            ->render();

        Gallery::$meta['title'] = __('Gallery', 'gallery');
        Gallery::$meta['keywords'] = __('Gallery keywords', 'gallery');
        Gallery::$meta['description'] = __('Gallery description', 'gallery');
        Gallery::$template = $output;
    }

    private static function viewItem($id, $slug)
    {
        Gallery::$opt['gallery'] = Gallery::$folder->select('[slug="'.$slug.'"]', null);
        Gallery::$meta = Gallery::$items->select('[id="'.$id.'"]',null);
        Gallery::$meta['keywords'] = '';
        Gallery::$meta['hits'] = Gallery::hits(Gallery::$meta['id'], Gallery::$meta['hits']);

        $output = View::factory('gallery/views/frontend/item')
            ->assign('item', Gallery::$meta)
            ->assign('opt', Gallery::$opt)
            ->render();

        Gallery::$template = $output;
    }

    /*
     * echo Gallery::Slider('test');
     */
    public static function Slider($slug)
    {
        Gallery::$items = new Table('gal_items');
        Gallery::$folder = new Table('gal_folder');
        Gallery::$opt['site_url'] = Option::get('siteurl');
        Gallery::$opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
        Gallery::$opt['url'] = Option::get('siteurl') . 'public/uploads/gallery/';
        return Gallery::viewSlider($slug);
    }

    /*
     * {gallery list="slider" slug="test"}
     */
    private static function viewSlider($slug, $display=true)
    {
        $item = Gallery::$folder->select('[slug="'.$slug.'"]', null);
        if (isset($item["id"]))
        {
            $id = $item["id"];

            $records = Gallery::$items->select('[guid="'.$id.'"]', 'all', null, array('id','title','description'));
            $images = View::factory('gallery/views/frontend/slider')
                ->assign('items', $records)
                ->assign('opt', Gallery::$opt);

            if ($display)
            {
                return $images->render();
            }
            else
            {
                $images->display();
            }
        }
        return '';
    }

    public static function title(){
        return Gallery::$meta['title'];
    }

    public static function keywords(){
        return Gallery::$meta['keywords'];
    }

    public static function description(){
        return Gallery::$meta['description'];
    }

    public static function content(){
        return Gallery::$template;
    }

    public static function template() {
        return Option::get('gallery_template');
    }

    public static function error404() {
        if (BACKEND == false) {
            Gallery::$template = Text::toHtml(File::getContent(STORAGE . DS . 'pages' . DS . '1.page.txt'));
            Gallery::$meta['title'] = 'error404';
            Gallery::$meta['keywords'] = '';
            Gallery::$meta['description'] = '';
            Response::status(404);
        }
    }

    public static function paginator_ajax($current, $pages, $slug, $sort, $order, $limit_pages=10) {
        $folder = new Table('gal_folder');
        $record = $folder->select('[slug="'.$slug.'"]', null);

        $result = '';
        if ($pages > 1) {

            // pages count > limit pages
            if ($pages > $limit_pages) {
                $start = ($current <= 6) ? 1 : $current-3;
                $finish = (($pages-$limit_pages) > $current) ? ($start + $limit_pages - 1) : $pages;
            } else {
                $start = 1;
                $finish = $pages;
            }

            // pages list
            $result .= '<ul>';

            // next
            if($current!=$pages && $record['sections'] == '1')
            {
                $result .= '<li><a href="#" data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.($current+1).'" data-pages="'.$pages.'" data-key="'.$slug.'">'.__('Next', 'dev').'</a></li>';
            }

            if (($pages > $limit_pages) and ($current > 6)) {
                $result .= '<li><a href="#" data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="1" data-pages="'.$pages.'" data-key="'.$slug.'">1</a></li>';
            }

            for ($i = $start; $i <= $finish; $i++) {
                $class = ($i == $current) ? ' class="active"' : '';
                $result .= '<li'.$class.'><a href="#" data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.($i).'" data-pages="'.$pages.'" data-key="'.$slug.'">'.$i.'</a></li>';
            }

            if (($pages > $limit_pages) && ($current < ($pages - $limit_pages))) {
                $result .= '<li><a href="#" data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.$pages.'" data-pages="'.$pages.'" data-key="'.$slug.'">'.$pages.'</a></li>';
            }

            // prev
            if($current!=1 && $record['sections'] == '1')
            {
                $result .= '<li><a href="#" data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.($current-1).'" data-pages="'.$pages.'" data-key="'.$slug.'">'.__('Prev', 'dev').'</a></li>';
            }
            $result .= '</ul>';

            return $result;
        }
        return '';
    }

    public static function hits($id, $hits) {
        if (Session::exists('hits'.$id) == false) {
            $hits++;
            if(Gallery::$items->updateWhere('[id='.$id.']', array('hits' => $hits))) {
                Session::set('hits'.$id, 1);
            }
        }

        return $hits;
    }
}