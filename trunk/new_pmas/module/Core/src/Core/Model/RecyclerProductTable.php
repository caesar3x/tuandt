<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Debug\Debug;

class RecyclerProductTable extends AbstractModel
{
    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('product_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(RecyclerProduct $entry)
    {
        $data = (array) $entry;
        $id = $entry->product_id;
        if($id == 0){
            return $this->tableGateway->insert($data);
        }else{
            if($row = $this->getEntry($id)){
                return $this->tableGateway->update($data, array('product_id' => $id));
            }else{
                throw new \Exception('Data does not exist.');
            }
        }

    }

    /**
     * Delete by product id
     * @param $id
     * @return int
     */
    public function deleteEntry($id)
    {
        return $this->tableGateway->update(array('deleted' => 1),array('product_id' => $id));
    }
    /**
     * @param $brand_id
     * @return int
     */
    public function clearBrandId($brand_id)
    {
        return $this->tableGateway->update(array('brand_id' => 0),array('brand_id' => $brand_id));
    }

    /**
     * @param $recycler_id
     * @return int
     */
    public function clearRecyclerId($recycler_id)
    {
        return $this->tableGateway->update(array('recycler_id' => 0),array('recycler_id' => $recycler_id));
    }
    /**
     * Rollback
     * @param $id
     * @return int
     */
    public function rollbackDeleted($id)
    {
        return $this->tableGateway->update(array('deleted' => 0),array('product_id' => $id));
    }
    /**
     * Delete by condition id
     * @param $condition_id
     * @return int
     */
    public function deleteByCondition($condition_id)
    {
        return $this->tableGateway->update(array('condition_id' => 0),array('condition_id' => $condition_id));
    }
    /**
     * Delete by recycler id
     * @param $recycler_id
     * @return int
     */
    public function deleteByRecyclerId($recycler_id)
    {
        return $this->tableGateway->update(array('recycler_id' => 0),array('recycler_id' => $recycler_id));
    }
    /**
     * @param $condition_id
     * @return bool
     */
    public function checkHasRowContainConditionId($condition_id)
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'condition_id' => $condition_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }
    /**
     * @param $brand_id
     * @return bool
     */
    public function checkHasRowHasBrandId($brand_id)
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'brand_id' => $brand_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param $recycler_id
     * @return bool
     */
    public function checkHasRowHasRecyclerId($recycler_id)
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0,'recycler_id' => $recycler_id));
        if ($rowset->count() <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param null $recycler_id
     * @return array|null
     */
    public function getAllTempIdsProduct($recycler_id = null)
    {
        if($recycler_id == null){
            $rowset = $this->tableGateway->select(array('deleted' => 0));
        }else{
            $rowset = $this->tableGateway->select(array('deleted' => 0,'recycler_id' => $recycler_id));
        }
        if($rowset->count() <= 0){
            return null;
        }
        $data = array();
        foreach($rowset as $row){
            if($row->temp_id != null && $row->temp_id != 0){
                $data[] = $row->temp_id;
            }
        }
        return $data;
    }

    /**
     * @param $recycler_id
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getProductsByRecycler($recycler_id)
    {
        if($recycler_id == null){
            return $recycler_id;
        }
        $rowset = $this->tableGateway->select(array('deleted' => 0,'recycler_id' => $recycler_id));
        if($rowset->count() <= 0){
            return null;
        }
        return $rowset;
    }

    /**
     * Get select products by recycler id query
     * @param $recycler_id
     * @return \Zend\Db\Sql\Select
     */
    public function getProductsByRecyclerQuery($recycler_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('deleted',0);
        $where->equalTo('recycler_id',$recycler_id);
        $select->where($where);
        return $select;
    }
    public function getRowsMatching($model,$condition)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('deleted',0);
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        $select->where($where);
        $select->order("product_id DESC");
        $selectString = $sql->getSqlStringForSqlObject($select);
        $selectStringFinal = "SELECT * FROM ($selectString) AS tmp_table GROUP BY recycler_id";
        $result = $adapter->query($selectStringFinal, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    /**
     * @param $model
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getRowsByModel($model,$condition)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('deleted',0);
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    public function getRowsByModelQuery($model,$condition)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select  = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('deleted',0);
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        $select->where($where);
        return $select;
    }
    /**
     * @param $model
     * @param $condition
     * @param $limit
     * @return Resultset
     */
    public function getProductsByModel($model,$condition,$limit)
    {
        if(!$model || $model == null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('deleted',0);
        $where->equalTo('condition_id',$condition);
        $where->equalTo('model',$model);
        $where->greaterThan('recycler_id',1);
        $select->where($where);
        $select->limit($limit);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }

    /**
     * @param $model
     * @param $condition
     * @return null|string
     */
    public function getSSACurrency($model,$condition)
    {
        if(!$model || !$condition){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $select->where(array('deleted' => 0,'condition_id' => (int) $condition,'model' => trim($model),'recycler_id' => 1));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if(!$row){
            return null;
        }
        return (!empty($row->currency)) ? $row->currency : 'HKD';
    }
    /**
     * @param $model
     * @param $condition
     * @return float
     */
    public function getSSAPrice($model,$condition)
    {
        if(!$model || !$condition){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $select->where(array('deleted' => 0,'condition_id' => (int) $condition,'model' => trim($model),'recycler_id' => 1));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if(!$row){
            return null;
        }
        $exchange = $this->serviceLocator->get('viewhelpermanager')->get('exchange')->getCurrentExchangeOfCurrency($row->currency);
        $price = (float) $row->price;
        $ssa = $price/$exchange;
        return (float) $ssa;
        /*return (float)round($ssa,2,PHP_ROUND_HALF_UP);*/
    }
    public function getTopPriceProductsByModel($model,$condition,$limit = 3,$country = null)
    {
        if(!$model || $model == null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $where = new Where();
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        if(!empty($country)){

            $select->join(array('r' => 'recycler'),'m.recycler_id = r.recycler_id',array('country_id'));
            $where->equalTo('r.country_id',$country);
        }
        $where->equalTo('m.deleted',0);
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->greaterThan('m.recycler_id',1);
        $select->where($where);
        $select->order('m.price DESC');
        $select->limit($limit);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    /**
     * @param $model
     * @return $result
     */
    public function getHighestPrice($model)
    {
        if($model == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from($this->tableGateway->table);
        $where = new Where();
        $where->equalTo('deleted',0);
        $where->equalTo('model',$model);
        $select->where($where);
        $select->order('price DESC')->limit(1);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        $row = $result->current();
        if(empty($row)){
            return null;
        }
        return $row;
    }
    public function getProductsByCountry($country_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->equalTo('m.deleted',0);
        $where->equalTo('c.deleted',0);
        $where->equalTo('r.deleted',0);
        $where->greaterThan('r.recycler_id',1);
        /*$select->where(array('r.country_id' => $country_id,'m.deleted' => 0,'c.deleted' => 0,'r.deleted' => 0));*/
        $select->where($where);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    public function getProductsByCountryQuery($country_id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'))->columns(array('rid' => 'recycler_id'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->equalTo('m.deleted',0);
        $where->equalTo('c.deleted',0);
        $where->equalTo('r.deleted',0);
        $where->greaterThan('r.recycler_id',1);
        /*$select->where(array('r.country_id' => $country_id,'m.deleted' => 0,'c.deleted' => 0,'r.deleted' => 0));*/
        $select->where($where);
        $select->group('m.product_id');
        return $select;
    }
    public function getProductsByCountryAndModelAndCondition($country_id,$model,$condition)
    {
        if(!$country_id || $country_id == null || !$model || $model==null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'))->columns(array('rid' => 'recycler_id'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->equalTo('m.deleted',0);
        $where->equalTo('c.deleted',0);
        $where->equalTo('r.deleted',0);
        $where->greaterThan('r.recycler_id',1);
        /*$select->where(array('r.country_id' => $country_id,'m.condition_id' => $condition,'m.model' => $model,'m.deleted' => 0,'c.deleted' => 0,'r.deleted' => 0));*/
        $select->where($where);
        $select->order('product_id DESC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $selectStringFinal = "SELECT * FROM ($selectString) AS tmp_table GROUP BY recycler_id";
        $result = $adapter->query($selectStringFinal, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    public function getProductsByCountryAndModelAndConditionQuery($country_id,$model,$condition)
    {
        if(!$country_id || $country_id == null || !$model || $model==null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('r' => 'recycler'));
        $select->join(array('m' => $this->tableGateway->table),'m.recycler_id = r.recycler_id');
        $select->join(array('c' => 'country'),'r.country_id = c.country_id',array('country_id'));
        $where = new Where();
        $where->equalTo('r.country_id',$country_id);
        $where->equalTo('m.condition_id',$condition);
        $where->equalTo('m.model',$model);
        $where->equalTo('m.deleted',0);
        $where->equalTo('c.deleted',0);
        $where->equalTo('r.deleted',0);
        $where->greaterThan('r.recycler_id',1);
        /*$select->where(array('r.country_id' => $country_id,'m.condition_id' => $condition,'m.model' => $model,'m.deleted' => 0,'c.deleted' => 0,'r.deleted' => 0));*/
        $select->where($where);
        return $select;
    }
    /**
     * @param $ids
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getAvailabeRecyclerProducts($ids)
    {
        if($ids == null){
            $rowset = $this->tableGateway->select(array('deleted' => 0));
        }else{
            $rowset = $this->tableGateway->select(array('deleted' => 0,'product_id' => $ids));
        }
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }

    /**
     * Check temp id saved
     * @param $temp_id
     * @return bool
     */
    public function hasTempId($temp_id)
    {
        $rowset = $this->tableGateway->select(array('temp_id' => $temp_id));
        $row = $rowset->current();
        if(!$row){
            return false;
        }
        return true;
    }
    public function delete($id)
    {
        return $this->tableGateway->delete(array('product_id' => $id));
    }
}