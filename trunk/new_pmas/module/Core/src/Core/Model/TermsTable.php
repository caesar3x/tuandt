<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/2/13
 */
namespace Core\Model;

use Core\Model\AbstractModel;
use Zend\Db\Sql\Sql;

class TermsTable extends AbstractModel
{
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('term_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Terms $entry)
    {
        $data = array(
            'term_id' => $entry->term_id,
            'name'  => $entry->name,
            'slug'  => $entry->slug,
            'term_group'  => $entry->term_group
        );

        $id = (int)$entry->term_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('term_id' => $id));
            } else {
                throw new \Exception('Dữ liệu không tồn tại.');
            }
        }
    }

    /**
     * Get list taxonomy
     * @param null $taxonomy
     * @return null
     */
    public function getByTaxonomy($taxonomy = null)
    {
        $termTaxonomyTable = $this->serviceLocator->get('TermTaxonomyTable');
        $taxonomyData = $termTaxonomyTable->getByTaxonomy($taxonomy);
        if(!empty($taxonomyData)){
            $adapter = $this->tableGateway->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select()->from(array('m' => $this->tableGateway->table));
            $select->join(array('tt' => $termTaxonomyTable->tableGateway->table),'m.term_id = tt.term_id');
            $select->where(array('taxonomy' => $taxonomy));
            $select->order('m.term_id ASC');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        }
        return null;
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('id' => $id));
    }
}