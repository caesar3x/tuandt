<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/3/13
 */
namespace Core\Model;

class TermTaxonomy
{
    public $term_taxonomy_id;

    public $term_id;

    public $taxonomy;

    public $description;

    public $parent;

    public $count;

    public $thumnail;

    public $image;

    public function exchangeArray($data)
    {
        $this->term_taxonomy_id    = (isset($data['term_taxonomy_id'])) ? $data['term_taxonomy_id'] : null;
        $this->term_id    = (isset($data['term_id'])) ? $data['term_id'] : null;
        $this->taxonomy    = (isset($data['taxonomy'])) ? $data['taxonomy'] : null;
        $this->description    = (isset($data['description'])) ? $data['description'] : null;
        $this->parent    = (isset($data['parent'])) ? $data['parent'] : 0;
        $this->thumnail    = (isset($data['thumnail'])) ? $data['thumnail'] : null;
        $this->image    = (isset($data['image'])) ? $data['image'] : null;
        $this->count    = (isset($data['count'])) ? $data['count'] : 0;
    }
}