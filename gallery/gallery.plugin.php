<?php

/**
 *  Gallery plugin
 *
 *  @package Monstra
 *  @subpackage Plugins
 *  @copyright Copyright (C) KANekT @ http://kanekt.ru
 *  @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 *  Donate Web Money Z104136428007 R346491122688
 *
 */


// Register plugin
Plugin::register( __FILE__,
    __('Gallery', 'gallery'),
    __('Gallery plugin for Monstra', 'gallery'),
    '1.3.0',
    'KANekT',
    'http://kanekt.ru/',
    'gallery');


// Load Sandbox Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {

    Plugin::admin('gallery');

}

Javascript::add('plugins/gallery/js/back.js', 'backend', 18);
Javascript::add('plugins/gallery/js/front.js', 'frontend', 18);

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

    public static function main(){
        Gallery::$items = new Table('gal_items');
        Gallery::$folder = new Table('gal_folder');
        $uri = Uri::segments();

        if (Request::get('slug') && $uri[0] == 'gallery')
        {
            $images = Gallery::getList(Request::get('slug'),Request::get('page'));
            $pages = Gallery::paginator_ajax(Request::get('page'),Request::get('pages'),Request::get('slug'),Request::get('sort'),Request::get('order'));
            $json_data = array ('pages'=>$pages,'images'=>$images);
            echo json_encode($json_data);
            exit();
        }
        else if($uri[0] == 'gallery' && count($uri) >= 0) {
            Gallery::viewGallery($uri[1]);
        }
        else
        {
            Gallery::error404();
        }
    }

    /**
     * Shortcode gallery
    */
    public static function _shortcode($attributes) {
        extract($attributes);

        Gallery::$sort = (isset($sort)) ? $sort : 'date';
        Gallery::$order = (isset($order)) ? strtoupper($order) : 'DESC';
        Gallery::$items = new Table('gal_items');
        Gallery::$folder = new Table('gal_folder');

        if (isset($list) && (isset($slug))) {
            switch ($list) {
                case 'album':
                    return Gallery::viewGallery($slug, true);
            }
        }
        return '';
    }

    /**
     * get Get Gallery by Slug
     */
    public static function viewGallery($slug, $display = false){

        $images = Gallery::getList($slug,1,true);

        if ($display)
        {
            return $images;
        }
        else
        {
            Gallery::$template = $images;
        }
    }


    private static function getList($slug, $page, $pages = false)
    {
        $meta = Gallery::$folder->select('[slug="'.$slug.'"]', null);
        if (isset($meta["id"]))
        {
            $id = $meta["id"];
            $site_url = Option::get('siteurl');
            $limit    = $meta['limit'];

            $opt["dir"] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'gallery' . DS;
            $opt["img"] = $site_url . 'public/uploads/gallery/';
            $opt["slug"] = $slug;

            $records_all = Gallery::$items->select('[guid="'.$id.'"]', 'all', null, array('id','title','description','date'));

            $count_items = count($records_all);

            $opt["pages"] = ceil($count_items/$limit);
            $opt["page"] = $page;

            if($opt["page"] < 1 or $opt["page"] > $opt["pages"]) {
                Gallery::error404();
            } else {

                $start = ($opt["page"]-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, Gallery::$sort, Gallery::$order);

                if($count_items > 0) $records = array_slice($records_sort, $start, $limit);
                else $records = array();

                if ($pages)
                {
                    $output = View::factory('gallery/views/frontend/images')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();

                    $pages = Gallery::paginator_ajax($opt["page"],$opt["pages"],$slug, Gallery::$sort, Gallery::$order);
                    return '<ul class="thumbnails">'.$output.'</ul><div class="pagination">'.$pages.'</div>';
                }
                else
                {
                    $output = View::factory('gallery/views/frontend/short')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();
                    return $output;
                }
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
            Response::status(404);
        }
    }

    /**
     * current page
     * pages all
     * site_url
     * limit pages
     */
    public static function paginator($current, $pages, $site_url, $limit_pages=10) {

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
            echo '<div class="pagination"><ul>';

            // next
            if($current!=$pages && Gallery::$meta['sections'] == '1')
            {
                echo '<li><a href="'.$site_url.($current+1).'">'.__('Next', 'gallery').'</a></li>';
            }

            if (($pages > $limit_pages) and ($current > 6)) {
                echo '<li><a href="'.$site_url.'1">1</a></li>';
            }

            for ($i = $start; $i <= $finish; $i++) {
                $class = ($i == $current) ? ' class="active"' : '';
                echo '<li '.$class.'><a href="'.$site_url.$i.'">'.$i.'</a></li>';
            }

            if (($pages > $limit_pages) && ($current < ($pages - $limit_pages))) {
                echo '<li><a href="'.$site_url.$pages.'">'.$pages.'</a></li>';
            }

            // prev
            if($current!=1 && Gallery::$meta['sections'] == '1')
            {
                echo '<li><a href="'.$site_url.($current-1).'">'.__('Prev', 'gallery').'</a></li>';
            }
            echo '</ul></div>';
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
                $result .= '<li><span data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.($current+1).'" data-pages="'.$pages.'" data-key="'.$slug.'">'.__('Next', 'gallery').'</span></li>';
            }

            if (($pages > $limit_pages) and ($current > 6)) {
                $result .= '<li><span data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="1" data-pages="'.$pages.'" data-key="'.$slug.'">1</span></li>';
            }

            for ($i = $start; $i <= $finish; $i++) {
                $class = ($i == $current) ? ' class="active"' : '';
                $result .= '<li'.$class.'><span data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.($i).'" data-pages="'.$pages.'" data-key="'.$slug.'">'.$i.'</span></li>';
            }

            if (($pages > $limit_pages) && ($current < ($pages - $limit_pages))) {
                $result .= '<li><span data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.$pages.'" data-pages="'.$pages.'" data-key="'.$slug.'">'.$pages.'</span></li>';
            }

            // prev
            if($current!=1 && $record['sections'] == '1')
            {
                $result .= '<li><span data-action="gallery" data-sort="'.$sort.'" data-order="'.$order.'" data-page="'.($current-1).'" data-pages="'.$pages.'" data-key="'.$slug.'">'.__('Prev', 'gallery').'</span></li>';
            }
            $result .= '</ul>';

            return $result;
        }
    }
}