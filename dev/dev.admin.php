<?php

// Admin Navigation: add new item
//Navigation::add(__('Dev', 'dev'), 'content', 'dev', 10);


/**
 * Sandbox admin class
 */
class DevAdmin extends Backend {
    public static function ReSize($img, $folder, $name, $opt)
    {
        $wmax   = (int)$opt['wmax'];
        $hmax   = (int)$opt['hmax'];
        $width  = (int)$opt['w'];
        $height = (int)$opt['h'];
        $resize = $opt['resize'];
        $ratio  = $width/$height;

        if ($img->width > $wmax or $img->height > $hmax) {
            if ($img->height > $img->width) {
                $img->resize($wmax, $hmax, Image::HEIGHT);
            } else {
                $img->resize($wmax, $hmax, Image::WIDTH);
            }
        }
        $img->save($folder.$name);

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
        $img->save($folder.'thumbnail'.DS.$name);
    }
}