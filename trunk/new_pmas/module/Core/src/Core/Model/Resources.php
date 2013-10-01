<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

class Resources
{
    public $resource_id;
    public $hidden;
    public $deleted;
    public $sort_order;
    public $name;
    public $path;

    public function exchangeArray($data)
    {
        $this->resource_id     = (isset($data['resource_id'])) ? $data['resource_id'] : 0;
        $this->hidden     = (isset($data['hidden'])) ? $data['hidden'] : 0;
        $this->deleted     = (isset($data['deleted'])) ? $data['deleted'] : 0;
        $this->sort_order     = (isset($data['sort_order'])) ? $data['sort_order'] : 0;
        $this->name     = (isset($data['name'])) ? $data['name'] : null;
        $this->path     = (isset($data['path'])) ? $data['path'] : null;
    }
}