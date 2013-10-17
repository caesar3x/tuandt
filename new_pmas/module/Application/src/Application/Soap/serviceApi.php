<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/17/13
 */
namespace Application\Soap;

use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;

class serviceApi
{
    public function method1($inputParam) {
        return 'Hello World';
    }
}