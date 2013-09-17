<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Role extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __invoke()
    {
        $authService = $this->serviceLocator->get('auth_service');
        if ($authService->hasIdentity()) {
            $user = $authService->getIdentity();
            return $user->role;
        }
        return ;
    }
}