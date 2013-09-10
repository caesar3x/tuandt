<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/8/13
 */
namespace Core\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;
class Admin extends AbstractHelper
{
    protected $serviceLocator;

    public function __invoke()
    {
        $adminTable = $this->getServiceLocator()->get('AdminUserTable');
        $admins = $adminTable->fetchAll();
        foreach($admins as $item){
            echo $item->username.'<br/>';
        }
        die('=========');
    }
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}