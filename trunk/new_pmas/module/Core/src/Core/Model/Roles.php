<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

class Roles
{
    public $role_id;
    public $created_at;
    public $update_at;
    public $hidden;
    public $deleted;
    public $sort_order;
    public $parent_id;
    public $role;
    public $name;
    public $resource_ids;
    public function exchangeArray($data)
    {
        $this->role_id     = (isset($data['role_id'])) ? $data['role_id'] : 0;
        $this->created_at = (isset($data['created_at'])) ? $data['created_at'] : time();
        $this->update_at = (isset($data['update_at'])) ? $data['update_at'] : time();
        $this->hidden = (isset($data['hidden'])) ? $data['hidden'] : 0;
        $this->deleted = (isset($data['deleted'])) ? $data['deleted'] : 0;
        $this->sort_order = (isset($data['sort_order'])) ? $data['sort_order'] : 0;
        $this->parent_id = (isset($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->role = (isset($data['role'])) ? $data['role'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->resource_ids = (isset($data['resource_ids'])) ? $data['resource_ids'] : null;
    }
}