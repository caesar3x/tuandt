<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Core\Model;

class Languages
{
    public $id;
    public $lang_code;
    public $lang_country;
    public $sort_order;
    public $file_path;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? (int)$data['id'] : 0;
        $this->lang_code     = (isset($data['lang_code'])) ? $data['lang_code'] : null;
        $this->lang_country     = (isset($data['lang_country'])) ? $data['lang_country'] : null;
        $this->sort_order     = (isset($data['sort_order'])) ? (int)$data['sort_order'] : null;
        $this->file_path     = (isset($data['file_path'])) ? $data['file_path'] : null;
    }
}