<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/13/13
 */
namespace Core\View\Helper;

use Zend\Debug\Debug;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class EventHelper extends AbstractHelper
{
    protected $request;

    protected $serviceLocator;

    protected $event;

    public function __construct(Request $request,MvcEvent $event)
    {
        $this->request = $request;
        $this->event = $event;
    }

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function fromRoute($param = null, $default = null)
    {
        if ($param === null)
        {
            return $this->event->getRouteMatch()->getParams();
        }
        $reponse = $this->event->getResponse();
        if($reponse->getStatusCode() != 404 && $reponse->getStatusCode() != 301 && $reponse->getStatusCode() != 302 && $reponse->getStatusCode() != 303 ){
            return $this->event->getRouteMatch()->getParam($param, $default);
        }
        return 0;
    }
}