<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class CurrentUrl extends AbstractHelper
{
    protected $request;

    protected $serviceLocator;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function __invoke()
    {
        return $this->request->getUri()->normalize();
    }
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}