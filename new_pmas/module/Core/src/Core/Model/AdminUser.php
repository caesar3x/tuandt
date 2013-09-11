<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/7/13
 */
namespace Core\Model;

class AdminUser
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $status;
    public $role;
    public $token;
    public $created_at;
    public $updated_at;
    public $note;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->password  = (isset($data['password'])) ? $data['password'] : null;
        $this->email  = (isset($data['email'])) ? $data['email'] : null;
        $this->status  = (isset($data['status'])) ? $data['status'] : '0';
        $this->role  = (isset($data['role'])) ? $data['role'] : 'editor';
        $this->token  = (isset($data['token'])) ? $data['token'] : md5($data['username']);
        $this->created_at  = (isset($data['created_at'])) ? $data['created_at'] : time();
        $this->updated_at  = (isset($data['updated_at'])) ? $data['updated_at'] : time();
        $this->note  = (isset($data['note'])) ? $data['note'] : null;
    }
}