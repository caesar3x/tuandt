<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/28/13
 */
namespace Core\Model;

class Referer
{
    /**
     * @return bool
     */
    public static function getRefererUrl()
    {
        $ref = false;
        if(!empty($_SERVER['HTTP_REFERER'])){
            $ref = $_SERVER['HTTP_REFERER'];
        }
        if ( $ref && $ref !== $_SERVER['REQUEST_URI'] ){
            return $ref;
        }
        return false;
    }
}