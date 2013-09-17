<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/2/13
 */
namespace Core\Model;

use Core\Model\AbstractModel;
use Zend\Db\Sql\Sql;
use Zend\Debug\Debug;

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
        if ($id == null) {
            return $this->tableGateway->insert($data);
        } else {
            if ($entry = $this->getEntry($id)) {
                //return $this->tableGateway->update($data, array('term_id' => $id));
                /*Debug::dump($this->tableGateway->update($data, array('term_id' => $id)));
                Debug::dump($entry);
                Debug::dump($data);
                Debug::dump(array_diff((array)$entry,$data));
                die;*/
                if(array_diff((array)$entry,$data) != null){
                    return $this->tableGateway->update($data, array('term_id' => $id));
                }else{
                    return 2;
                }
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
    public function getTaxonomyById($id,$taxonomy = 'category')
    {
        if($id == null){
            return;
        }
        $termTaxonomyTable = $this->serviceLocator->get('TermTaxonomyTable');
        $taxonomyData = $termTaxonomyTable->getByTaxonomy($taxonomy);
        if(!empty($taxonomyData)){
            $adapter = $this->tableGateway->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select()->from(array('m' => $this->tableGateway->table));
            $select->join(array('tt' => $termTaxonomyTable->tableGateway->table),'m.term_id = tt.term_id');
            $select->where(array('taxonomy' => $taxonomy,'m.term_id' => $id));
            $select->order('m.term_id ASC');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->current();
        }
        return null;
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('id' => $id));
    }
}