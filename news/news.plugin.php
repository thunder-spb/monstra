<?php

    /**
     *  News plugin
     *
     *  @package Monstra
     *  @subpackage Plugins
     *  @author Yudin Evgeniy / JINN
     *  @copyright 2012 Yudin Evgeniy / JINN
     *  @version 1.0.3
     *
     */


    // Register plugin
    Plugin::register( __FILE__,                    
                    __('News', 'news'),
                    __('News plugin for Monstra', 'news'),  
                    '1.1.0',
                    'JINN',                 
                    'http://monstra.promo360.ru/',
                    'news');

    // Load News Admin for Editor and Admin
    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
        Plugin::admin('news');
    }
    
    if (!BACKEND) Stylesheet::add('plugins/news/news/style.css', 'frontend', 11);
    
    Shortcode::add('news', 'News::_shortcode');
    
    class News extends Frontend {
        
        public static $news = null; // news table @object
        public static $meta = array(); // meta tags news @array
        public static $template = ''; // news template content @string
        
        public static function main(){
                
            News::$news = new Table('news');
             
            News::$meta['title'] = __('News', 'news');
            News::$meta['keywords'] = '';
            News::$meta['description'] = '';
                
            $uri = Uri::segments();
            
            if(empty($uri[1]) or ($uri[1] == 'page')) {
                News::getNews($uri);    
            } elseif (intval($uri[1]) > 0 and isset($uri[2])) {
                News::getNewsCurrent($uri);
            }
        }
        
        /** 
         * Last news
         * <ul><?php News::last(3);?></ul>
         */
        public static function last($count=3, $what='last', $display=true){
            $site_url = Option::get('siteurl');
            News::$news = new Table('news');
            
            $sort = ($what == 'hits') ? 'hits' : 'date';
            
            $records_all = News::$news->select('[status="published"]', 'all', null, array('id','slug','name', 'hits', 'date'));
            $records_sort = Arr::subvalSort($records_all, $sort, 'DESC');
            
            if(count($records_sort)>0) {
                $records = array_slice($records_sort, 0, $count); 

				if ($what == 'news')
					$view = View::factory('news/views/frontend/news');
				else
					$view = View::factory('news/views/frontend/last');
                $view->assign('records', $records)->assign('site_url', $site_url);
                $output = $view->render();
                
                if($display) echo $output; else return $output;
            }
        }
        
        /**
         * Best views
         * <ul><?php News::views(5);?></ul>
         */
        public static function views($count=3) {
            News::last($count, 'hits', true);
        }
        
        /**
         * Shortcode news
         * <ul>{news list="last" count=3 cut=1}</ul>
         * <ul>{news list="views" count=3}</ul>
		 * <ul>{news list="news" count=3}</ul>
         */
        public static function _shortcode($attributes) {
            extract($attributes);
        
            $count = (isset($count)) ? (int)$count : 3;
        	if(!isset($cut))
				$cut = 0;
            if (isset($list)) {
                if ($list == 'last') return News::last($count, 'last', false);
                elseif ($list == 'views') return News::last($count, 'hits', false);
				elseif ($list == 'news') return News::last($count, 'news', false);
            }
        }
        
        /**
         * get News List
         */
        public static function getNews($uri){
        
            $site_url = Option::get('siteurl');
            $limit    = Option::get('news_limit');
            
            $records_all = News::$news->select('[status="published"]', 'all', null, array('id','slug','name', 'hits', 'date'));
            
            $count_news = count($records_all);
            
            $pages = ceil($count_news/$limit);
            
            $page = (isset($uri[1]) and isset($uri[2]) and $uri[1] == 'page') ? (int)$uri[2] : 1;
            
            if($page < 1 or $page > $pages) {
                News::error404();
            } else {
            
                $start = ($page-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, 'date', 'DESC');
                
                if($count_news > 0) $records = array_slice($records_sort, $start, $limit);  
                else $records = array();
                
                News::$template = View::factory('news/views/frontend/index')
                    ->assign('records', $records)
                    ->assign('site_url', $site_url)
                    ->assign('current_page', $page)
                    ->assign('pages_count', $pages)
                    ->render();
            }
        }
        
        /**
         * get Current news
         */
        public static function getNewsCurrent($uri){
            $site_url = Option::get('siteurl');
            
            $id = intval($uri[1]);
            $slug = $uri[2];
                    
            $records = News::$news->select('[id='.$id.']', null);
                
            if($records) {
                if($records['slug'] == $slug) {
                
                    if(empty($records['title'])) $records['title'] = $records['name'];
                    if(empty($records['h1']))    $records['h1']    = $records['name'];
                
                    News::$meta['title'] = $records['title'];
                    News::$meta['keywords'] = $records['keywords'];
                    News::$meta['description'] = $records['description'];
                        
                    $records['hits'] = News::hits($records['id'], $records['hits']);
                        
                    News::$template = View::factory('news/views/frontend/current')
                        ->assign('row', $records)
                        ->assign('site_url', $site_url)
                        ->render();
                } else {
                    News::error404();
                }
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
            return News::$template;
        }

        public static function template() {
            return Option::get('news_template');
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
        
        public static function getContentShort($id, $short=true, $full_news='') {
            $text = Text::toHtml(File::getContent(STORAGE . DS . 'news' . DS . $id . '.news.txt'));
            
            if($short) {
                $content_array = explode("{cut}", $text);
                $content = $content_array[0];
                if(count($content_array)>1) $content.= '<a href="'.$full_news.'" class="news-more">'.__('Read more', 'news').'</a>';
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
                echo '<strong>'.__('Pages:', 'news').'</strong> &nbsp; < ';
                
                // prev
                if($current==1){ echo __('Prev', 'news');} 
                else { echo '<a href="'.$site_url.($current-1).'">'.__('Prev', 'news').'</a> '; } echo '&nbsp; ';
                
                // next
                if($current==$pages){ echo __('Next', 'news'); }
                else { echo '<a href="'.$site_url.($current+1).'">'.__('Next', 'news').'</a> '; } echo ' > ';
    
                // pages list
                echo '<div id="news-page">';
                
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