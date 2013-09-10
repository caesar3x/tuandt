<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/2/13
 */
namespace Core\Model;

class Terms
{
    public $term_id;
    public $name;
    public $slug;
    public $term_group;

    public function exchangeArray($data)
    {
        $this->term_id     = (!empty($data['term_id'])) ? $data['term_id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->slug  = (!empty($data['slug'])) ? $data['slug'] : null;
        $this->term_group  = (!empty($data['term_group'])) ? $data['term_group'] : null;
    }
}