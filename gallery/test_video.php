<?php
$media = 'http://instagr.am/p/IejkuUGxQn';
//$media = 'http://www.youtube.com/watch?v=opj24KnzrWo';
//$media = 'http://vimeo.com/36031564';
//$media = 'http://www.metacafe.com/watch/7635964/';
//$media = 'http://www.dailymotion.com/video/xoeylt_electric-guest-this-head-i-hold_music';
//$media = 'http://twitpic.com/7p93st';

$url = parse_url($media);
        $img_url = '';
        switch($url['host'])
        {
            case 'www.youtube.com':
                $img_url = youtube($media);
                break;
            case 'youtube.com':
                $img_url = youtube($media);
                break;

            case 'www.vimeo.com':
                $img_url = vimeo($media);
                break;
            case 'vimeo.com':
                $img_url = vimeo($media);
                break;

            case 'www.metacafe.com':
                $img_url = metacafe($media);
                break;
            case 'metacafe.com':
                $img_url = metacafe($media);
                break;

            case 'www.dailymotion.com':
                $img_url = dailymotion($media);
                break;
            case 'dailymotion.com':
                $img_url = dailymotion($media);
                break;

            case 'www.twitpic.com':
                $img_url = twitpic($media);
                break;
            case 'twitpic.com':
                $img_url = twitpic($media);
                break;

            case 'www.instagr.am':
                $img_url = instagram($media);
                break;
            case 'instagr.am':
                $img_url = instagram($media);
                break;
        }
		
    function youtube($media)
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $media, $matches);
        if ($matches[0] != '')
            return 'http://img.youtube.com/vi/'.$matches[0].'/0.jpg';
        return '';
    }

    function vimeo($media)
    {
        preg_match('#http://(?:\w+.)?vimeo.com/(?:video/|moogaloop\.swf\?clip_id=|)(\w+)#i', $media, $matches);
        if ($matches[1] != '')
        {
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$matches[1].php"));
            return $hash[0]['thumbnail_medium'];
        }
        return '';
    }

    function metacafe($media)
    {
        preg_match('#http://(?:www\.)?metacafe.com/(?:watch|fplayer)/(\w+)/#i', $media, $matches);
        if ($matches[1] != '')
            return 'http://www.metacafe.com/thumb/'.$matches[1].'.jpg';
        return '';
    }

    function dailymotion($media)
    {
        preg_match('#http://(?:\w+.)?dailymotion.com/video/([A-Za-z0-9]+)#s', $media, $matches);
        if ($matches[1] != '')
        {
            $hash = json_decode(file_get_contents("https://api.dailymotion.com/video/$matches[1]?fields=thumbnail_large_url"));
            return $hash->thumbnail_large_url;
        }
        return '';
    }

    function twitpic($media)
    {
        preg_match('#http://(?:\w+.)?twitpic.com/([A-Za-z0-9]+)#i', $media, $matches);
        if ($matches[1] != '')
            return 'http://twitpic.com/show/thumb/'.$matches[1];
        return '';
    }

    function instagram($media)
    {
        $hash = json_decode(file_get_contents("http://api.instagram.com/oembed?url=".$media));
        return $hash->url;
    }
	
	echo '<img src='.$img_url.'>';