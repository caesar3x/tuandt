<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/26/13
 */
namespace Core\Cache;

use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\ServiceManager\ServiceManager;
use Zend\Cache\StorageFactory;

class CacheSerializer
{
    protected $max_time_to_live = 86400;

    protected $min_time_to_live = 3600;

    protected $_maker;

    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public static function init2()
    {
        $maker = new \stdClass();
        $cache = new Filesystem();
        $serializer = new Serializer();
        $cache->addPlugin($serializer);
        $baseCapabilities = new Capabilities($cache,$maker);
        $capabilities = new Capabilities($cache,$maker,array(),$baseCapabilities);
        $capabilities->setMaxTtl($maker,86400);
        $capabilities->setMaxTtl($maker,3600);
        return $cache;
    }
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