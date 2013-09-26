<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/26/13
 */
namespace Core\Model;

use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer;

class CacheSerializer
{
    /**
     * @return Filesystem
     */
    public static function init()
    {
        $cache = new Filesystem();
        $serializer = new Serializer();
        $cache->addPlugin($serializer);
        return $cache;
    }
}