<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/13/13
 */
namespace Core\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class TopMenuNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'top_menu_navigation';
    }
}