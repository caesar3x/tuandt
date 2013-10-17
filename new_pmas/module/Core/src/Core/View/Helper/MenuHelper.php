<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/2/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class MenuHelper extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function getRoleId()
    {
        $authService = $this->serviceLocator->get('auth_service');
        $userRoleId = 0;
        if($authService->hasIdentity()){
            $userRoleId = (int) $authService->getIdentity()->role;
        }
        return $userRoleId;
    }
    public function getRole()
    {
        $rolesTable = $this->serviceLocator->get('RolesTable');
        $userRole = 'guest';
        $userRoleId = $this->getRoleId();
        if($userRoleId != 0){
            if($rolesTable->getRoleNameById($userRoleId)){
                $userRole = $rolesTable->getRoleNameById($userRoleId);
            }
        }
        return $userRole;
    }
    public function getResourceGroups()
    {
        $rolesTable = $this->serviceLocator->get('RolesTable');
        $userRoleId = $this->getRoleId();
        $groups = array();
        if($userRoleId != 0){
            if($rolesTable->getResourcesById($userRoleId)){
                $resource_ids = $rolesTable->getResourcesById($userRoleId);
                if(!empty($resource_ids) && $resource_ids != ''){
                    $groups = unserialize($resource_ids);
                }
            }
        }
        return $groups;
    }
}