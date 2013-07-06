<?php

/**
 *  News plugin
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
    __('News', 'news'),
    __('News plugin for Monstra', 'news'),
    '1.6.0',
    'KANekT',
    'http://monstra.org/',
    'news');

// Load News Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
    Plugin::admin('news');
}

/*
 * Register for Developer Helper
 */
Registry::set('dev_migrate_backend', 1);
Registry::set('dev_migrate_frontend', 1);
Registry::set('dev_valid_backend', 1);
Registry::set('dev_bootstrap_file_upload', 1);
Registry::set('dev_fancy_frontend', 1);

Shortcode::add('news', 'News::_shortcode');

class News extends Frontend {

    public static $news = null; // news table @object
    public static $_news = null; // news item @object
    public static $meta = array(); // meta tags news @array
    public static $template = ''; // news template content @string
    public static $slug;

    public static function main(){

        News::$news = new Table('news');

        News::$meta['title'] = __('News', 'news');
        News::$meta['keywords'] = '';
        News::$meta['description'] = '';

        $uri = Uri::segments();

        if($uri[0] == 'news') {
            if (isset($uri[1]))
            {
                News::getNewsBySlug($uri[1]);
            }
            else
            {
                News::getNews($uri);
            }
        }
    }

    /**
     * List news for shortcode
     */
    private static function getNewsList($count=3, $action='last', $display=true){
        $opt['site_url'] = Option::get('siteurl');
        News::$news = new Table('news');

        $sort = ($action == 'views') ? 'hits' : 'date';

        $records_all = News::$news->select('[status="published"]', 'all', null, array('id','slug','title', 'hits', 'date'));
        $records_sort = Arr::subvalSort($records_all, $sort, 'DESC');

        if(count($records_sort)>0) {
            $records = array_slice($records_sort, 0, $count);

            switch($action)
            {
                case 'block':
                    $output = View::factory('news/views/frontend/block')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();
                    break;
                case 'views':
                    $output = View::factory('news/views/frontend/views')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();
                    break;
                default:
                    $output = View::factory('news/views/frontend/last')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();
                    break;

            }

            if($display) echo $output; else return $output;
        }
    }

    /**
     * Last news
     * <ul><?php News::last(3);?></ul>
     */
    public static function last($count=3) {
        News::getNewsList($count, 'last');
    }

    /**
     * Best views
     * <ul><?php News::views(5);?></ul>
     */
    public static function views($count=3) {
        News::getNewsList($count, 'views');
    }

    /**
     * News views
     * <ul><?php News::block(5);?></ul>
     */
    public static function block($count=3) {
        News::getNewsList($count, 'block');
    }

    /**
     * Shortcode news
     * <ul>{news list="last" count=3}</ul>
     * <ul>{news list="views" count=3}</ul>
     * <ul>{news list="block" count=3}</ul>
     */
    public static function _shortcode($attributes) {
        extract($attributes);

        $count = (isset($count)) ? (int)$count : 3;
        if (isset($list)) {
            return News::getNewsList($count, $list, false);
        }
    }

    /**
     * get News List
     */
    public static function getNews($uri){

        $opt['site_url'] = Option::get('siteurl');
        $opt['url'] = $opt['site_url'] . 'public/uploads/news/';
        $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'news' . DS;
        $limit    = Option::get('news_limit');

        if (Request::get('tag')) {
            $query = '[status="published" and contains(tags, "'.Request::get('tag').'")]';
            Notification::set('tag', Request::get('tag'));
        } else {
            $query = '[status="published"]';
            Notification::clean();
        }

        $records_all = News::$news->select($query, 'all', null, array('id','slug','title', 'hits', 'date'));

        $count_news = count($records_all);

        $opt['pages'] = ceil($count_news/$limit);

        $opt['page'] = (isset($uri[1]) and isset($uri[2]) and $uri[1] == 'page') ? (int)$uri[2] : 1;

        if($opt['page'] < 1 or $opt['page'] > $opt['pages']) {
            News::error404();
        } else {

            $start = ($opt['page']-1)*$limit;

            $records_sort = Arr::subvalSort($records_all, 'date', 'DESC');

            if($count_news > 0) $records = array_slice($records_sort, $start, $limit);
            else $records = array();

            News::$template = View::factory('news/views/frontend/index')
                ->assign('records', $records)
                ->assign('opt', $opt)
                ->render();
        }
    }

    /**
     * get Current news
     */
    public static function getNewsBySlug($slug){
        $opt['site_url'] = Option::get('siteurl');
        $opt['url'] = $opt['site_url'] . 'public/uploads/news/';
        $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'news' . DS;
        $record = News::$news->select('[slug="'.$slug.'"]', null);
        News::$slug = $slug;

        if($record) {
            News::$_news = $record;

            if(empty($record['title'])) $record['title'] = $record['name'];

            News::$meta['title'] = $record['title'];
            News::$meta['keywords'] = $record['keywords'];
            News::$meta['description'] = $record['description'];

            $record['hits'] = News::hits($record['id'], $record['hits']);

            News::$template = View::factory('news/views/frontend/item')
                ->assign('item', $record)
                ->assign('opt', $opt)
                ->render();
        } else {
            News::error404();
        }
    }

    public static function title(){
        return News::$meta['title'];
    }

    public static function keywords(){
        return News::$meta['keywords'];
    }

    public static function description(){
        return News::$meta['description'];
    }

    public static function content(){
        $content = Filter::apply('content', News::$template);
        return $content;
    }

    public static function template() {
        if (News::$_news['template'] == '') return Option::get('news_template'); else return News::$_news['template'];
    }

    public static function error404() {
        if (BACKEND == false) {
            News::$template = Text::toHtml(File::getContent(STORAGE . DS . 'pages' . DS . '1.page.txt'));
            News::$meta['title'] = 'error404';
            Response::status(404);
        }
    }

    public static function hits($id, $hits) {
        if (Session::exists('hits'.$id) == false) {
            $hits++;
            if(News::$news->updateWhere('[id='.$id.']', array('hits' => $hits))) {
                Session::set('hits'.$id, 1);
            }
        }

        return $hits;
    }

    public static function getNewsContent($id, $view=true) {
        if($view) {
            $content = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $id . '.news.txt'));
        } else {
            $content = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $id . '.short.news.txt'));
        }

        return Filter::apply('content', $content);
    }

    /**
     * Get tags
     * @author Romanenko Sergey / Awilum
     *
     *  <code>
     *      echo News::getTags();
     *  </code>
     *
     * @return string
     */
    public static function getTags($slug = null) {

        // Display view
        return View::factory('news/views/frontend/tags')
            ->assign('tags', News::getTagsArray($slug))
            ->render();

    }

    /**
     * Get tags array
     * @author Romanenko Sergey / Awilum
     *
     *  <code>
     *      echo News::getTagsArray();
     *  </code>
     *
     * @return array
     */
    public static function getTagsArray($slug = null) {

        // Init vars
        $tags = array();
        $tags_string = '';

        if ($slug == null) {
            $posts = News::$news->select('[status="published"]', 'all');
        } else {
            $posts = News::$news->select('[status="published" and slug="'.$slug.'"]', 'all');
        }

        foreach($posts as $post) {
            $tags_string .= $post['tags'].',';
        }

        $tags_string = substr($tags_string, 0, strlen($tags_string)-1);

        // Explode tags in tags array
        $tags = explode(',', $tags_string);

        // Remove empty array elementss
        foreach ($tags as $key => $value) {
            if ($tags[$key] == '') {
                unset($tags[$key]);
            }
        }

        // Trim tags
        array_walk($tags, create_function('&$val', '$val = trim($val);'));

        // Get unique tags
        $tags = array_unique($tags);

        // Return tags
        return $tags;
    }

    /**
     * Get related posts
     * @author Romanenko Sergey / Awilum
     *
     *  <code>
     *      echo News::getRelatedPosts();
     *  </code>
     *
     * @return string
     */
    public static function getRelatedPosts($limit = null) {

        $related_posts = array();
        $tags = News::getTagsArray(News::$slug);

        foreach($tags as $tag) {

            $query = '[status="published" and contains(keywords, "'.$tag.'") and slug!="'.News::$slug.'"]';

            if ($result = Arr::subvalSort(News::$news->select($query, ($limit == null) ? 'all' : (int)$limit), 'date', 'DESC')) {
                $related_posts = $result;
            }
        }

        // Display view
        return View::factory('news/views/frontend/related_posts')
            ->assign('related_posts', $related_posts)
            ->render();

    }
}