<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/11/13
 */
namespace Core\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class SecondaryNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'secondary_navigation';
    }
}