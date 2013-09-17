<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/3/13
 */
namespace Core\Model;

use Core\Model\AbstractModel;

class TermTaxonomyTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('term_taxonomy_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }

    /**
     * Get entry by term_id
     * @param $term_id
     * @return array|\ArrayObject|null
     */
    public function getByTermId($term_id)
    {
        $id  = (int) $term_id;
        $rowset = $this->tableGateway->select(array('term_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
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
            'thumbnail'  => $entry->thumbnail,
            'image'  => $entry->image,
            'count'  => $entry->count,
        );

        $id = (int)$entry->term_taxonomy_id;

        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('term_taxonomy_id' => $id));
            } else {
                throw new \Exception('Xảy ra lỗi.');
            }
        }
    }
    public function deleteEntry($id)
    {
        $this->tableGateway->delete(array('term_taxonomy_id' => $id));
    }
}