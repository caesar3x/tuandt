<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/8/13
 */
namespace Core\View\Helper;
use Zend\Debug\Debug;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class Username extends AbstractHelper
{
    protected $serviceLocator;

    public function __invoke($local = 'frontend',$id = null)
    {
        if($local == 'backend'){
            if($id == null){
                $authService = $this->serviceLocator->get('auth_service');
                if ($authService->hasIdentity()) {
                    $identity = $authService->getIdentity();
                    $id = $identity->id;
                }
            }
            $adminTable = $this->serviceLocator->get('AdminUserTable');
            $admins = $adminTable->getEntry($id);
            if($admins != null){
               return $admins->username;
            }
            return null;
        }else{
            return;
        }
    }
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function test(){
        die('====dasdasdas');
    }
}