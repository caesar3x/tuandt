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
    public $first_name;
    public $last_name;
    public $password;
    public $email;
    public $status;
    public $role;
    public $token;
    public $created_at;
    public $updated_at;
    public $note;
    public $hidden;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : 0;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->first_name = (isset($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (isset($data['last_name'])) ? $data['last_name'] : null;
        $this->password  = (isset($data['password'])) ? $data['password'] : null;
        $this->email  = (isset($data['email'])) ? $data['email'] : null;
        $this->status  = (isset($data['status'])) ? $data['status'] : '0';
        $this->role  = (isset($data['role'])) ? $data['role'] : 'editor';
        $this->token  = (isset($data['token'])) ? $data['token'] : md5($data['first_name'].$data['last_name']);
        $this->created_at  = (isset($data['created_at'])) ? $data['created_at'] : time();
        $this->updated_at  = (isset($data['updated_at'])) ? $data['updated_at'] : time();
        $this->note  = (isset($data['note'])) ? $data['note'] : null;
        $this->hidden  = (isset($data['hidden'])) ? $data['hidden'] : 0;
    }
}