<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/9/13
 */
namespace Core\View\Helper;
use Zend\Debug\Debug;
use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;
class PageHeader extends AbstractHelper
{
    protected $request;

    protected $serviceLocator;

    public function __invoke()
    {
        Debug::dump($this->serviceLocator->get('Request'));die;
        echo $this->serviceLocator->get('Request')->getUri()->normalize();die;
        return $this->request->getUri()->normalize();
    }
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}