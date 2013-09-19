<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/19/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class CreatePath extends AbstractHelper
{
    protected $request;

    protected $serviceLocator;

    public function __invoke($path = null)
    {
        if (null == $path) return true;
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = createPath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function implement($path)
    {
        return $this->__invoke($path);
    }
}