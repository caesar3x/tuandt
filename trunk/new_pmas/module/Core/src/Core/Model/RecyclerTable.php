<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/17/13
 */
namespace Core\Model;

use Core\Cache\CacheSerializer;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Debug\Debug;

class RecyclerTable extends AbstractModel
{
    protected $cache_key = 'recyclers';

    public function getIdByName($name)
    {
        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        if(!$row){
            return null;
        }
        return $row->recycler_id;
    }

    public function fetchAll()
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('c' => 'country'),'m.country_id = c.country_id',array('country_name' => 'name'));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $rowset = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if ($rowset->count() <= 0) {
            return null;
        }
        return $rowset;
    }
    /**
     * Get data
     * @return mixed|null
     */
    public function get_all()
    {
        $cache = CacheSerializer::init();
        /*$cache->removeItem($this->cache_key);*/
        $data = $cache->getItem($this->cache_key);
        if($data === null){
            $all = $this->fetchAll();
            if(!empty($all)){
                foreach($all as $row){
                    $row_array = (array) $row;
                    unset($row_array['recycler_id']);
                    $data[$row->recycler_id] = $row_array;
                }
            }
            $cache->setItem($this->cache_key,$data);
        }
        return $data;
    }
    /**
     * @return Select
     */
    public function getRecyclerQuery()
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->join(array('c' => 'country'),'m.country_id = c.country_id',array('country_name' => 'name'));
        $select->order('recycler_id ACS');
        return $select;
    }
    public function getAvaiableRows()
    {
        $rowset = $this->tableGateway->select();
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('recycler_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Recycler $entry)
    {
        $data = (array) $entry;
        $id = (int)$entry->recycler_id;
        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEntry($id)) {
                return $this->tableGateway->update($data, array('recycler_id' => $id));
            } else {
                throw new \Exception('Data does not exist.');
            }
        }
    }
    public function deleteEntry($id)
    {
        return $this->tableGateway->delete(array('recycler_id' => $id));
    }

    /**
     * @param $id
     * @return bool
     */
    public function clearRecycler($id)
    {
        if($this->deleteEntry($id)){
            $success = true;
            $recyclerProductTable = $this->serviceLocator->get('RecyclerProductTable');
            if($recyclerProductTable->checkHasRowHasRecyclerId($id)){
                $success = $success && $recyclerProductTable->deleteByRecyclerId($id);
            }
            return $success;
        }else{
            return false;
        }
    }
    /**
     * Delete by country id
     * @param $country_id
     * @return int
     */
    public function clearCountry($country_id)
    {
        return $this->tableGateway->update(array('country_id' => 0),array('country_id' => $country_id));
    }
    /**
     * check if has record contain country id
     * @param $country_id
     * @return bool
     */
    public function checkAvailabelCountry($country_id)
    {
        $rowset = $this->tableGateway->select(array('country_id' => $country_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }
    /**
     * Get available recyclers
     * @param null $ids
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getAvailabeRecyclers($ids = null)
    {
        if($ids == null){
            $rowset = $this->tableGateway->select();
        }else{
            $rowset = $this->tableGateway->select(array('recycler_id' => $ids));
        }
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }

    /**
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getLastRecyclers()
    {
        $rowset = $this->tableGateway->select(function (Select $select){
            $select->order('recycler_id DESC')->limit(10);
        });
        if($rowset->count() <= 0){
            return null;
        }
        return $rowset;
    }

    /**
     * @param $country_id
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getRecyclersByCountry($country_id)
    {
        $country_id = (int) $country_id;
        $rowset = $this->tableGateway->select(array('country_id' => $country_id));
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }
}