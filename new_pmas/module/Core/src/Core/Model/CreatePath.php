<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/24/13
 */
namespace Core\Model;

class CreatePath
{
    /**
     * @param $path
     * @return bool
     */
    public static function createPath($path)
    {
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = self::createPath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }
}