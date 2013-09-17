<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class SuperAdminNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'super_admin_navigation';
    }
}