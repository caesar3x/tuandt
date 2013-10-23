<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Core\Soap;

use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;

class Authentication
{
    protected $_adapter;

    protected $_loggedin = false;

    protected $_username;

    protected $_password;

    protected $_session_time = 3600;

    public $_soap_users_table = 'soap_users';

    public function __construct()
    {
        $this->_adapter = $this->getAdapter();
    }
    public function getAdapter()
    {
        $adapter = new Adapter(array(
            'driver' => 'Mysqli',
            'database' => 'pmas',
            'username' => 'root',
            'password' => ''
        ));
        return $adapter;
    }
    public function getUserSession()
    {
        $session = new Container('SoapUser');
        return $session;
    }
    public function unsetUserSession()
    {
        $session = $this->getUserSession();
        $session->soap_username = null;
        $session->soap_password = null;
        $session->soap_logged = false;
    }
    public function isLoggedin()
    {
        $session = $this->getUserSession();
        return $session->soap_logged;
    }
}