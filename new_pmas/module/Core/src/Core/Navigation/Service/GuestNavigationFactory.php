<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/16/13
 */
namespace Core\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class GuestNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'guest_navigation';
    }
}