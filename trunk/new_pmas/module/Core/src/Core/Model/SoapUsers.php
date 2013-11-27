<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/22/13
 */
namespace Core\Model;

class SoapUsers
{
    public $id;
    public $username;
    public $password;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? (int) $data['id'] : 0;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->password  = (isset($data['password'])) ? $data['password'] : null;
    }
}