<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/18/13
 */
namespace Core\Model;

use Zend\Db\Sql\Sql;

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
     * @param $model
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getRowsByModel($model,$condition)
    {
        if($model == null){
            return null;
        }
        $rowset = $this->tableGateway->select(array('deleted' => 0,'condition_id' => $condition,'model' => $model));
        if($rowset->count() <= 0){
            return null;
        }
        return $rowset;
    }
    public function getProductsByModel($model,$condition,$limit)
    {
        if(!$model || $model == null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->where(array('m.deleted' => 0,'m.condition_id' => (int)$condition,'m.model' => trim($model)));
        $select->limit($limit);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($result->count() <= 0){
            return null;
        }
        return $result;
    }
    public function getTopPriceProductsByModel($model,$condition,$limit = 3)
    {
        if(!$model || $model == null || !$condition || $condition == null){
            return null;
        }
        $adapter = $this->tableGateway->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->where(array('m.deleted' => 0,'m.condition_id' => (int)$condition,'m.model' => trim($model)));
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
        $select = $sql->select()->from(array('m' => $this->tableGateway->table));
        $select->where(array('m.deleted' => 0,'m.model' => $model));
        $select->order('m.price DESC')->limit(1);
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
}