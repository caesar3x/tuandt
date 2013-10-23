<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/12/13
 */
require '../manual_loader.php';

class ComplexTypeB
{
    /**
     * @var string
     */
    public $bar;
    /**
     * @var string
     */
    public $foo;
}

class Server2
{
    /**
     * @param  string $foo
     * @param  string $bar
     * @return \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType
     */
    public function requestData($foo, $bar)
    {
        $b = new ComplexTypeB();
        $b->bar = $bar;
        $b->foo = $foo;
        return $b;
    }
}
$current = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$uri = $current."?wsdl";
if (isset($_GET['wsdl'])) {
    $server = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\AnyType());
    $server->setUri($current);
} else {
    /*$uri = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?wsdl";*/
    $server = new \Zend\Soap\Server($uri);
}
$server->setClass('Server2');
$server->handle();