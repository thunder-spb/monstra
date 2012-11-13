<?php

    /**
     *  Articles plugin
     *
     *  @package Monstra
     *  @subpackage Plugins
     *  @author Yudin Evgeniy / JINN
     *  @copyright 2012 Yudin Evgeniy / JINN
     *  @version 1.0.0
     *
     */


    // Register plugin
    Plugin::register( __FILE__,                    
                    __('Articles', 'articles'),
                    __('Articles plugin for Monstra', 'articles'),  
                    '1.0.0',
                    'JINN',                 
                    'http://monstra.org/',
                    'articles');

    // Load Articles Admin for Editor and Admin
    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
        Plugin::admin('articles');
    }
    
    if (!BACKEND) Stylesheet::add('plugins/articles/content/style.css', 'frontend', 11);
    
    Shortcode::add('articles', 'Articles::_shortcode');
    
    class Articles extends Frontend {
        
        public static $articles = null; // articles table @object
        public static $meta = array(); // meta tags articles @array
        public static $template = ''; // articles template content @string
        
        public static function main(){
                
            Articles::$articles = new Table('articles');
             
            Articles::$meta['title'] = __('Articles', 'articles');
            Articles::$meta['keywords'] = '';
            Articles::$meta['description'] = '';
                
            $uri = Uri::segments();
            
            if(empty($uri[1]) or ($uri[1] == 'page')) {
                Articles::getArticles($uri);    
            } elseif (intval($uri[1]) > 0 and isset($uri[2])) {
                Articles::getArticlesCurrent($uri);
            }
        }
        
        /** 
         * Last articles
         * <ul><?php Articles::last(3);?></ul>
         */
        public static function last($count=3, $what='last', $display=true){
            $site_url = Option::get('siteurl');
            Articles::$articles = new Table('articles');
            
            $sort = ($what == 'hits') ? 'hits' : 'date';
            
            $records_all = Articles::$articles->select('[status="published"]', 'all', null, array('id','slug','name', 'hits', 'date'));
            $records_sort = Arr::subvalSort($records_all, $sort, 'DESC');
            
            
            if(count($records_sort)>0) {
                $records = array_slice($records_sort, 0, $count); 
                
                $view = View::factory('articles/views/frontend/last');
                $view->assign('records', $records)->assign('site_url', $site_url);
                $output = $view->render();
                
                if($display) echo $output; else return $output;
            }
        }
        
        /**
         * Best views
         * <ul><?php Articles::views(5);?></ul>
         */
        public static function views($count=3) {
            Articles::last($count, 'hits', true);
        }
        
        /**
         * Shortcode articles
         * <ul>{articles list="last" count=3}</ul>
         * <ul>{articles list="views" count=3}</ul>
         */
        public static function _shortcode($attributes) {
            extract($attributes);
        
            $count = (isset($count)) ? (int)$count : 3;
        
            if (isset($list)) {
                if ($list == 'last') return Articles::last($count, 'last', false);
                elseif ($list == 'views') return Articles::last($count, 'hits', false);
            }
        }
        
        /**
         * get Articles List
         */
        public static function getArticles($uri){
        
            $site_url = Option::get('siteurl');
            $limit    = Option::get('articles_limit');
            
            $records_all = Articles::$articles->select('[status="published"]', 'all', null, array('id','slug','name', 'hits', 'date'));
            
            $count_articles = count($records_all);
            
            $pages = ceil($count_articles/$limit);
            
            $page = (isset($uri[1]) and isset($uri[2]) and $uri[1] == 'page') ? (int)$uri[2] : 1;
            
            if($page < 1 or $page > $pages) {
                Articles::error404();
            } else {
            
                $start = ($page-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, 'date', 'DESC');
                
                if($count_articles > 0) $records = array_slice($records_sort, $start, $limit);  
                else $records = array();
                
                Articles::$template = View::factory('articles/views/frontend/index')
                    ->assign('records', $records)
                    ->assign('site_url', $site_url)
                    ->assign('current_page', $page)
                    ->assign('pages_count', $pages)
                    ->render();
            }
        }
        
        /**
         * get Current articles
         */
        public static function getArticlesCurrent($uri){
            $site_url = Option::get('siteurl');
            
            $id = intval($uri[1]);
            $slug = $uri[2];
                    
            $records = Articles::$articles->select('[id='.$id.']', null);
                
            if($records) {
                if($records['slug'] == $slug) {
                
                    if(empty($records['title'])) $records['title'] = $records['name'];
                    if(empty($records['h1']))    $records['h1']    = $records['name'];
                
                    Articles::$meta['title'] = $records['title'];
                    Articles::$meta['keywords'] = $records['keywords'];
                    Articles::$meta['description'] = $records['description'];
                        
                    $records['hits'] = Articles::hits($records['id'], $records['hits']);
                        
                    Articles::$template = View::factory('articles/views/frontend/current')
                        ->assign('row', $records)
                        ->assign('site_url', $site_url)
                        ->render();
                } else {
                    Articles::error404();
                }
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
            return Articles::$template;
        }

        public static function template() {
            return Option::get('articles_template');
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
        
        public static function getContentShort($id, $short=true, $full_articles='') {
            $text = Text::toHtml(File::getContent(STORAGE . DS . 'articles' . DS . $id . '.articles.txt'));
            
            if($short) {
                $content_array = explode("{cut}", $text);
                $content = $content_array[0];
                if(count($content_array)>1) $content.= '<a href="'.$full_articles.'" class="articles-more">'.__('Read more', 'articles').'</a>';
            } else {
                $content = strtr($text, array('{cut}' => ''));
            }
            
            return Filter::apply('content', $content);
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

                // pages title
                echo '<strong>'.__('Pages:', 'articles').'</strong> &nbsp; < ';
                
                // prev
                if($current==1){ echo __('Prev', 'articles');} 
                else { echo '<a href="'.$site_url.($current-1).'">'.__('Prev', 'articles').'</a> '; } echo '&nbsp; ';
                
                // next
                if($current==$pages){ echo __('Next', 'articles'); }
                else { echo '<a href="'.$site_url.($current+1).'">'.__('Next', 'articles').'</a> '; } echo ' > ';
    
                // pages list
                echo '<div id="articles-page">';
                
                    if (($pages > $limit_pages) and ($current > 6)) {
                        echo '<a href="'.$site_url.'1">1</a>';
                        echo '<span>...</span>'; 
                    }
                
                    for ($i = $start; $i <= $finish; $i++) {
                        $class = ($i == $current) ? ' class="current"' : '';
                        echo '<a href="'.$site_url.$i.'"'.$class.'>'.$i.'</a>'; 
                    }
                
                    if (($pages > $limit_pages) and ($current < ($pages - $limit_pages))) {
                        echo '<span>...</span>'; 
                        echo '<a href="'.$site_url.$pages.'">'.$pages.'</a>';
                    }
                echo '</div>';
            }
        }
    }