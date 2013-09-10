<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/3/13
 */
namespace Core\Model;

use Core\Model\AbstractModel;

class TermTaxonomyTable extends AbstractModel
{
    public function getByTaxonomy($taxonomy = null)
    {
        if(null !== $taxonomy){
            $resultSet = $this->tableGateway->select(array('taxonomy' => $taxonomy));
            return $resultSet;
        }else{
            return $this->fetchAll();
        }
    }
    public function save(TermTaxonomy $entry)
    {
        $data = array(
            'term_taxonomy_id' => $entry->term_taxonomy_id,
            'term_id'  => $entry->term_id,
            'taxonomy'  => $entry->taxonomy,
            'description'  => $entry->description,
            'parent'  => $entry->parent,
            'thumnail'  => $entry->thumnail,
            'image'  => $entry->image,
            'count'  => $entry->count,
        );

        $id = (int)$entry->term_id;
        if ($id == 0) {
            if($this->tableGateway->insert($data)){
                return 'Thêm thành công.';
            }
        } else {
            if ($this->getEntry($id)) {
                if($this->tableGateway->update($data, array('term_id' => $id))){
                    return true;
                }
                return false;
            } else {
                throw new \Exception('Xảy ra lỗi.');
            }
        }
    }
    public function deleteEntry($id)
    {
        if($this->tableGateway->delete(array('id' => $id))){
            return true;
        }
        return false;
    }
}