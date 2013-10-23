<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/12/13
 */
require_once "./init_autoloader.php";

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
     * @return \ZendTest\Soap\TestAsset\fulltests\ComplexTypeB
     */
    public function request($foo, $bar)
    {
        $b = new ComplexTypeB();
        $b->bar = $bar;
        $b->foo = $foo;
        return $b;
    }
}
if (isset($_GET['wsdl'])) {
    $server = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    echo $uri;die('---');
    $server = new \Zend\Soap\Server($uri);
}
$server->setClass('ZendTest\Soap\TestAsset\fulltests\Server2');
$server->handle();