<?php

/**
 *  Articles plugin
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
    __('Articles', 'articles'),
    __('Articles plugin for Monstra', 'articles'),
    '1.6.0',
    'KANekT',
    'http://monstra.org/',
    'articles');

// Load Articles Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
    Plugin::admin('articles');
}

/*
 * Register for Developer Helper
 */
Registry::set('dev_migrate_backend', 1);
Registry::set('dev_migrate_frontend', 1);
Registry::set('dev_valid_backend', 1);
Registry::set('dev_bootstrap_file_upload', 1);
Registry::set('dev_fancy_frontend', 1);

Shortcode::add('articles', 'Articles::_shortcode');

class Articles extends Frontend {

    public static $articles = null; // articles table @object
    public static $_articles = null; // articles item @object
    public static $meta = array(); // meta tags articles @array
    public static $template = ''; // articles template content @string
    public static $slug;

    public static function main(){

        Articles::$articles = new Table('articles');

        Articles::$meta['title'] = __('Articles', 'articles');
        Articles::$meta['keywords'] = '';
        Articles::$meta['description'] = '';

        $uri = Uri::segments();

        if($uri[0] == 'articles') {
            if (isset($uri[1]))
            {
                Articles::getArticlesBySlug($uri[1]);
            }
            else
            {
                Articles::getArticles($uri);
            }
        }
    }

    /**
     * List articles for shortcode
     */
    private static function getArticlesList($count=3, $action='last', $display=true){
        $opt['site_url'] = Option::get('siteurl');
        Articles::$articles = new Table('articles');

        $sort = ($action == 'views') ? 'hits' : 'date';

        $records_all = Articles::$articles->select('[status="published"]', 'all', null, array('id','slug','title', 'hits', 'date'));
        $records_sort = Arr::subvalSort($records_all, $sort, 'DESC');

        if(count($records_sort)>0) {
            $records = array_slice($records_sort, 0, $count);

            switch($action)
            {
                case 'block':
                    $output = View::factory('articles/views/frontend/block')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();
                    break;
                case 'views':
                    $output = View::factory('articles/views/frontend/views')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();
                    break;
                default:
                    $output = View::factory('articles/views/frontend/last')
                        ->assign('records', $records)
                        ->assign('opt', $opt)
                        ->render();
                    break;

            }

            if($display) echo $output; else return $output;
        }
    }

    /**
     * Last articles
     * <ul><?php Articles::last(3);?></ul>
     */
    public static function last($count=3) {
        Articles::getArticlesList($count, 'last');
    }

    /**
     * Best views
     * <ul><?php Articles::views(5);?></ul>
     */
    public static function views($count=3) {
        Articles::getArticlesList($count, 'views');
    }

    /**
     * Articles views
     * <ul><?php Articles::block(5);?></ul>
     */
    public static function block($count=3) {
        Articles::getArticlesList($count, 'block');
    }

    /**
     * Shortcode articles
     * <ul>{articles list="last" count=3}</ul>
     * <ul>{articles list="views" count=3}</ul>
     * <ul>{articles list="block" count=3}</ul>
     */
    public static function _shortcode($attributes) {
        extract($attributes);

        $count = (isset($count)) ? (int)$count : 3;
        if (isset($list)) {
            return Articles::getArticlesList($count, $list, false);
        }
    }

    /**
     * get Articles List
     */
    public static function getArticles($uri){

        $opt['site_url'] = Option::get('siteurl');
        $opt['url'] = $opt['site_url'] . 'public/uploads/articles/';
        $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'articles' . DS;
        $limit    = Option::get('article_limit');

        if (Request::get('tag')) {
            $query = '[status="published" and contains(tags, "'.Request::get('tag').'")]';
            Notification::set('tag', Request::get('tag'));
        } else {
            $query = '[status="published"]';
            Notification::clean();
        }

        $records_all = Articles::$articles->select($query, 'all', null, array('id','slug','title', 'hits', 'date'));

        $count_articles = count($records_all);

        $opt['pages'] = ceil($count_articles/$limit);

        $opt['page'] = (isset($uri[1]) and isset($uri[2]) and $uri[1] == 'page') ? (int)$uri[2] : 1;

        if($opt['page'] < 1 or $opt['page'] > $opt['pages']) {
            Articles::error404();
        } else {

            $start = ($opt['page']-1)*$limit;

            $records_sort = Arr::subvalSort($records_all, 'date', 'DESC');

            if($count_articles > 0) $records = array_slice($records_sort, $start, $limit);
            else $records = array();

            Articles::$template = View::factory('articles/views/frontend/index')
                ->assign('records', $records)
                ->assign('opt', $opt)
                ->render();
        }
    }

    /**
     * get Current articles
     */
    public static function getArticlesBySlug($slug){
        $opt['site_url'] = Option::get('siteurl');
        $opt['url'] = $opt['site_url'] . 'public/uploads/articles/';
        $opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'articles' . DS;
        $record = Articles::$articles->select('[slug="'.$slug.'"]', null);
        Articles::$slug = $slug;

        if($record) {
            Articles::$_articles = $record;

            if(empty($record['title'])) $record['title'] = $record['name'];

            Articles::$meta['title'] = $record['title'];
            Articles::$meta['keywords'] = $record['keywords'];
            Articles::$meta['description'] = $record['description'];

            $record['hits'] = Articles::hits($record['id'], $record['hits']);

            Articles::$template = View::factory('articles/views/frontend/item')
                ->assign('item', $record)
                ->assign('opt', $opt)
                ->render();
        } else {
            Articles::error404();
        }
    }

    public static function title(){
        return Articles::$meta['title'];
    }

    public static function keywords(){
        return Articles::$meta['keywords'];
    }

    public static function description(){
        return Articles::$meta['description'];
    }

    public static function content(){
        $content = Filter::apply('content', Articles::$template);
        return $content;
    }

    public static function template() {
        if (Articles::$_articles['template'] == '') return Option::get('article_template'); else return Articles::$_articles['template'];
    }

    public static function error404() {
        if (BACKEND == false) {
            Articles::$template = Text::toHtml(File::getContent(STORAGE . DS . 'pages' . DS . '1.page.txt'));
            Articles::$meta['title'] = 'error404';
            Response::status(404);
        }
    }

    public static function hits($id, $hits) {
        if (Session::exists('hits'.$id) == false) {
            $hits++;
            if(Articles::$articles->updateWhere('[id='.$id.']', array('hits' => $hits))) {
                Session::set('hits'.$id, 1);
            }
        }

        return $hits;
    }

    public static function getArticlesContent($id, $view=true) {
        if($view) {
            $content = Text::toHtml(File::getContent(STORAGE . DS . 'articles' . DS . $id . '.articles.txt'));
        } else {
            $content = Text::toHtml(File::getContent(STORAGE . DS . 'articles' . DS . $id . '.short.article.txt'));
        }

        return Filter::apply('content', $content);
    }

    /**
     * Get tags
     * @author Romanenko Sergey / Awilum
     *
     *  <code>
     *      echo Articles::getTags();
     *  </code>
     *
     * @return string
     */
    public static function getTags($slug = null) {

        // Display view
        return View::factory('articles/views/frontend/tags')
            ->assign('tags', Articles::getTagsArray($slug))
            ->render();

    }

    /**
     * Get tags array
     * @author Romanenko Sergey / Awilum
     *
     *  <code>
     *      echo Articles::getTagsArray();
     *  </code>
     *
     * @return array
     */
    public static function getTagsArray($slug = null) {

        // Init vars
        $tags = array();
        $tags_string = '';

        if ($slug == null) {
            $posts = Articles::$articles->select('[status="published"]', 'all');
        } else {
            $posts = Articles::$articles->select('[status="published" and slug="'.$slug.'"]', 'all');
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
     *      echo Articles::getRelatedPosts();
     *  </code>
     *
     * @return string
     */
    public static function getRelatedPosts($limit = null) {

        $related_posts = array();
        $tags = Articles::getTagsArray(Articles::$slug);

        foreach($tags as $tag) {

            $query = '[status="published" and contains(keywords, "'.$tag.'") and slug!="'.Articles::$slug.'"]';

            if ($result = Arr::subvalSort(Articles::$articles->select($query, ($limit == null) ? 'all' : (int)$limit), 'date', 'DESC')) {
                $related_posts = $result;
            }
        }

        // Display view
        return View::factory('articles/views/frontend/related_posts')
            ->assign('related_posts', $related_posts)
            ->render();

    }
}