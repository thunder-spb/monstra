<?php

/**
 *  Catalog plugin
 *
 *  @package Monstra
 *  @subpackage Plugins
 *  @copyright Copyright (C) KANekT @ http://kanekt.ru
 *  @license http://creativecommons.org/licenses/by-nc/3.0/
 *  Creative Commons Attribution-NonCommercial 3.0
 *  Donate Web Money Z104136428007 R346491122688
 *  Yandex Money 410011782214621
 */


// Register plugin
Plugin::register( __FILE__,
    __('Catalog', 'catalog'),
    __('Catalog plugin for Monstra', 'catalog'),
    '1.4.2',
    'KANekT',
    'http://kanekt.ru/',
    'catalog');


// Load Sandbox Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {

    Plugin::admin('catalog');

}
Javascript::add('plugins/catalog/js/back.js', 'backend', 15);
Shortcode::add('catalog', 'Catalog::_shortcode');

/*
 * Register for Developer Helper
 */
Registry::set('dev_valid_backend', 1);
Registry::set('dev_file_upload', 1);
Registry::set('dev_fancy_frontend', 1);

/**
 * Sandbox simple class
 */
class Catalog extends Frontend {

    public static $catalog = null; // catalog table @object
    public static $items = null; // catalog table @object
    public static $tags = null; // catalog table @object
    public static $meta = array(); // meta tags catalog @array
    public static $template = ''; // catalog template content @string

    public static function main(){
        Catalog::$meta['title'] = __('Catalog', 'catalog');
        Catalog::$meta['keywords'] = '';
        Catalog::$meta['description'] = '';
        Catalog::$catalog = new Table('cat_folder');
        Catalog::$items = new Table('cat_items');
        Catalog::$tags = new Table('cat_tag');

        $uri = Uri::segments();
        if(isset($uri[2]) && $uri[2] == 'item' && isset($uri[3])) {
            if (intval($uri[3]) > 0)
            {
                Catalog::getItemCurrent($uri[3], $uri[1]);
            }
            else
            {
                Catalog::error404();
            }
        }
        elseif(isset($uri[1])) {
            Catalog::getCatalogCurrent($uri[1]);
        }
        else
        {
            Catalog::Index();
        }
    }

    /**
     * Shortcode catalog
     */
    public static function _shortcode($attributes) {
        extract($attributes);

        $count = (isset($count)) ? (int)$count : 5;
        $uid = (isset($uid)) ? (int)$uid : 0;
        $price = (isset($price)) ? (int)$price : 0;
        $sort = (isset($sort)) ? (string)$sort : 'id';
        Catalog::$items = new Table('cat_items');
        Catalog::$catalog = new Table('cat_folder');
        Catalog::$tags = new Table('cat_tag');

        if (isset($list)) {
            switch ($list) {
                case 'item':
                    return Catalog::getItem($uid, $count, $price);
                case 'cat':
                    return Catalog::getCatalog($uid, $count, $price, $sort);
                case 'last':
                    return Catalog::getLast($count, $price, true);
                case 'menu':
                    return Catalog::getMenu(true);
            }
        }
        return '';
    }

    /**
     * Catalog menu
     * {catalog list="menu"}
     */
    public static function getMenu($display = false)
    {
        $tags = Catalog::$tags->select(null, 'all');
        $tags = Arr::subvalSort($tags, 'sorting');
        $output = View::factory('catalog/views/frontend/menu')
            ->assign('tags', $tags)
            ->render();

        if ($display){
            return $output;
        }
        else{
            echo $output;
        }
    }

    /**
     * Catalog last items
     * {catalog list="last" count=5}
     */
    public static function getLast($count=0, $price=0, $display = false){
        $records = Catalog::$items->select(null, 'all');
        $records_sort = Arr::subvalSort($records, 'id', 'DESC');

        if(count($records_sort)>0) {
            if($count == 0) {
                $count = 5;
            }

            $records = array_slice($records_sort, 0, $count);

            $opt = array();
            $opt["dir"]         = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
            $opt["url"]         = Option::get('siteurl').'public/uploads/catalog/';
            $opt["site_url"]    = Option::get('siteurl');
            $opt["price"]       = $price;
            $opt['display']     = $display;

            if(count($records)>0) {

                $output = View::factory('catalog/views/frontend/last')
                    ->assign('items', $records)
                    ->assign('opt', $opt)
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
     * Catalog items
     * {catalog list="item" uid=3}
     */
    public static function getItem($id=0, $count=0, $price=0){
        $records = Catalog::$items->select('[id="'.$id.'"]', null);
        $records['hits'] = Catalog::hits($records['id'], $records['hits']);

        $opt = array();
        $opt["dir"]         = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
        $opt["url"]         = Option::get('siteurl').'public/uploads/catalog/';
        $opt["site_url"]    = Option::get('siteurl');
        $opt["price"]       = $price;
        $opt['display']     = 0;
        $opt['catalog']     = Catalog::$catalog->select('[id="'.$records['catalog'].'"]', null);

        if(count($records)>0) {

            $output = View::factory('catalog/views/frontend/item')
                ->assign('item', $records)
                ->assign('opt', $opt)
                ->render();

            return $output;
        }
        return '';
    }

    /**
     * get Catalog List
     * {catalog list="cat" uid=1}
     */
    public static function getCatalog($id=0, $count=0, $price=0, $sort='id'){
        $records_all    = Catalog::$items->select('[catalog="'.$id.'"]', 'all', null, array('id','title','short'));
        $records        = Arr::subvalSort($records_all, $sort, 'DESC');
        $cat            = Catalog::$catalog->select('[id="'.$id.'"]', null);

        $opt = array();
        $opt["dir"]         = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
        $opt["url"]         = Option::get('siteurl').'public/uploads/catalog/';
        $opt["site_url"]    = Option::get('siteurl');
        $opt["price"]       = $price;
        $opt['display']     = 0;
        $opt['title']       = $cat['title'];
        $opt['id']          = $cat['id'];
        $opt['slug']        = $cat['slug'];

        if(count($records)>0) {
            /*if ($count > 0)
            {
                $records = array_slice($records, 0, $count);
            }*/

            $output = View::factory('catalog/views/frontend/catalog')
                ->assign('records', $records)
                ->assign('opt', $opt)
                ->render();

            return $output;
        }
        return '';
    }

    /**
     * get Current catalog
     */
    public static function getCatalogCurrent($uri){
        $records = Catalog::$catalog->select('[slug="'.$uri.'"]', null);
        $limit = Option::get('catalog_limit');

        $opt = array();
        $opt['id']          = $records['id'];
        $opt['title']       = $records['title'];
        $opt['slug']        = $records['slug'];
        $opt["dir"]         = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
        $opt["url"]         = Option::get('siteurl').'public/uploads/catalog/';
        $opt["site_url"]    = Option::get('siteurl');
        $opt["price"]       = 1;

        if(isset($records['id'])) {
            $record = Catalog::$items->select('[catalog="'.$records['id'].'"]');

            $cnt = count($record);
            $opt['pages'] = ceil($cnt/$limit);

            $opt['page']    = (Request::get('page')) ? (int)Request::get('page') : 1;
            $opt['sort']    = (Request::get('sort')) ? (string)Request::get('sort') : 'date';
            $opt['order']   = (Request::get('order') and Request::get('order')=='ASC') ? 'ASC' : 'DESC';

            if ($opt['page'] < 1) { $opt['page'] = 1; }
            elseif ($opt['page'] > $opt['pages']) { $opt['page'] = $opt['pages']; }

            $start = ($opt['page']-1)*$limit;
            $record_sort = Arr::subvalSort($record, $opt['sort'], $opt['order']);
            if($cnt>0) $record = array_slice($record_sort, $start, $limit);
            else $record = array();

            if (count($record) > 0)
            {
                Catalog::$meta['title'] = $records['title'];
                Catalog::$meta['keywords'] = $records['keywords'];
                Catalog::$meta['description'] = $records['description'];

                Catalog::$template = View::factory('catalog/views/frontend/catalog')
                    ->assign('records', $record)
                    ->assign('opt', $opt)
                    ->render();
            } else {
                Catalog::error404();
            }
        }
        else {
            Catalog::error404();
        }
    }


    private static function getItemCurrent($id, $catalog)
    {
        $opt["dir"]         = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
        $opt["url"]         = Option::get('siteurl').'public/uploads/catalog/';
        $opt["site_url"]    = Option::get('siteurl');
        $opt['catalog']     = Catalog::$catalog->select('[slug="'.$catalog.'"]', null);
        $records            = Catalog::$items->select('[id="'.$id.'"]', null);
        $records['hits']    = Catalog::hits($records['id'], $records['hits']);

        Catalog::$meta['title']         = $records['title'];
        Catalog::$meta['keywords']      = $records['keywords'];
        Catalog::$meta['description']   = $records['description'];

        Catalog::$template = View::factory('catalog/views/frontend/item')
            ->assign('item', $records)
            ->assign('opt', $opt)
            ->render();
    }

    private static function Index()
    {
        $opt["dir"]         = ROOT . DS . 'public' . DS . 'uploads' . DS . 'catalog' . DS;
        $opt["url"]         = Option::get('siteurl').'public/uploads/catalog/';
        $opt["site_url"]    = Option::get('siteurl');

        $tags = Catalog::$tags->select(null, 'all');
        $tags = Arr::subvalSort($tags, 'sorting');
        Catalog::$template = View::factory('catalog/views/frontend/index')
            ->assign('tags', $tags)
            ->assign('opt', $opt)
            ->render();
    }

    public static function title(){
        return Catalog::$meta['title'];
    }

    public static function keywords(){
        return Catalog::$meta['keywords'];
    }

    public static function description(){
        return Catalog::$meta['description'];
    }

    public static function content(){
        return Catalog::$template;
    }

    public static function template() {
        return Option::get('catalog_template');
    }

    public static function error404() {
        if (BACKEND == false) {
            Catalog::$template = Text::toHtml(File::getContent(STORAGE . DS . 'pages' . DS . '1.page.txt'));
            Catalog::$meta['title'] = 'error404';
            Response::status(404);
        }
    }

    public static function hits($id, $hits) {
        if (Session::exists('hits'.$id) == false) {
            $hits++;
            if(Catalog::$items->updateWhere('[id='.$id.']', array('hits' => $hits))) {
                Session::set('hits'.$id, 1);
            }
        }

        return $hits;
    }
}